<script setup lang="ts">
import { NavItem } from '@/types';
import { taskAddForm, tasks } from '@/routes';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { toUrl, urlIsActive } from '@/lib/utils';
import { Link } from '@inertiajs/vue3';
import { Separator } from '@/components/ui/separator';

const navItems: NavItem[] = [
    {
        title: 'Tasks',
        href: tasks(),
    },
    {
        title: 'Add Task',
        href: taskAddForm.url(),
    },
];
const currentPath =
    typeof window !== 'undefined' ? window.location.pathname : '';
</script>

<template>
    <div class="px-4 py-6">
        <Heading title="Tasks" description="Manage your tasks" />

        <div class="flex flex-col lg:flex-row lg:space-x-12">
            <aside class="w-full max-w-xl lg:w-64">
                <nav class="flex flex-col space-y-1 space-x-0">
                    <Button
                        v-for="item in navItems"
                        :key="toUrl(item.href)"
                        variant="ghost"
                        :class="[
                            'w-full justify-start',
                            { 'bg-muted': urlIsActive(item.href, currentPath) },
                        ]"
                        as-child
                    >
                        <Link :href="item.href">
                            <component :is="item.icon" class="h-4 w-4" />
                            {{ item.title }}
                        </Link>
                    </Button>
                </nav>
            </aside>

            <Separator class="my-6 lg:hidden" />

            <div class="flex-1 md:max-w-2xl">
                <section class="max-w-xl space-y-12">
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>

<style scoped></style>
