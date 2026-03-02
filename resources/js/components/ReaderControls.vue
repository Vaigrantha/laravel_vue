<script setup lang="ts">
import { useEpubReader } from '@/composables/useEpubReader';

const {
    next,
    prev,
    setFont,
    toggleFullscreen,
    currentPage,
    totalPages,
    isLoaded,
    fontSize,
    isTocOpen, // Added this
} = useEpubReader();

const presets = [80, 90, 100, 110, 120, 130, 140, 150];
</script>

<template>
    <div
        class="flex items-center justify-between border-b bg-zinc-900 px-6 py-2 text-white"
    >
        <div class="flex items-center gap-6">
            <button @click="isTocOpen = !isTocOpen" class="hover:text-blue-400">
                {{ isTocOpen ? 'Close Menu' : 'Menu' }}
            </button>

            <div class="flex items-center gap-2">
                <button :disabled="!isLoaded" @click="prev">Prev</button>
                <span class="font-mono text-sm"
                    >{{ currentPage }} / {{ totalPages }}</span
                >
                <button :disabled="!isLoaded" @click="next">Next</button>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <select
                v-model.number="fontSize"
                @change="setFont(fontSize)"
                class="rounded bg-zinc-800 px-1"
            >
                <option v-for="size in presets" :key="size" :value="size">
                    {{ size }}%
                </option>
            </select>
            <button @click="toggleFullscreen">Fullscreen</button>
        </div>
    </div>
</template>
