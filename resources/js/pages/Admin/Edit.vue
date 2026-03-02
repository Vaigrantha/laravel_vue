<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{
    admin: { id: number; name: string; email: string };
}>();
</script>

<template>
    <Head title="Edit Admin" />

    <AppLayout>
        <div class="space-y-4 p-6">
            <h1 class="text-2xl font-semibold">Edit Admin</h1>
            <div class="rounded border p-4">{{ admin.name }} ({{ admin.email }})</div>
            <Link class="underline" href="/admin/admins">Back to admin list</Link>
        </div>
    </AppLayout>
</template>
