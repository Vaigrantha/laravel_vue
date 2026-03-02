<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { useEpubReader } from '@/composables/useEpubReader';
import ReaderControls from '@/components/ReaderControls.vue';
import ReaderViewer from '@/components/ReaderViewer.vue';
import { onBeforeUnmount } from 'vue';

const { load, reset, viewer, isLoaded, isLoading } = useEpubReader();

const handleFile = async (event: Event) => {
    const file = (event.target as HTMLInputElement)?.files?.[0];
    if (!file) {
        reset();
        return;
    }
    await load(file);
};

onBeforeUnmount(() => {
    reset();
});
</script>
<template>
    <AppLayout>
        <div class="flex h-[calc(100vh-64px)] flex-col">
            <ReaderControls />

            <div class="relative flex-1 bg-gray-50 dark:bg-black">
                <div
                    v-if="!isLoaded && !isLoading"
                    class="flex h-full items-center justify-center"
                >
                    <label
                        class="cursor-pointer rounded-lg border-2 border-dashed p-12 hover:bg-gray-100"
                    >
                        <span class="text-gray-600">Click to upload EPUB</span>
                        <input
                            type="file"
                            class="hidden"
                            accept=".epub"
                            @change="handleFile"
                        />
                    </label>
                </div>

                <ReaderViewer v-show="isLoaded" />

                <div
                    v-if="isLoading"
                    class="absolute inset-0 z-50 flex items-center justify-center bg-white/80 dark:bg-black/80"
                >
                    <div class="flex flex-col items-center gap-2">
                        <div
                            class="h-8 w-8 animate-spin rounded-full border-4 border-blue-500 border-t-transparent"
                        ></div>
                        <p class="text-sm font-medium">Updating pages...</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
