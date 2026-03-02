<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{
    userRecord: { id: number; name: string; email: string };
}>();
</script>

<template>
    <Head title="Edit User" />

    <AppLayout>
        <div class="space-y-4 p-6">
            <h1 class="text-2xl font-semibold">Edit User</h1>
            <div class="rounded border p-4">{{ userRecord.name }} ({{ userRecord.email }})</div>
            <Link class="underline" href="/admin/users">Back to user list</Link>
        </div>
    </AppLayout>
</template>
