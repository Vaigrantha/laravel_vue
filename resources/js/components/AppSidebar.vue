<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    BookOpen,
    LayoutGrid,
    Shield,
    SlidersHorizontal,
    User,
    UserCog,
    Users,
} from 'lucide-vue-next';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { AppPageProps, NavItem } from '@/types';
import AppLogo from './AppLogo.vue';

const page = usePage<AppPageProps>();
const roleName = (page.props.auth.user?.role_name as string | undefined) ?? '';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
];

if (roleName === 'admin') {
    mainNavItems.push(
        { title: 'Admins', href: '/admin/admins', icon: UserCog },
        { title: 'Authors', href: '/admin/authors', icon: Users },
        { title: 'Users', href: '/admin/users', icon: User },
        { title: 'Books', href: '/admin/books', icon: BookOpen },
        { title: 'Roles', href: '/admin/roles', icon: Shield },
        { title: 'App Settings', href: '/admin/app-settings', icon: SlidersHorizontal },
    );
}

const footerNavItems: NavItem[] = [];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
