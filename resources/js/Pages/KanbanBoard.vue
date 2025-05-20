<template>
    <div class="flex justify-center min-h-screen bg-gray-50">
        <div class="flex space-x-4 overflow-auto p-4">
            <div class="bg-gray-100 rounded-md p-4 w-64 flex-shrink-0" v-for="status in statuses" :key="status.id">
                <h2 class="font-bold text-center text-lg mb-4">{{ status.name }}</h2>
                <KanbanTask v-for="task in status.tasks" :key="task.id" :task="task"/>
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
