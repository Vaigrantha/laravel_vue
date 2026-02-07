<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { ref, onBeforeUnmount, onMounted, watch } from 'vue';
import ePub, { Rendition } from 'epubjs';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

// EPUB state
const viewer = ref<HTMLElement | null>(null);
const book = ref<any>(null);
const rendition = ref<Rendition | null>(null);
const isFullscreen = ref(false);

const loadEpub = async (event: Event) => {
    const file = (event.target as HTMLInputElement)?.files?.[0];
    if (!file || !viewer.value) return;

    const buffer = await file.arrayBuffer();

    book.value = ePub(buffer);

    rendition.value = book.value.renderTo(viewer.value, {
        width: '100%',
        height: '100%',
        flow: 'paginated',
        allowScriptedContent: true,
    });

    await rendition.value?.display();

    await book.value.ready;
    await book.value.locations.generate(1600);

    applyAppTheme();
    await loadToc();
    trackLocation();
};
const handleDrop = async (event: DragEvent) => {
    const file = event.dataTransfer?.files?.[0];
    if (!file || !file.name.endsWith('.epub')) return;

    await loadEpub({
        target: { files: [file] },
    } as unknown as Event);
};

const nextPage = () => rendition.value?.next();
const prevPage = () => rendition.value?.prev();

const toggleFullscreen = () => {
    if (!viewer.value) return;

    if (!document.fullscreenElement) {
        viewer.value.requestFullscreen();
        isFullscreen.value = true;
    } else {
        document.exitFullscreen();
        isFullscreen.value = false;
    }
};

const fontSize = ref(100);

/**
 * Sync EPUB with application theme (light / dark)
 */
const applyAppTheme = () => {
    if (!rendition.value) return;

    const systemTheme = getSystemTheme();
    const bgColor = systemTheme === 'dark' ? '#1e1e1e' : '#ffffff';
    const fgColor = systemTheme === 'dark' ? '#f0f0f0' : '#1a1a1a';

    rendition.value.themes.register('system-theme', {
        body: {
            background: bgColor,
            color: fgColor,
            lineHeight: '1.6',
            fontFamily: 'system-ui, sans-serif',
        },
        a: {
            color: systemTheme === 'dark' ? '#3ea6ff' : '#1a73e8',
        },
    });

    rendition.value.themes.select('system-theme');
};

const getSystemTheme = (): 'light' | 'dark' => {
    return window.matchMedia('(prefers-color-scheme: dark)').matches
        ? 'dark'
        : 'light';
};

/**
 * Font size control
 */
const setFontSize = (size: number) => {
    fontSize.value = size;
    rendition.value?.themes.fontSize(`${size}%`);
};

/* ---------------- TOC ---------------- */

interface TocItem {
    id: string;
    label: string;
    href: string;
    spineIndex?: number;
}

const activeHref = ref<string | null>(null);
const toc = ref<TocItem[]>([]);

watch(activeHref, (newHref) => {
    const el = document.querySelector(`[data-href="${newHref}"]`);
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
});

const normalizeHref = (href: string) => {
    return href
        .replace(/^(\.\.\/)+/, '') // remove ../
        .replace(/^\/+/, ''); // remove leading /
};

const loadToc = async (): Promise<void> => {
    if (!book.value) return;

    const navigation = await book.value.loaded.navigation;
    const spine = book.value.spine;

    toc.value = navigation.toc
        .map((item: any): TocItem | null => {
            const normalizedHref = normalizeHref(item.href);

            const spineIndex =
                spine.spineByHref[normalizedHref] ??
                spine.spineByHref[`Text/${normalizedHref}`];

            if (spineIndex === undefined) return null;

            return {
                id: item.id,
                label: item.label,
                href: normalizedHref,
                spineIndex,
            };
        })
        .filter((item: TocItem) => item !== null);
};

/**
 * Navigate to chapter
 */
const goToChapter = async (item: TocItem) => {
    if (!rendition.value) return;
    if (item.spineIndex == null) return;

    try {
        await rendition.value.display(item.spineIndex);
        activeHref.value = item.href;
    } catch (err) {
        console.warn('Navigation failed:', item);
    }
};

/**
 * Track current chapter (optional highlight)
 */
const trackLocation = () => {
    rendition.value?.on('relocated', (location: any) => {
        const href = location?.start?.href;
        activeHref.value = href ? href.split('#')[0] : null;
    });
};
onMounted(() => {
    applyAppTheme();

    // Listen for system theme changes
    window
        .matchMedia('(prefers-color-scheme: dark)')
        .addEventListener('change', applyAppTheme);
});

onBeforeUnmount(() => {
    book.value?.destroy();
    window
        .matchMedia('(prefers-color-scheme: dark)')
        .removeEventListener('change', applyAppTheme);
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="grid auto-rows-fr gap-4 md:grid-cols-3">
                <!-- <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <PlaceholderPattern />
                </div> -->
                <div
                    class="relative flex aspect-video items-center justify-center rounded-xl border border-dashed border-sidebar-border/70 dark:border-sidebar-border"
                    @dragover.prevent
                    @drop.prevent="handleDrop"
                >
                    <label
                        class="flex cursor-pointer flex-col items-center gap-2 text-center text-sm text-gray-500"
                    >
                        <input
                            type="file"
                            accept=".epub"
                            class="hidden"
                            @change="loadEpub"
                        />

                        <span class="text-lg">📚</span>
                        <span>Drag & drop EPUB here</span>
                        <span class="text-xs">or click to select</span>

                        <button
                            type="button"
                            class="mt-1 rounded bg-gray-200 px-3 py-1 text-xs dark:bg-gray-700"
                        >
                            Browse EPUB
                        </button>
                    </label>
                </div>
                <!-- <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <PlaceholderPattern />
                </div> -->
                <div
                    class="relative flex aspect-video flex-col gap-4 rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                >
                    <h3 class="text-sm font-semibold">Reader Appearance</h3>

                    <!-- Theme -->
                    <div class="flex items-center justify-between text-xs">
                        <span>Theme</span>
                        <button
                            class="rounded bg-muted px-2 py-1 hover:bg-muted/80"
                            @click="applyAppTheme"
                        >
                            Sync App Theme
                        </button>
                    </div>

                    <!-- Font Size -->
                    <div class="flex flex-col gap-1 text-xs">
                        <div class="flex justify-between">
                            <span>Font Size</span>
                            <span>{{ fontSize }}%</span>
                        </div>

                        <input
                            type="range"
                            min="80"
                            max="160"
                            :value="fontSize"
                            @input="
                                setFontSize(
                                    Number(
                                        ($event.target as HTMLInputElement)
                                            .value,
                                    ),
                                )
                            "
                            class="cursor-pointer"
                        />
                    </div>
                </div>
                <!-- <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <PlaceholderPattern />
                </div> -->
                <div
                    class="relative flex flex-col overflow-hidden rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                >
                    <h3 class="mb-2 text-sm font-semibold text-white">
                        Contents
                    </h3>

                    <ul
                        class="flex flex-1 flex-col gap-1 overflow-y-auto pr-1 text-xs"
                    >
                        <li
                            v-for="item in toc"
                            :key="item.id"
                            :data-href="item.href"
                            @click="goToChapter(item)"
                            :class="[
                                'cursor-pointer rounded px-2 py-1 transition-colors',
                                activeHref === item.href
                                    ? 'bg-blue-600 font-medium text-white'
                                    : 'text-gray-300 hover:bg-gray-700 hover:text-white',
                            ]"
                        >
                            {{ item.label }}
                        </li>
                    </ul>

                    <div
                        v-if="toc.length === 0"
                        class="mt-2 text-center text-xs text-gray-500"
                    >
                        No contents available
                    </div>
                </div>
            </div>
            <!-- EPUB READER -->
            <div
                class="flex flex-col gap-3 rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
            >
                <!-- Controls -->
                <div class="flex flex-wrap items-center gap-2">
                    <button
                        @click="prevPage"
                        class="rounded bg-gray-200 px-3 py-1 text-sm dark:bg-gray-700"
                    >
                        ◀ Prev
                    </button>

                    <button
                        @click="nextPage"
                        class="rounded bg-gray-200 px-3 py-1 text-sm dark:bg-gray-700"
                    >
                        Next ▶
                    </button>

                    <button
                        @click="toggleFullscreen"
                        class="rounded bg-gray-200 px-3 py-1 text-sm dark:bg-gray-700"
                    >
                        ⛶ Fullscreen
                    </button>
                </div>

                <!-- Reader -->
                <div
                    ref="viewer"
                    class="relative h-[70vh] w-full overflow-hidden rounded-lg border"
                />
            </div>
        </div>
    </AppLayout>
</template>
