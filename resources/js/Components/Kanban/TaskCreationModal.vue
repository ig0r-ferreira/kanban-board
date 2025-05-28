<template>
  <div
    v-if="show"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 overflow-y-auto"
  >
    <div class="bg-white rounded-lg p-6 w-full max-w-lg shadow-lg">
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
          :min="new Date().toJSON().slice(0, 10)"
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
        />
        <StaticElement name="divider" tag="hr" />
        <ButtonElement
          secondary
          name="cancel"
          add-class="mt-2"
          :columns="6"
          full
          @click="$emit('close')"
        >
          Cancel
        </ButtonElement>
        <ButtonElement name="save" add-class="mt-2" :columns="6" full submits>
          Save
        </ButtonElement>
      </Vueform>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from "vue";
import axios from "axios";
import { usePage } from "@inertiajs/vue3";

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

watch(
  () => props.show,
  (newVal) => {
    if (newVal) {
      fetchUsers();
    }
  }
);
</script>
