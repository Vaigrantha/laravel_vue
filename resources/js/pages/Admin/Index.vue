<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{
    admins: Array<{ id: number; name: string; email: string; created_at: string }>;
}>();
</script>

<template>
    <Head title="Admin Management" />

    <AppLayout>
        <div class="space-y-4 p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Admin Management</h1>
                <Link class="rounded border px-3 py-2" href="/admin/admins/create">Create Admin</Link>
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
                        <tr v-for="admin in admins" :key="admin.id" class="border-b">
                            <td class="p-3">{{ admin.name }}</td>
                            <td class="p-3">{{ admin.email }}</td>
                            <td class="p-3">
                                <Link class="underline" :href="`/admin/admins/${admin.id}/edit`">Edit</Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
