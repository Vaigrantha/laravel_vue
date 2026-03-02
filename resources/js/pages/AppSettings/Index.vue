<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{
    settings: Array<{
        id: number;
        key: string;
        value: string | null;
        type: string;
        description: string | null;
    }>;
}>();

const removeSetting = (id: number) => {
    if (!confirm('Delete this setting?')) return;
    router.delete(`/admin/app-settings/${id}`);
};
</script>

<template>
    <Head title="App Settings" />

    <AppLayout>
        <div class="space-y-4 p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">App Settings</h1>
                <Link class="rounded border px-3 py-2" href="/admin/app-settings/create">Add Setting</Link>
            </div>

            <div class="rounded-lg border">
                <table class="w-full text-left">
                    <thead class="border-b bg-slate-50">
                        <tr>
                            <th class="p-3">Key</th>
                            <th class="p-3">Value</th>
                            <th class="p-3">Type</th>
                            <th class="p-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="setting in settings" :key="setting.id" class="border-b">
                            <td class="p-3 font-medium">{{ setting.key }}</td>
                            <td class="max-w-[420px] truncate p-3">{{ setting.value || 'N/A' }}</td>
                            <td class="p-3">{{ setting.type }}</td>
                            <td class="space-x-3 p-3">
                                <Link class="underline" :href="`/admin/app-settings/${setting.id}/edit`">Edit</Link>
                                <button class="underline text-red-600" @click="removeSetting(setting.id)">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
