<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

const form = useForm({
    title: '',
    author: '',
    description: '',
    epub: null as File | null,
});

const submit = () => {
    form.post('/admin/books', {
        forceFormData: true,
    });
};

const onFileChange = (event: Event) => {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null;
    form.epub = file;
    if (file) {
        void fillFromEpub(file);
    }
};

const fillFromEpub = async (file: File) => {
    const token =
        document
            .querySelector<HTMLMetaElement>('meta[name="csrf-token"]')
            ?.getAttribute('content') ?? '';

    const payload = new FormData();
    payload.append('epub', file);

    const response = await fetch('/admin/books/metadata-preview', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: payload,
    });

    if (!response.ok) {
        return;
    }

    const data = (await response.json()) as {
        title?: string | null;
        author?: string | null;
        description?: string | null;
    };

    if (data.title) form.title = data.title;
    if (data.author) form.author = data.author;
    if (data.description) form.description = data.description;
};
</script>

<template>
    <Head title="Create Book" />

    <AppLayout>
        <div class="max-w-3xl space-y-4 p-6">
            <h1 class="text-2xl font-semibold">Create Book</h1>

            <form class="space-y-4" @submit.prevent="submit">
                <div>
                    <label class="mb-1 block text-sm font-medium">Title</label>
                    <input v-model="form.title" class="w-full rounded border px-3 py-2" />
                    <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">{{ form.errors.title }}</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Author</label>
                    <input v-model="form.author" class="w-full rounded border px-3 py-2" />
                    <p v-if="form.errors.author" class="mt-1 text-sm text-red-600">{{ form.errors.author }}</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Description</label>
                    <textarea v-model="form.description" rows="4" class="w-full rounded border px-3 py-2" />
                    <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">EPUB File</label>
                    <input type="file" accept=".epub,application/epub+zip" @change="onFileChange" class="w-full rounded border px-3 py-2" />
                    <p v-if="form.errors.epub" class="mt-1 text-sm text-red-600">{{ form.errors.epub }}</p>
                </div>

                <div class="flex gap-3">
                    <button class="rounded border px-3 py-2" :disabled="form.processing">Save Book</button>
                    <Link class="rounded border px-3 py-2" href="/admin/books">Cancel</Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
