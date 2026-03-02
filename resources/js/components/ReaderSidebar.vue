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
    <div class="flex h-full w-full overflow-hidden bg-gray-50 dark:bg-zinc-950">
        <transition name="sidebar">
            <aside
                v-if="isTocOpen"
                class="w-72 flex-shrink-0 overflow-y-auto border-r bg-white dark:bg-zinc-900"
            >
                <ReaderSidebar />
            </aside>
        </transition>

        <main class="relative flex min-w-0 flex-1 flex-col">
            <div
                ref="localViewer"
                class="flex-1 transition-opacity duration-300"
                :class="{ 'opacity-20': !isLoaded }"
            ></div>
        </main>
    </div>
</template>

<style scoped>
.sidebar-enter-active,
.sidebar-leave-active {
    transition:
        margin-left 0.3s ease,
        opacity 0.3s ease;
}
.sidebar-enter-from,
.sidebar-leave-to {
    margin-left: -18rem; /* Match w-72 */
    opacity: 0;
}
</style>
