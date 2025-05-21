<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import KanbanBoard from '@/Components/Kanban/KanbanBoard.vue';
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <KanbanBoard/>
    </AuthenticatedLayout>
</template>
