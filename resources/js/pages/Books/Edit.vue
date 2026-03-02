<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
    book: {
        id: number;
        title: string;
        author: string | null;
        description: string | null;
        file_path: string;
        mime_type: string | null;
        file_size: number | null;
        epub_data?: {
            metadata?: Record<string, string>;
            entries?: Array<{ path: string; size: number }>;
            documents?: Array<{ path: string; content: string }>;
        } | null;
    };
}>();

const form = useForm({
    title: props.book.title,
    author: props.book.author ?? '',
    description: props.book.description ?? '',
    epub: null as File | null,
});

const submit = () => {
    form
        .transform((data) => ({
            ...data,
            _method: 'put',
        }))
        .post(`/admin/books/${props.book.id}`, {
            forceFormData: true,
        });
};

const remove = () => {
    if (!confirm('Delete this book?')) return;
    form.delete(`/admin/books/${props.book.id}`);
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
    <Head title="Edit Book" />

    <AppLayout>
        <div class="max-w-4xl space-y-4 p-6">
            <h1 class="text-2xl font-semibold">Edit Book</h1>

            <form class="space-y-4" @submit.prevent="submit">
                <div>
                    <label class="mb-1 block text-sm font-medium">Title</label>
                    <input v-model="form.title" class="w-full rounded border px-3 py-2" />
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Author</label>
                    <input v-model="form.author" class="w-full rounded border px-3 py-2" />
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Description</label>
                    <textarea v-model="form.description" rows="4" class="w-full rounded border px-3 py-2" />
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Replace EPUB File</label>
                    <input type="file" accept=".epub,application/epub+zip" @change="onFileChange" class="w-full rounded border px-3 py-2" />
                </div>

                <div class="flex gap-3">
                    <button class="rounded border px-3 py-2" :disabled="form.processing">Update Book</button>
                    <button type="button" class="rounded border border-red-300 px-3 py-2 text-red-700" @click="remove">Delete</button>
                    <Link class="rounded border px-3 py-2" href="/admin/books">Back</Link>
                </div>
            </form>

            <div class="rounded border p-4 text-sm">
                <p><strong>File:</strong> {{ book.file_path }}</p>
                <p><strong>Mime:</strong> {{ book.mime_type || 'N/A' }}</p>
                <p><strong>Size:</strong> {{ book.file_size || 0 }} bytes</p>
                <p><strong>EPUB Entries:</strong> {{ book.epub_data?.entries?.length || 0 }}</p>
                <p><strong>EPUB Documents:</strong> {{ book.epub_data?.documents?.length || 0 }}</p>
                <p><strong>Metadata:</strong> {{ book.epub_data?.metadata || {} }}</p>
            </div>
        </div>
    </AppLayout>
</template>
