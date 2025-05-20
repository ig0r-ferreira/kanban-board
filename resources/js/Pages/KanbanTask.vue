<template>
    <div class="bg-white rounded shadow p-2 mb-2">
        <div class="h-20">
            <span class="text-sm">{{ task.title }}</span>
        </div>
        <div>
            <div class="flex align-center justify-between">
                <div class="text-sm text-gray-500 font-bold">{{ task.key }}</div>
                <div class="text-sm">{{ task.priority }}</div>
            </div>
            <div class="flex align-center justify-between">
                <div class="text-sm text-gray-600" v-if="task.due_date">{{ formatDateToBr(task.due_date) }}</div>
                <div class="text-sm">{{ task.assignee.name }}</div>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    task: Object,
});
const formatDateToBr = ((date) => new Date(date).toLocaleString('pt-BR', { year: 'numeric', month: '2-digit', day: '2-digit' }));
</script>
