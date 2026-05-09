<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import {
    Bell,
    LayoutDashboard,
    Menu,
    Moon,
    Package,
    SlidersHorizontal,
    ShoppingBag,
    Sun,
    Users,
    X,
} from 'lucide-vue-next';
import { computed, onUnmounted, ref, watch } from 'vue';
import { route } from 'ziggy-js';
import AppLogo from '@/components/AppLogo.vue';
import { useAppearance } from '@/composables/useAppearance';
import { logout } from '@/routes';
import { Toaster } from 'vue-sonner';
import 'vue-sonner/style.css';

const page = usePage();

const sidebarOpen = ref(false);
const { resolvedAppearance, updateAppearance } = useAppearance();
const isDark = computed(() => resolvedAppearance.value === 'dark');

const adminNav = [
    {
        label: 'Overview',
        href: route('admin.dashboard'),
        icon: LayoutDashboard,
        match: () => route().current('admin.dashboard'),
    },
    {
        label: 'Catalog Settings',
        href: route('admin.catalog.settings'),
        icon: SlidersHorizontal,
        match: () => route().current('admin.catalog.settings'),
    },
    {
        label: 'Products',
        href: route('admin.products'),
        icon: Package,
        match: () => route().current('admin.products'),
    },
    {
        label: 'Customers',
        href: route('admin.customers'),
        icon: Users,
        match: () => route().current('admin.customers'),
    },
    {
        label: 'Orders',
        href: route('admin.orders'),
        icon: ShoppingBag,
        match: () => route().current('admin.orders'),
    },
];

const adminUser = computed(() => page.props.auth?.user);
const adminInitials = computed(() => {
    const name = adminUser.value?.name || 'Admin';
    const parts = name.split(/\s+/).filter(Boolean);

    if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase();
    }

    return name.slice(0, 2).toUpperCase();
});

function openSidebar() {
    sidebarOpen.value = true;
}

function closeSidebar() {
    sidebarOpen.value = false;
}

function onNavInteract() {
    if (typeof window !== 'undefined' && window.innerWidth < 1024) {
        closeSidebar();
    }
}

watch(sidebarOpen, (open) => {
    if (typeof document === 'undefined') {
        return;
    }

    document.documentElement.classList.toggle('overflow-hidden', open);
});

onUnmounted(() => {
    if (typeof document !== 'undefined') {
        document.documentElement.classList.remove('overflow-hidden');
    }
});

function toggleTheme() {
    updateAppearance(isDark.value ? 'light' : 'dark');
}
</script>

<template>
    <div class="min-h-screen bg-muted text-foreground">
        <Toaster position="top-right" richColors />
        <!-- Mobile overlay -->
        <Transition
            enter-active-class="transition-opacity duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <button
                v-if="sidebarOpen"
                type="button"
                class="fixed inset-0 z-40 cursor-default bg-black/60 lg:hidden"
                aria-label="Close menu"
                @click="closeSidebar"
            />
        </Transition>

        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-50 flex w-[min(100%,20rem)] flex-col border-r border-border bg-card shadow-xl transition-transform duration-300 ease-out lg:w-64 lg:shadow-none"
            :class="
                sidebarOpen
                    ? 'translate-x-0'
                    : '-translate-x-full lg:translate-x-0'
            "
            aria-label="Admin navigation"
        >
            <div
                class="flex h-16 shrink-0 items-center justify-between gap-2 border-b border-border px-4"
            >
                <Link
                    :href="route('admin.dashboard')"
                    class="flex min-w-0 items-center gap-2"
                    @click="onNavInteract"
                >
                    <AppLogo />
                    <span
                        class="truncate text-sm font-bold tracking-tight text-foreground"
                        >Admin</span
                    >
                </Link>
                <button
                    type="button"
                    class="flex h-10 w-10 items-center justify-center rounded-xl text-muted-foreground transition hover:bg-muted hover:text-foreground lg:hidden"
                    aria-label="Close sidebar"
                    @click="closeSidebar"
                >
                    <X class="h-6 w-6" stroke-width="2" />
                </button>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto p-3">
                <template v-for="item in adminNav" :key="item.label">
                    <a
                        v-if="item.href === '#'"
                        href="#"
                        class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-muted-foreground transition hover:bg-muted hover:text-foreground"
                        @click.prevent="onNavInteract"
                    >
                        <component
                            :is="item.icon"
                            class="h-5 w-5 shrink-0 text-muted-foreground"
                            stroke-width="2"
                        />
                        {{ item.label }}
                    </a>
                    <Link
                        v-else
                        :href="item.href"
                        class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition"
                        :class="
                            item.match()
                                ? 'bg-primary/12 text-primary shadow-sm ring-1 ring-primary/25'
                                : 'text-muted-foreground hover:bg-muted hover:text-foreground'
                        "
                        @click="onNavInteract"
                    >
                        <component
                            :is="item.icon"
                            class="h-5 w-5 shrink-0"
                            :class="
                                item.match()
                                    ? 'text-[#FF6600]'
                                    : 'text-muted-foreground'
                            "
                            stroke-width="2"
                        />
                        <span
                            :class="
                                item.match()
                                    ? 'font-semibold text-[#FF6600]'
                                    : ''
                            "
                            >{{ item.label }}</span
                        >
                    </Link>
                </template>
            </nav>

            <div class="shrink-0 border-t border-border p-3">
                <div class="space-y-2">
                    <Link
                        :href="route('home')"
                        class="flex w-full items-center justify-center rounded-xl border border-border py-2.5 text-sm font-medium text-muted-foreground transition hover:border-primary/40 hover:text-primary"
                        @click="onNavInteract"
                    >
                        View storefront
                    </Link>
                    <Link
                        :href="logout()"
                        as="button"
                        class="flex w-full items-center justify-center rounded-xl border border-destructive/40 py-2.5 text-sm font-medium text-destructive transition hover:bg-destructive/10"
                        @click="onNavInteract"
                    >
                        Log out
                    </Link>
                </div>
            </div>
        </aside>

        <!-- Main column -->
        <div class="flex min-h-screen flex-col lg:pl-64">
            <header
                class="sticky top-0 z-30 flex h-16 shrink-0 items-center gap-3 border-b border-border bg-card/95 px-3 shadow-sm backdrop-blur-sm sm:gap-4 sm:px-4 lg:px-6 dark:bg-card/90"
            >
                <button
                    type="button"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-border bg-muted/50 text-foreground transition hover:border-primary/40 hover:bg-muted lg:hidden"
                    aria-label="Open menu"
                    :aria-expanded="sidebarOpen"
                    @click="openSidebar"
                >
                    <Menu class="h-5 w-5" stroke-width="2" />
                </button>

                <button
                    type="button"
                    class="relative ml-auto flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-border text-muted-foreground transition hover:border-primary/40 hover:text-[#FF6600]"
                    aria-label="Notifications"
                >
                    <Bell class="h-5 w-5" stroke-width="2" />
                    <span
                        class="absolute top-2 right-2 h-2 w-2 rounded-full bg-[#FF6600] ring-2 ring-card"
                    />
                </button>

                <button
                    type="button"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-border text-muted-foreground transition hover:border-primary/40 hover:text-[#FF6600]"
                    aria-label="Toggle dark mode"
                    @click="toggleTheme"
                >
                    <Sun v-if="isDark" class="h-5 w-5" stroke-width="2" />
                    <Moon v-else class="h-5 w-5" stroke-width="2" />
                </button>

                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary/15 text-sm font-bold text-[#FF6600] ring-1 ring-primary/30"
                    :title="adminUser?.name || 'Administrator'"
                >
                    {{ adminInitials }}
                </div>
            </header>

            <main class="flex-1 p-4 md:p-6 lg:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>
