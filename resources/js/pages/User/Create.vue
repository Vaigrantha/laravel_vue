<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
</script>

<template>
    <Head title="Create User" />

    <AppLayout>
        <div class="space-y-4 p-6">
            <h1 class="text-2xl font-semibold">Create User</h1>
            <p class="text-sm text-slate-600">This page is ready for user creation form wiring.</p>
            <Link class="underline" href="/admin/users">Back to user list</Link>
        </div>
    </AppLayout>
</template>
