<template>
  <div
    :class="[
      'bg-white shadow-md rounded-md p-4 mb-4 border-l-4',
      getBorderColorClass(task.priority),
    ]"
  >
    <h3
      class="font-bold text-sm overflow-hidden text-ellipsis whitespace-nowrap"
    >
      {{ task.title }}
    </h3>
    <p class="text-xs text-gray-500 font-bold flex items-center my-3">
      <span v-if="!isPastDate(task.due_date)" title="On time">
        🗓️ {{ task.due_date }}
      </span>
      <span v-else class="text-red-600" title="This task is overdue">
        🚨 {{ task.due_date }}
      </span>
    </p>
    <div
      class="text-xs text-gray-600 mt-2 overflow-hidden text-ellipsis whitespace-nowrap"
    >
      <span class="font-semibold">#{{ task.key }}</span> •
      <span
        :class="['uppercase', 'font-bold', getTextColorClass(task.priority)]"
      >
        {{ task.priority }}</span
      >
      •
      <span>{{ task.assignee.name }}</span>
    </div>
  </div>
</template>

<script setup>
import { isPastDate } from "@/Utils/date";

const props = defineProps({
  task: {
    type: Object,
    required: true,
  },
});

function getTextColorClass(priority) {
  switch (priority.toLowerCase()) {
    case "lowest":
    case "low":
      return "text-blue-600";
    case "medium":
      return "text-yellow-600";
    case "high":
    case "highest":
      return "text-red-600";
    default:
      return "text-gray-600";
  }
}

function getBorderColorClass(priority) {
  switch (priority.toLowerCase()) {
    case "lowest":
    case "low":
      return "border-blue-500";
    case "medium":
      return "border-yellow-500";
    case "high":
    case "highest":
      return "border-red-500";
    default:
      return "border-gray-500";
  }
}
</script>
