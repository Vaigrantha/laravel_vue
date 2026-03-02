<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{
    roles: Array<{ id: number; name: string; permissions: Array<{ id: number; name: string }> }>;
    permissions: Array<{ id: number; name: string; roles_count: number }>;
}>();
</script>

<template>
    <Head title="Role Management" />

    <AppLayout>
        <div class="space-y-6 p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Role Management</h1>
                <Link class="rounded border px-3 py-2" href="/admin/roles/create">Create Role</Link>
            </div>

            <div class="rounded-lg border">
                <table class="w-full text-left">
                    <thead class="border-b bg-slate-50">
                        <tr>
                            <th class="p-3">Role</th>
                            <th class="p-3">Permissions</th>
                            <th class="p-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="role in roles" :key="role.id" class="border-b">
                            <td class="p-3">{{ role.name }}</td>
                            <td class="p-3">{{ role.permissions.length }}</td>
                            <td class="p-3">
                                <Link class="underline" :href="`/admin/roles/${role.id}/edit`">Edit</Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold">Permissions (within Roles)</h2>
                </div>

                <div class="rounded-lg border">
                    <table class="w-full text-left">
                        <thead class="border-b bg-slate-50">
                            <tr>
                                <th class="p-3">Permission</th>
                                <th class="p-3">Roles Using It</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="permission in permissions" :key="permission.id" class="border-b">
                                <td class="p-3">{{ permission.name }}</td>
                                <td class="p-3">{{ permission.roles_count }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
