<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { useEpubReader } from '@/composables/useEpubReader';
import ReaderControls from '@/components/ReaderControls.vue';
import ReaderSidebar from '@/components/ReaderSidebar.vue';
import ReaderViewer from '@/components/ReaderViewer.vue';
import { onBeforeUnmount } from 'vue';

const {
    load,
    next,
    prev,
    setFont,
    reset,
    toggleFullscreen,
    fontSize,
    viewer,
    isLoading,
} = useEpubReader();

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
const handleViewerReady = (el: HTMLElement) => {
    viewer.value = el;
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout>
        <div class="flex flex-col gap-6 p-6">
            <!-- Upload -->
            <div class="rounded-xl border p-6 text-center">
                <input type="file" accept=".epub" @change="handleFile" />
            </div>

            <ReaderControls
                :font-size="fontSize"
                @next="next"
                @prev="prev"
                @fullscreen="toggleFullscreen"
                @font="setFont"
            />

            <div class="grid h-[calc(100vh-200px)] grid-cols-[260px_1fr] gap-6">
                <ReaderViewer @ready="handleViewerReady" />

                <div
                    v-if="isLoading"
                    class="absolute inset-0 flex items-center justify-center bg-black/40"
                >
                    <div class="text-white">Loading book...</div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
