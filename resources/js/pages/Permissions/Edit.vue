<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{
    permission: { id: number; name: string; roles: Array<{ id: number; name: string }> };
}>();
</script>

<template>
    <Head title="Edit Permission" />

    <AppLayout>
        <div class="space-y-4 p-6">
            <h1 class="text-2xl font-semibold">Edit Permission</h1>
            <div class="rounded border p-4">
                {{ permission.name }} ({{ permission.roles.length }} roles)
            </div>
            <Link class="underline" href="/admin/permissions">Back to permission list</Link>
        </div>
    </AppLayout>
</template>
