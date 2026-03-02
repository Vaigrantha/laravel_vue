<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{
    permissions: Array<{ id: number; name: string; roles_count: number }>;
}>();
</script>

<template>
    <Head title="Permission Management" />

    <AppLayout>
        <div class="space-y-4 p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Permission Management</h1>
                <Link class="rounded border px-3 py-2" href="/admin/permissions/create">Create Permission</Link>
            </div>

            <div class="rounded-lg border">
                <table class="w-full text-left">
                    <thead class="border-b bg-slate-50">
                        <tr>
                            <th class="p-3">Permission</th>
                            <th class="p-3">Roles Using It</th>
                            <th class="p-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="permission in permissions" :key="permission.id" class="border-b">
                            <td class="p-3">{{ permission.name }}</td>
                            <td class="p-3">{{ permission.roles_count }}</td>
                            <td class="p-3">
                                <Link class="underline" :href="`/admin/permissions/${permission.id}/edit`">Edit</Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
