<template>
  <div
    v-if="show"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
  >
    <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-lg">
      <h2 class="text-lg font-bold mb-4">Nova Tarefa</h2>

      <input
        v-model="form.title"
        type="text"
        placeholder="Título da tarefa"
        class="w-full border border-gray-300 rounded px-3 py-2 mb-4"
      />

      <div class="flex justify-end gap-2">
        <button @click="$emit('close')" class="text-gray-500 hover:underline">
          Cancelar
        </button>
        <button @click="createTask" class="bg-blue-600 text-white px-4 py-2 rounded">
          Criar
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue';

const props = defineProps({
  show: Boolean,
});

const emit = defineEmits(['close', 'created']);

const form = reactive({
  title: '',
});

function createTask() {
  if (!form.title.trim()) return;

  emit('created', { title: form.title });
  form.title = '';
  emit('close');
}
</script>
