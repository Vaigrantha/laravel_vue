<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{
    stats: {
        admins: number;
        authors: number;
        users: number;
        books: number;
    };
}>();
</script>

<template>
    <Head title="Admin Dashboard" />

    <AppLayout>
        <div class="space-y-6 p-6">
            <h1 class="text-2xl font-semibold">Admin Dashboard</h1>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-lg border bg-white p-4">Admins: {{ stats.admins }}</div>
                <div class="rounded-lg border bg-white p-4">Authors: {{ stats.authors }}</div>
                <div class="rounded-lg border bg-white p-4">Users: {{ stats.users }}</div>
                <div class="rounded-lg border bg-white p-4">Books: {{ stats.books }}</div>
            </div>

            <div class="flex flex-wrap gap-3">
                <Link class="rounded border px-3 py-2" href="/admin/admins">Manage Admins</Link>
                <Link class="rounded border px-3 py-2" href="/admin/authors">Manage Authors</Link>
                <Link class="rounded border px-3 py-2" href="/admin/users">Manage Users</Link>
                <Link class="rounded border px-3 py-2" href="/admin/books">Manage Books</Link>
                <Link class="rounded border px-3 py-2" href="/admin/roles">Manage Roles</Link>
                <Link class="rounded border px-3 py-2" href="/admin/permissions">Manage Permissions</Link>
            </div>
        </div>
    </AppLayout>
</template>
