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
} = useEpubReader();
const presets = [80, 90, 100, 110, 120, 130, 140, 150];
</script>

<template>
    <div class="flex items-center gap-3">
        <button :disabled="!isLoaded" @click="prev">Prev</button>
        <button :disabled="!isLoaded" @click="next">Next</button>
        <select
            :disabled="!isLoaded"
            v-model.number="fontSize"
            @change="setFont(fontSize)"
            class="rounded border px-2 py-1"
        >
            <option v-for="size in presets" :key="size" :value="size">
                {{ size }}%
            </option>
        </select>
        <span class="text-sm"> {{ currentPage }} / {{ totalPages }} </span>

        <button :disabled="!isLoaded" @click="toggleFullscreen">
            Fullscreen
        </button>
    </div>
</template>
