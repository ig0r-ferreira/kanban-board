<template>
    <div
        v-if="show"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
    >
        <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-lg">
            <Vueform
                :float-placeholders="true"
                endpoint="api/task"
                method="post"
                @response="handleResponse"
            >
                <StaticElement
                    name="create_title"
                    content="Create task"
                    tag="h3"
                />
                <StaticElement
                    name="divider"
                    tag="hr"
                />
                <TextElement
                    name="title"
                    placeholder="Title"
                    rules="required|string|max:255"
                />
                <TextareaElement
                    name="description"
                    placeholder="Description"
                    rules="required"
                />
                <SelectElement
                    name="priority"
                    placeholder="Priority"
                    default="Medium"
                    rules="required"
                    :native="false"
                    :items="['Highest','High', 'Medium', 'Low', 'Lowest']"
                    :columns="6"
                />
                <DateElement
                    name="due_date"
                    floating="Due date"
                    placeholder="Select a due date..."
                    display-format="DD/MM/YYYY"
                    rules="required|date|after_or_equal:today"
                    :columns="6"
                />
                <SelectElement
                    name="reporter_id"
                    placeholder="Reporter"
                    rules='required'
                    :native="false"
                    :items="users"
                    :columns="6"
                />
                <SelectElement
                    name="assignee_id"
                    placeholder="Assignee"
                    rules='required'
                    :native="false"
                    :items="users"
                    :columns="6"
                />
                <StaticElement
                    name="divider"
                    tag="hr"
                />
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
                <ButtonElement
                    name="save"
                    add-class="mt-2"
                    :columns="6"
                    full
                    submits
                >
                    Save
                </ButtonElement>
            </Vueform>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    show: Boolean,
});

const emit = defineEmits(['close', 'created']);
const users = ref([]);

const fetchUsers = async () => {
    try {
        const { data } = await axios.get('/api/users');

        users.value = data.map(item => ({
            value: item.id,
            label: item.name,
        }));
    } catch (error) {
        console.error('Erro ao carregar categorias:', error);
    }
}

const handleResponse = (response, form$) => {
    form$.messageBag.clear();
    form$.reset();
    emit('close');
    emit('created', response);
}

onMounted(fetchUsers);
</script>