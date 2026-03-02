<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    targets: Array<{
        label: string;
        description: string;
        path: string;
    }>;
}>();
</script>

<template>
    <Head title="Select Sign-In Mode" />

    <div class="min-h-screen bg-slate-100 px-6 py-10 text-slate-900">
        <div class="mx-auto max-w-4xl space-y-8">
            <div class="space-y-2 text-center">
                <p class="text-sm uppercase tracking-wide text-slate-500">Generic CMS Access</p>
                <h1 class="text-3xl font-semibold">Sign in to Library CMS</h1>
                <p class="text-slate-600">Choose the profile that matches your access scope.</p>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <Link
                    v-for="target in targets"
                    :key="target.label"
                    :href="target.path"
                    class="rounded-xl border border-slate-300 bg-white p-5 shadow-sm transition hover:border-slate-500"
                >
                    <p class="text-lg font-semibold">{{ target.label }}</p>
                    <p class="mt-2 text-sm text-slate-600">{{ target.description }}</p>
                </Link>
            </div>

            <div class="text-center text-sm text-slate-500">
                Direct role login still works: <code>/login/admin</code>, <code>/login/author</code>, <code>/login/user</code>
            </div>
        </div>
    </div>
</template>
