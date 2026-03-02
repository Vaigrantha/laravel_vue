<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{
    author: { id: number; name: string; email: string };
}>();
</script>

<template>
    <Head title="Edit Author" />

    <AppLayout>
        <div class="space-y-4 p-6">
            <h1 class="text-2xl font-semibold">Edit Author</h1>
            <div class="rounded border p-4">{{ author.name }} ({{ author.email }})</div>
            <Link class="underline" href="/admin/authors">Back to author list</Link>
        </div>
    </AppLayout>
</template>
