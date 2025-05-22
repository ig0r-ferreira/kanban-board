<template>
    <div class="flex-1 flex flex-col h-full overflow-hidden">
        <div class="flex-1 h-full overflow-x-auto">
            <div class="inline-flex h-full items-start p-5 space-x-4 overflow-hidden">
                <div v-for="(tasks, status) in columns" :key="status">
                    <div class="w-80 max-h-full bg-gray-200 flex flex-col rounded-md">
                        <div class="flex items-center justify-between p-3">
                            <h3 class="font-semibold text-md text-gray-700">
                                {{ status }}
                            </h3>
                        </div>
                        <div
                            v-if="tasks.length > 0"
                            class="overflow-y-auto max-h-[calc(100vh-200px)] pt-1 px-3 scroll-smooth scrollbar-thin"
                        >
                            <Task v-for="task in tasks" :key="task.id" :task="task"/>
                        </div>
                        <div 
                            v-if="status === 'Backlog'" 
                            class="flex justify-start p-3 rounded-b-md border-t border-gray-300"
                        >
                            <button
                                @click="showModal = true"
                                class="font-semibold text-sm text-gray-700"
                            > + Create
                            </button>
                            <CreateTaskModal
                                :show="showModal"
                                @close="showModal = false"
                                @created="handleTaskCreated"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref } from 'vue';
import axios from 'axios';
import Task from './Task.vue';
import CreateTaskModal from './CreateTaskModal.vue';

export default {
  data() {
    return {
      columns: [],
      showModal: ref(false)
    };
  },
  mounted() {
    this.loadKanban();
  },
  components: {
    Task,
    CreateTaskModal
  },
  methods: {
    loadKanban() {
      axios.get('/api/status')
        .then(response => {
            this.columns = Object.fromEntries(
                response.data.map(status => [status['name'], status['tasks']])
            );
        })
        .catch(error => {
            console.error('Erro ao carregar o Kanban:', error);
        });
    },
    handleTaskCreated(task) {
        console.log('Nova tarefa:', task);
    }
  }
}
</script>

<style>
/* Estilo da barra de rolagem */
.scrollbar-thin {
  scrollbar-width: thin;
  scrollbar-color: #cbd5e1 transparent; /* cinza + fundo transparente */
}

/* Para navegadores WebKit (Chrome, Edge, Safari) */
.scrollbar-thin::-webkit-scrollbar {
  width: 6px;
}

.scrollbar-thin::-webkit-scrollbar-thumb {
  background-color: #cbd5e1;
  border-radius: 4px;
}
</style>