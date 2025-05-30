<template>
  <Modal :show="show" max-width="lg">
    <div class="flex justify-end">
      <button
        v-if="!isEditing"
        class="py-2 px-4 rounded-md bg-sky-800 text-white text-sm transition hover:scale-110"
        @click="isEditing = true"
      >
        Editar
      </button>
    </div>
    <Vueform v-if="!isEditing">
      <StaticElement name="title" :content="task.title" tag="h3" />
      <StaticElement
        name="key"
        :content="`#${task.key}`"
        tag="p"
        add-class="font-bold"
      />
      <StaticElement name="divider" tag="hr" />
      <StaticElement
        name="description"
        label="Description"
        :content="task.description"
        tag="p"
        class="mb-5"
      />
      <StaticElement
        name="status"
        label="Status"
        :content="task.status.name"
        :columns="6"
      />
      <StaticElement
        name="priority"
        label="Priority"
        :content="task.priority"
        :columns="6"
      />
      <StaticElement
        name="due_date"
        label="Due date"
        :content="`${isTodayOrPast(task.due_date) ? '🚨' : ''} ${formatDate(
          task.due_date,
          'YYYY-MM-DD'
        )}`"
        :columns="6"
        :add-class="{
          content: { 'text-red-600 font-bold': isTodayOrPast(task.due_date) },
        }"
      />
      <StaticElement
        name="resolution_date"
        label="Resolution date"
        :content="formatUTC(task.resolution_date, 'YYYY-MM-DD HH:mm')"
        :columns="6"
      />
      <StaticElement
        name="reporter"
        label="Reporter"
        :content="task.reporter.name"
        :columns="6"
      />
      <StaticElement
        name="assignee"
        label="Assignee"
        :content="task.assignee.name"
        :columns="6"
      />
      <ButtonElement
        secondary
        name="close"
        add-class="mt-8 mx-auto w-full sm:w-1/2"
        full
        @click="closeModal"
      >
        Close
      </ButtonElement>
    </Vueform>
    <Vueform v-else ref="form$" :endpoint="false" @submit="onSubmit">
      <StaticElement name="edit_title" content="Edit task" tag="h3" />
      <StaticElement name="divider" tag="hr" />
      <TextElement
        name="title"
        label="Title"
        placeholder="What is it about?"
        :floating="false"
        rules="required|string|max:255"
      />
      <TextareaElement
        name="description"
        label="Description"
        :floating="false"
        placeholder="Add details, context or steps to reproduce..."
        rules="required"
        :rows="10"
      />
      <SelectElement
        name="status_id"
        placeholder="Status"
        rules="required"
        label-prop="name"
        value-prop="id"
        :native="false"
        :items="
          statuses.map((status) => ({
            ...status,
            disabled: status.name === task.status.name,
          }))
        "
        :disabled="
          statuses.length > 0 &&
          statuses[statuses.length - 1].name === task.status.name
        "
      />
      <SelectElement
        name="priority"
        placeholder="Priority"
        default="Medium"
        rules="required"
        :native="false"
        :items="['Highest', 'High', 'Medium', 'Low', 'Lowest']"
        :columns="6"
      />
      <DateElement
        name="due_date"
        floating="Due date"
        placeholder="Select a due date..."
        rules="required|date|after_or_equal:today"
        :min="dayjs().format('YYYY-MM-DD')"
        :columns="6"
      />
      <SelectElement
        name="reporter_id"
        placeholder="Reporter"
        rules="required"
        label-prop="name"
        value-prop="id"
        :native="false"
        :items="users"
        :search="true"
        :columns="6"
      />
      <SelectElement
        name="assignee_id"
        placeholder="Assignee"
        rules="required"
        label-prop="name"
        value-prop="id"
        :native="false"
        :items="users"
        :search="true"
        :columns="6"
      />
      <StaticElement name="divider" tag="hr" />
      <ButtonElement
        secondary
        name="cancel"
        add-class="mt-2"
        :columns="6"
        full
        @click="closeEditing"
      >
        Cancel
      </ButtonElement>
      <ButtonElement name="save" add-class="mt-2" :columns="6" full submits>
        Save
      </ButtonElement>
    </Vueform>
  </Modal>
</template>


<script setup>
import { ref, watch } from "vue";
import Modal from "@/Components/Modal.vue";
import axios from "axios";
import { isTodayOrPast, formatUTC, formatDate, dayjs } from "@/Utils/date";

const props = defineProps({
  show: Boolean,
  task: Object,
});
const emit = defineEmits(["close", "updated"]);
const form$ = ref(null);
const isEditing = ref(false);
const statuses = ref([]);
const users = ref([]);

async function fetchUsers(query, input) {
  try {
    const { data } = await axios.get("/api/users");
    users.value = data;
  } catch (error) {
    console.error("Error loading users:", error);
  }
}

async function fetchStatuses(query, input) {
  try {
    const { data } = await axios.get("/api/statuses");
    statuses.value = data;
  } catch (error) {
    console.error("Error loading statuses:", error);
  }
}

const closeEditing = () => {
  isEditing.value = false;
};

const closeModal = () => {
  emit("close");
};

const handleResponse = (response, form$) => {
  form$.messageBag.clear();
  form$.reset();
  closeEditing();
  emit("updated", response.data);
};

const onSubmit = async (form$) => {
  const data = form$.data;
  const finalStatus = statuses.value.at(-1);
  const isFinished = finalStatus && data.status_id == finalStatus.id;

  if (isFinished) {
    data["resolution_date"] = dayjs().utc().toISOString();
  }

  try {
    const response = await axios.patch(`/api/tasks/${props.task.id}`, data);
    handleResponse(response, form$);
  } catch (err) {
    console.error(err);
  }
};

const formatTask = (form) => {
  const { status, reporter, assignee, ...rest } = form;
  const formatedTask = {
    status_id: status.id,
    reporter_id: reporter.id,
    assignee_id: assignee.id,
    ...rest,
  };
  return formatedTask;
};

watch(
  () => [isEditing.value, form$.value, props.task],
  ([editing, form, task]) => {
    if (editing && form && task) {
      form.load(formatTask(task));
    }
  },
  { immediate: true }
);

watch(
  () => [isEditing.value, props.show],
  ([editing, newVal]) => {
    if (editing && newVal) {
      fetchUsers();
      fetchStatuses();
    }
  },
  { immediate: true }
);
</script>

<style>
label {
  font-weight: bold;
  font-size: 1.125rem;
}
input,
textarea {
  border: unset;
}
</style>
