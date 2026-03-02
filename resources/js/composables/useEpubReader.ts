import { ref } from 'vue';
import ePub, { Book, Rendition } from 'epubjs';

/*
|--------------------------------------------------------------------------
| Shared State (Singleton)
|--------------------------------------------------------------------------
*/

const viewer = ref<HTMLElement | null>(null);
const book = ref<Book | null>(null);
const rendition = ref<Rendition | null>(null);
const toc = ref<any[]>([]);
const fontSize = ref(100);
const isFullscreen = ref(false);
const isLoading = ref(false);
const isLoaded = ref(false);
const isTocOpen = ref(false);

const currentPage = ref(0);
const totalPages = ref(0);

let resizeObserver: ResizeObserver | null = null;
let resizeTimeout: number | null = null;

/*
|--------------------------------------------------------------------------
| Composable
|--------------------------------------------------------------------------
*/

export function useEpubReader() {
    const setViewer = (el: HTMLElement | null) => {
        viewer.value = el;
    };

    /*
    |--------------------------------------------------------------------------
    | Load Book
    |--------------------------------------------------------------------------
    */
    // useEpubReader.ts

    const load = async (file: File) => {
        if (!viewer.value) return;

        isLoading.value = true;
        isLoaded.value = false;

        try {
            const buffer = await file.arrayBuffer();
            book.value = ePub(buffer);

            rendition.value = book.value.renderTo(viewer.value, {
                width: '100%',
                height: '100%',
                flow: 'paginated',
                manager: 'default',
            });

            // 1. Initial Display
            await rendition.value.display();

            // 2. Setup listeners and theme
            rendition.value.on('relocated', handleRelocated);
            initResizeObserver();
            registerKeyboard();
            applyTheme();

            const navigation = await book.value.loaded.navigation;
            toc.value = navigation.toc;

            // 3. Mark as loaded so UI shows the content
            isLoaded.value = true;
            isLoading.value = false; // Hide loader early so user sees text

            // 4. Calculate pagination in background
            await book.value.locations.generate(1600);
            totalPages.value = book.value.locations.length();
            updateCurrentPage();
        } catch (error) {
            console.error('Load error:', error);
            isLoading.value = false;
        }
    };

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    const handleRelocated = (location: any) => {
        // Check if location and start exist before accessing cfi
        if (!book.value || !location?.start?.cfi) return;

        const index = book.value.locations.locationFromCfi(location.start.cfi);

        if (typeof index === 'number' && index !== -1) {
            currentPage.value = index + 1;
        }
    };

    const updateCurrentPage = () => {
        // If locations haven't been generated yet, we can't determine the page index
        if (
            !rendition.value ||
            !book.value ||
            !book.value.locations?.length()
        ) {
            return;
        }

        const location = rendition.value.currentLocation() as any;

        // Safely check for the CFI
        const cfi = location?.start?.cfi;
        if (!cfi) return;

        const index = book.value.locations.locationFromCfi(cfi);
        if (typeof index === 'number' && index !== -1) {
            currentPage.value = index + 1;
        }
    };

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

    const next = () => {
        if (!isLoaded.value) return;
        rendition.value?.next();
    };

    const prev = () => {
        if (!isLoaded.value) return;
        rendition.value?.prev();
    };

    const goTo = async (target: any) => {
        if (!rendition.value) return;

        try {
            await rendition.value.display(target.href);
            updateCurrentPage();
        } catch (e) {
            console.warn('Navigation failed:', e);
        }
    };

    /*
    |--------------------------------------------------------------------------
    | Font
    |--------------------------------------------------------------------------
    */

    // ... inside useEpubReader.ts ...

    const setFont = async (size: number) => {
        const clamped = Math.min(200, Math.max(80, size));
        fontSize.value = clamped;

        if (!rendition.value || !book.value) return;

        // We only show the loader for font changes if you want to block UI
        isLoading.value = true;

        rendition.value.themes.fontSize(`${clamped}%`);

        // Reflow and recalculate
        setTimeout(async () => {
            if (book.value) {
                await book.value.locations.generate(1600);
                totalPages.value = book.value.locations.length();
                updateCurrentPage();
            }
            isLoading.value = false;
        }, 300);
    };

    /*
    |--------------------------------------------------------------------------
    | Theme
    |--------------------------------------------------------------------------
    */

    const isDark = () => document.documentElement.classList.contains('dark');

    const applyTheme = () => {
        if (!rendition.value) return;

        const dark = isDark();

        rendition.value.themes.register('app-theme', {
            body: {
                background: dark ? '#000000' : '#ffffff',
                color: dark ? '#ffffff' : '#000000',
            },
        });

        rendition.value.themes.select('app-theme');
    };

    /*
    |--------------------------------------------------------------------------
    | Resize Handling (Debounced)
    |--------------------------------------------------------------------------
    */

    const initResizeObserver = () => {
        if (!viewer.value) return;

        resizeObserver = new ResizeObserver((entries) => {
            const { width, height } = entries[0].contentRect;

            if (resizeTimeout) clearTimeout(resizeTimeout);

            resizeTimeout = window.setTimeout(() => {
                if (!isLoaded.value) return;

                rendition.value?.resize(width, height);
            }, 200);
        });

        resizeObserver.observe(viewer.value);
    };

    /*
    |--------------------------------------------------------------------------
    | Fullscreen
    |--------------------------------------------------------------------------
    */

    const toggleFullscreen = async () => {
        if (!viewer.value) return;

        if (!document.fullscreenElement) {
            await viewer.value.requestFullscreen();
        } else {
            await document.exitFullscreen();
        }
    };

    document.addEventListener('fullscreenchange', () => {
        isFullscreen.value = !!document.fullscreenElement;
    });

    /*
    |--------------------------------------------------------------------------
    | Keyboard Controls
    |--------------------------------------------------------------------------
    */

    const handleKeydown = (e: KeyboardEvent) => {
        if (!isLoaded.value) return;

        switch (e.key) {
            case 'ArrowRight':
                next();
                break;
            case 'ArrowLeft':
                prev();
                break;
            case 'f':
            case 'F':
                toggleFullscreen();
                break;
            case 'Escape':
                if (document.fullscreenElement) {
                    document.exitFullscreen();
                }
                break;
        }
    };

    const registerKeyboard = () => {
        window.addEventListener('keydown', handleKeydown);
    };

    const unregisterKeyboard = () => {
        window.removeEventListener('keydown', handleKeydown);
    };

    /*
    |--------------------------------------------------------------------------
    | Reset
    |--------------------------------------------------------------------------
    */

    const reset = () => {
        unregisterKeyboard();

        rendition.value?.off('relocated', handleRelocated);
        rendition.value?.destroy();
        book.value?.destroy?.();

        if (resizeObserver) {
            resizeObserver.disconnect();
            resizeObserver = null;
        }

        book.value = null;
        rendition.value = null;
        toc.value = [];
        currentPage.value = 0;
        totalPages.value = 0;
        isLoaded.value = false;

        if (viewer.value) viewer.value.innerHTML = '';
    };

    return {
        viewer,
        book,
        rendition,
        toc,
        fontSize,
        isFullscreen,
        isLoaded,
        isLoading,
        isTocOpen,
        currentPage,
        totalPages,
        setViewer,
        load,
        next,
        prev,
        goTo,
        setFont,
        toggleFullscreen,
        reset,
    };
}
