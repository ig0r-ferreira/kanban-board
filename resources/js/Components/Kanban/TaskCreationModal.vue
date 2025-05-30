<template>
  <Modal :show="show" max-width="lg">
    <Vueform endpoint="api/tasks" method="post" @response="handleResponse">
      <StaticElement name="create_title" content="Create task" tag="h3" />
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
        :default="defaultReporter"
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
        @click="closeModal"
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
import { ref, watch, computed } from "vue";
import axios from "axios";
import { usePage } from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";
import { dayjs } from "@/Utils/date";

const { auth } = usePage().props;
const props = defineProps({
  show: Boolean,
});

const emit = defineEmits(["close", "created"]);
const users = ref([]);

const defaultReporter = computed(() => {
  return auth.user.id;
});

const fetchUsers = async () => {
  try {
    const { data } = await axios.get("/api/users");
    users.value = data;
  } catch (error) {
    console.error("Error loading users:", error);
  }
};

const handleResponse = (response, form$) => {
  form$.messageBag.clear();
  form$.reset();
  emit("close");
  emit("created");
};

const closeModal = () => {
  emit("close");
};

watch(
  () => props.show,
  (newVal) => {
    if (newVal) {
      fetchUsers();
    }
  },
  { immediate: true }
);
</script>
