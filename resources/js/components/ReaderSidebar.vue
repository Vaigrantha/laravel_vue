<script setup lang="ts">
import { useEpubReader } from '@/composables/useEpubReader';

const { toc, goTo, isLoaded } = useEpubReader();
</script>

<template>
    <div class="p-4">
        <h3 class="mb-4 font-bold">Table of Contents</h3>

        <div v-if="!isLoaded">No book loaded</div>

        <ul v-else>
            <li
                v-for="item in toc"
                :key="item.href"
                class="mb-2 cursor-pointer hover:underline"
                @click="goTo(item.href)"
            >
                {{ item.label }}
            </li>
        </ul>
    </div>
</template>
