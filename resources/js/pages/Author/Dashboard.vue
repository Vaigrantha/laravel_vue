<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{
    stats: {
        books: number;
        myRole: string;
    };
}>();
</script>

<template>
    <Head title="Author Dashboard" />

    <AppLayout>
        <div class="space-y-4 p-6">
            <h1 class="text-2xl font-semibold">Author Dashboard</h1>
            <div class="rounded border p-4">Total Books: {{ stats.books }}</div>
            <div class="flex gap-3">
                <Link class="rounded border px-3 py-2" href="/admin/books">Browse Books</Link>
                <Link class="rounded border px-3 py-2" href="/author">Refresh Dashboard</Link>
            </div>
        </div>
    </AppLayout>
</template>
