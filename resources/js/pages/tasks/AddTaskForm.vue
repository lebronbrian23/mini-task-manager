<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Label } from '@/components/ui/label';
import { Form, Head, usePage } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { saveTask, tasks } from '@/routes';
import { LoaderCircle } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Textarea } from '@/components/ui/textarea';
import TaskLayout from '@/layouts/tasks/TaskLayout.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { computed } from 'vue';

defineProps<{
    status?: string;
}>();

const page = usePage();
const userId = computed(() => (page.props as any)?.auth?.user?.id ?? null);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Add task',
        href: tasks().url,
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Add task" />
        <TaskLayout>
            <div class="flex flex-col space-y-6 mr-10 md:mr-0">
                <HeadingSmall
                    title="Add Task"
                    description="Enter task information below."
                />
                <div
                    v-if="status"
                    class="mb-4 text-center text-sm font-medium text-green-600">
                    {{ status }}
                </div>
                <Form
                    v-bind="saveTask.form()"
                    :reset-on-success="true"
                    v-slot="{ errors, processing }"
                    class="m-6 flex w-full max-w-xl flex-col gap-6 lg:w-2/3"
                >
                    <div class="grid gap-6">
                        <div class="grid gap-2">
                            <Label for="name">Name</Label>
                            <Input
                                type="text"
                                name="name"
                                id="name"
                                placeholder="Enter task name"
                                required
                                :tabindex="1"
                            />
                            <InputError :message="errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="description">Description</Label>
                            <Textarea
                                name="description"
                                id="description"
                                :cols="30"
                                :rows="10"
                                :tabindex="2"
                                placeholder="Enter task description"
                            ></Textarea>
                            <InputError :message="errors.description" />
                        </div>

                        <input type="hidden" name="user_id" :value="userId ?? ''" />
                        <Button type="submit">
                            <LoaderCircle
                                v-if="processing"
                                class="mr-2 h-4 w-4 animate-spin"
                            />
                            Add task
                        </Button>
                    </div>
                </Form>
            </div>
        </TaskLayout>
    </AppLayout>
</template>