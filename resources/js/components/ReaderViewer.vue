<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useEpubReader } from '@/composables/useEpubReader';
import ReaderSidebar from '@/components/ReaderSidebar.vue';

const localViewer = ref<HTMLElement | null>(null);
const { setViewer, isTocOpen, isLoaded } = useEpubReader();

onMounted(() => {
    if (localViewer.value) {
        setViewer(localViewer.value);
    }
});
</script>

<template>
    <div class="relative flex h-full w-full overflow-hidden">
        <transition name="drawer">
            <div
                v-if="isTocOpen"
                class="absolute inset-y-0 left-0 z-50 w-80 border-r bg-white shadow-2xl dark:bg-zinc-900"
            >
                <ReaderSidebar />
            </div>
        </transition>

        <div
            v-if="isTocOpen"
            @click="isTocOpen = false"
            class="absolute inset-0 z-40 bg-black/40 backdrop-blur-sm"
        ></div>

        <div
            ref="localViewer"
            class="h-full w-full transition-opacity duration-500"
            :class="isLoaded ? 'opacity-100' : 'opacity-0'"
        ></div>
    </div>
</template>

<style scoped>
.drawer-enter-active,
.drawer-leave-active {
    transition: transform 0.3s ease;
}
.drawer-enter-from,
.drawer-leave-to {
    transform: translateX(-100%);
}
</style>
