<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { LoaderCircle } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import TaskLayout from '@/layouts/tasks/TaskLayout.vue';
import { tasks, getTasks } from '@/routes';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';

const taskList = ref<any[]>([])
const loading = ref(false)
const error = ref<string | null>(null)

onMounted(async () => {
    loading.value = true
    error.value = null
    try {
        const response = await axios.get(getTasks().url)
        taskList.value = response.data ?? []
    } catch (e) {
        console.error(e)
        error.value = 'Failed to load tasks.'
        taskList.value = []
    } finally {
        loading.value = false
    }
})

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Tasks',
        href: tasks().url,
    },
]
</script>
<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Tasks" />
        <TaskLayout>
            <div class="flex flex-col space-y-6">
                <div v-if="loading" class="flex justify-center py-4">
                    <LoaderCircle class="mr-2 h-5 w-5 animate-spin" />
                </div>
                <div v-else>
                    <table class="border-collapse border border-gray-400 w-full">
                        <caption class="caption-top">List of tasks</caption>
                        <thead>
                            <tr class="bg-gray-700 text-white">
                                <th class="border border-b-gray-300 p-4">#</th>
                                <th class="border border-b-gray-300 p-4">Task</th>
                                <th class="border border-b-gray-300 p-4">Status</th>
                                <th class="border border-b-gray-300 p-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="error">
                                <td colspan="4" class="p-4 text-center text-red-700">{{ error }}</td>
                            </tr>
                            <tr v-else-if="taskList.length === 0">
                                <td colspan="4" class="p-4 text-center">No tasks found.</td>
                            </tr>
                            <tr v-else v-for="task in taskList" :key="task.id" class="dark:bg-gray-500">
                                <td class="border border-b-gray-300 p-4">{{ task.id }}</td>
                                <td class="border border-b-gray-300 p-4">{{ task.name }}</td>
                                <td class="border border-b-gray-300 p-4">{{ task.status }}</td>
                                <td class="border border-b-gray-300 p-4"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </TaskLayout>
    </AppLayout>
</template>