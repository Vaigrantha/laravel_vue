<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { dashboard, login, register } from '@/routes';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);

const page = usePage();
const theme = computed(
    () => (page.props.branding as { theme?: string } | undefined)?.theme ?? 'default',
);

const themeClasses = computed(() => {
    if (theme.value === 'dark') {
        return {
            page: 'bg-slate-950 text-slate-100',
            headerButton: 'rounded border border-slate-700 bg-slate-900 px-4 py-2 text-sm hover:bg-slate-800',
            primaryButton: 'rounded bg-teal-500 px-5 py-2 text-sm font-medium text-white hover:bg-teal-400',
            secondaryButton: 'rounded border border-slate-700 bg-slate-900 px-5 py-2 text-sm font-medium hover:bg-slate-800',
            quickButton: 'rounded border border-slate-700 bg-slate-900 px-3 py-1.5 hover:bg-slate-800',
            mutedText: 'text-slate-400',
            sectionText: 'text-slate-300',
            imageCard: 'overflow-hidden rounded-xl border border-slate-800 bg-slate-900 shadow-sm',
        };
    }

    if (theme.value === 'ocean') {
        return {
            page: 'bg-cyan-50 text-cyan-950',
            headerButton: 'rounded border border-cyan-200 bg-white px-4 py-2 text-sm hover:bg-cyan-100',
            primaryButton: 'rounded bg-cyan-700 px-5 py-2 text-sm font-medium text-white hover:bg-cyan-600',
            secondaryButton: 'rounded border border-cyan-200 bg-white px-5 py-2 text-sm font-medium hover:bg-cyan-100',
            quickButton: 'rounded border border-cyan-200 bg-white px-3 py-1.5 hover:bg-cyan-100',
            mutedText: 'text-cyan-700',
            sectionText: 'text-cyan-800',
            imageCard: 'overflow-hidden rounded-xl border border-cyan-200 bg-white shadow-sm',
        };
    }

    return {
        page: 'bg-slate-50 text-slate-900',
        headerButton: 'rounded border border-slate-300 bg-white px-4 py-2 text-sm hover:bg-slate-100',
        primaryButton: 'rounded bg-slate-900 px-5 py-2 text-sm font-medium text-white hover:bg-slate-800',
        secondaryButton: 'rounded border border-slate-300 bg-white px-5 py-2 text-sm font-medium hover:bg-slate-100',
        quickButton: 'rounded border border-slate-300 bg-white px-3 py-1.5 hover:bg-slate-100',
        mutedText: 'text-slate-500',
        sectionText: 'text-slate-600',
        imageCard: 'overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm',
    };
});
</script>

<template>
    <Head title="Welcome" />

    <div class="min-h-screen" :class="themeClasses.page">
        <header
            class="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-6"
        >
            <h1 class="text-xl font-semibold">Library Management</h1>

            <nav class="flex items-center gap-3">
                <Link
                    v-if="$page.props.auth.user"
                    :href="dashboard()"
                    :class="themeClasses.headerButton"
                >
                    Dashboard
                </Link>
                <template v-else>
                    <Link
                        :href="login()"
                        :class="themeClasses.headerButton"
                    >
                        Log in
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="register()"
                        :class="themeClasses.primaryButton"
                    >
                        Register
                    </Link>
                </template>
            </nav>
        </header>

        <main
            class="mx-auto grid w-full max-w-6xl gap-8 px-6 pt-4 pb-10 lg:grid-cols-2 lg:items-center"
        >
            <section class="space-y-5">
                <p
                    class="text-sm font-medium tracking-wide uppercase"
                    :class="themeClasses.sectionText"
                >
                    Generic Library Portal
                </p>
                <h2 class="text-4xl leading-tight font-bold">
                    Discover, manage, and read books in one place.
                </h2>
                <p class="max-w-xl" :class="themeClasses.sectionText">
                    This library application provides role-based access for
                    administrators, authors, and users. Manage books,
                    permissions, and accounts from a single workspace.
                </p>

                <div class="flex flex-wrap gap-3" v-if="!$page.props.auth.user">
                    <Link
                        :href="login()"
                        :class="themeClasses.primaryButton"
                    >
                        Go to Login
                    </Link>
                    <Link
                        href="/login/cms"
                        :class="themeClasses.secondaryButton"
                    >
                        CMS Login
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="register()"
                        :class="themeClasses.secondaryButton"
                    >
                        Create Account
                    </Link>
                </div>

                <div
                    v-if="!$page.props.auth.user"
                    class="flex flex-wrap items-center gap-2 text-sm"
                >
                    <span :class="themeClasses.mutedText">Quick role login:</span>
                    <Link
                        href="/login/admin"
                        :class="themeClasses.quickButton"
                    >
                        Admin
                    </Link>
                    <Link
                        href="/login/author"
                        :class="themeClasses.quickButton"
                    >
                        Author
                    </Link>
                    <Link
                        href="/login/author"
                        :class="themeClasses.quickButton"
                    >
                        Role (Author)
                    </Link>
                    <Link
                        href="/login/user"
                        :class="themeClasses.quickButton"
                    >
                        User
                    </Link>
                </div>
            </section>

            <section :class="themeClasses.imageCard">
                <img
                    src="/images/library-generic.svg"
                    alt="Generic library illustration"
                    class="h-full w-full object-cover"
                />
            </section>
        </main>
    </div>
</template>
