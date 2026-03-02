<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

const form = useForm({
    key: '',
    value: '',
    type: 'string',
    description: '',
});

const submit = () => {
    form.post('/admin/app-settings');
};
</script>

<template>
    <Head title="Create App Setting" />

    <AppLayout>
        <div class="space-y-4 p-6 max-w-2xl">
            <h1 class="text-2xl font-semibold">Create App Setting</h1>

            <form class="space-y-4" @submit.prevent="submit">
                <div>
                    <label class="mb-1 block text-sm font-medium">Key</label>
                    <input v-model="form.key" class="w-full rounded border px-3 py-2" placeholder="app_name" />
                    <p v-if="form.errors.key" class="mt-1 text-sm text-red-600">{{ form.errors.key }}</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Value</label>
                    <textarea v-model="form.value" rows="4" class="w-full rounded border px-3 py-2" />
                    <p v-if="form.errors.value" class="mt-1 text-sm text-red-600">{{ form.errors.value }}</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Type</label>
                    <select v-model="form.type" class="w-full rounded border px-3 py-2">
                        <option value="string">string</option>
                        <option value="text">text</option>
                        <option value="json">json</option>
                        <option value="color">color</option>
                        <option value="number">number</option>
                        <option value="boolean">boolean</option>
                    </select>
                    <p v-if="form.errors.type" class="mt-1 text-sm text-red-600">{{ form.errors.type }}</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Description</label>
                    <input v-model="form.description" class="w-full rounded border px-3 py-2" placeholder="Shown on auth pages" />
                    <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                </div>

                <div class="flex gap-3">
                    <button :disabled="form.processing" class="rounded border px-3 py-2">Save</button>
                    <Link class="rounded border px-3 py-2" href="/admin/app-settings">Cancel</Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
