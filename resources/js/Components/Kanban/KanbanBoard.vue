<template>
    <div class="flex justify-center bg-gray-50">
        <div class="flex space-x-4 overflow-auto p-4">
            <div 
              class="bg-gray-200 rounded-md p-4 w-80 flex-shrink-0" 
              v-for="status in statuses" 
              :key="status.id"
            >
              <h2 class="font-bold text-center text-lg mb-4">{{ status.name }}</h2>
              <div class="overflow-y-auto max-h-[76vh] pr-2 scroll-smooth scrollbar-thin">
                <KanbanTask v-for="task in status.tasks" :key="task.id" :task="task"/>
              </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import KanbanTask from './KanbanTask.vue';
export default {
  data() {
    return {
      statuses: []
    };
  },
  mounted() {
    this.loadKanban();
  },
  components: {
    KanbanTask
  },
  methods: {
    loadKanban() {
      axios.get('/api/status')
        .then(response => {
          this.statuses = response.data;
        })
        .catch(error => {
          console.error('Erro ao carregar o Kanban:', error);
        });
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