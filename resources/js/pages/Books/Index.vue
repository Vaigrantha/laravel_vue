<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{
    books: Array<{
        id: number;
        title: string;
        author: string | null;
        file_path: string;
        file_size: number | null;
        epub_data?: {
            entries?: Array<{ path: string; size: number }>;
            metadata?: Record<string, string>;
        } | null;
    }>;
}>();
</script>

<template>
    <Head title="Book Management" />

    <AppLayout>
        <div class="space-y-4 p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Book Management</h1>
                <Link class="rounded border px-3 py-2" href="/admin/books/create">Create Book</Link>
            </div>

            <div class="rounded-lg border">
                <table class="w-full text-left">
                    <thead class="border-b bg-slate-50">
                        <tr>
                            <th class="p-3">Title</th>
                            <th class="p-3">Author</th>
                            <th class="p-3">EPUB Entries</th>
                            <th class="p-3">File Size</th>
                            <th class="p-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="book in books" :key="book.id" class="border-b">
                            <td class="p-3">{{ book.title }}</td>
                            <td class="p-3">{{ book.author || 'N/A' }}</td>
                            <td class="p-3">{{ book.epub_data?.entries?.length || 0 }}</td>
                            <td class="p-3">{{ book.file_size || 0 }} bytes</td>
                            <td class="p-3">
                                <Link class="underline" :href="`/admin/books/${book.id}/edit`">Edit</Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
