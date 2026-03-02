<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{
    permissions: Array<{ id: number; name: string }>;
}>();
</script>

<template>
    <Head title="Create Role" />

    <AppLayout>
        <div class="space-y-4 p-6">
            <h1 class="text-2xl font-semibold">Create Role</h1>
            <p class="text-sm text-slate-600">Available permissions: {{ permissions.length }}</p>
            <Link class="underline" href="/admin/roles">Back to role list</Link>
        </div>
    </AppLayout>
</template>
