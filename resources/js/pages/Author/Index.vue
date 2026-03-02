<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{
    authors: Array<{ id: number; name: string; email: string; created_at: string }>;
}>();
</script>

<template>
    <Head title="Author Management" />

    <AppLayout>
        <div class="space-y-4 p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Author Management</h1>
                <Link class="rounded border px-3 py-2" href="/admin/authors/create">Create Author</Link>
            </div>

            <div class="rounded-lg border">
                <table class="w-full text-left">
                    <thead class="border-b bg-slate-50">
                        <tr>
                            <th class="p-3">Name</th>
                            <th class="p-3">Email</th>
                            <th class="p-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="author in authors" :key="author.id" class="border-b">
                            <td class="p-3">{{ author.name }}</td>
                            <td class="p-3">{{ author.email }}</td>
                            <td class="p-3">
                                <Link class="underline" :href="`/admin/authors/${author.id}/edit`">Edit</Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
