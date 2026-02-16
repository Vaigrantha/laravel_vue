<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useEpubReader } from '@/composables/useEpubReader';

const localViewer = ref<HTMLElement | null>(null);
const { setViewer, isTocOpen } = useEpubReader();

onMounted(() => {
    if (localViewer.value) {
        setViewer(localViewer.value);
    }
});
</script>

<template>
    <button @click="isTocOpen = !isTocOpen">TOC</button>

    <transition name="slide">
        <div
            v-if="isTocOpen"
            class="absolute top-0 left-0 z-50 h-full w-72 overflow-y-auto bg-black"
        >
            <ReaderSidebar />
        </div>
    </transition>

    <div ref="localViewer" class="h-full w-full overflow-auto"></div>
</template>
