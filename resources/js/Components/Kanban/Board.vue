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
              <div v-for="task in tasks" :key="task.id">
                <Task
                  :task="task"
                  @click="openTask(task)"
                  class="cursor-pointer hover:bg-gray-300"
                />
              </div>
            </div>
            <div v-if="status === 'Backlog'">
              <button
                @click="showTaskCreationModal = true"
                class="w-full p-3 font-semibold text-sm text-gray-700 text-left rounded-b-md border-t border-gray-300 hover:bg-gray-300"
              >
                + Create
              </button>
              <CreateTaskModal
                :show="showTaskCreationModal"
                @close="showTaskCreationModal = false"
                @created="loadKanban"
              />
              <TaskDetailsModal
                :task="selectedTask"
                :show="showTaskDetailsModal"
                :statuses="getStatuses"
                @close="showTaskDetailsModal = false"
                @updated="loadKanban"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from "vue";
import axios from "axios";
import Task from "./Task.vue";
import CreateTaskModal from "./CreateTaskModal.vue";
import TaskDetailsModal from "./TaskDetailsModal.vue";

export default {
  data() {
    return {
      board: [],
      columns: [],
      showTaskCreationModal: ref(false),
      showTaskDetailsModal: ref(false),
      selectedTask: null,
    };
  },
  computed: {
    getStatuses() {
      return this.board.map(({ id, name }) => ({ id, name }));
    },
  },
  mounted() {
    this.loadKanban();
  },
  components: {
    Task,
    CreateTaskModal,
    TaskDetailsModal,
  },
  methods: {
    loadKanban() {
      axios
        .get("/api/statuses")
        .then((response) => {
          this.board = response.data;
          this.columns = Object.fromEntries(
            this.board.map((status) => [status["name"], status["tasks"]])
          );
        })
        .catch((error) => {
          console.error("Error loading kanban board:", error);
        });
    },
    openTask(task) {
      this.selectedTask = task;
      this.showTaskDetailsModal = true;
    },
  },
};
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
