<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
</script>

<template>
    <Head title="Create Admin" />

    <AppLayout>
        <div class="space-y-4 p-6">
            <h1 class="text-2xl font-semibold">Create Admin</h1>
            <p class="text-sm text-slate-600">This page is ready for admin creation form wiring.</p>
            <Link class="underline" href="/admin/admins">Back to admin list</Link>
        </div>
    </AppLayout>
</template>
