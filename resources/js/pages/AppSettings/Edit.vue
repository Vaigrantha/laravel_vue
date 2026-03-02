<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
    setting: {
        id: number;
        key: string;
        value: string | null;
        type: string;
        description: string | null;
    };
}>();

const form = useForm({
    key: props.setting.key,
    value: props.setting.value ?? '',
    type: props.setting.type,
    description: props.setting.description ?? '',
});

const submit = () => {
    form.put(`/admin/app-settings/${props.setting.id}`);
};
</script>

<template>
    <Head title="Edit App Setting" />

    <AppLayout>
        <div class="space-y-4 p-6 max-w-2xl">
            <h1 class="text-2xl font-semibold">Edit App Setting</h1>

            <form class="space-y-4" @submit.prevent="submit">
                <div>
                    <label class="mb-1 block text-sm font-medium">Key</label>
                    <input v-model="form.key" class="w-full rounded border px-3 py-2" />
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
                    <input v-model="form.description" class="w-full rounded border px-3 py-2" />
                    <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                </div>

                <div class="flex gap-3">
                    <button :disabled="form.processing" class="rounded border px-3 py-2">Update</button>
                    <Link class="rounded border px-3 py-2" href="/admin/app-settings">Cancel</Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
