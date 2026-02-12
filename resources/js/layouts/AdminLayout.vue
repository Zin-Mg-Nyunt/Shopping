<script setup>
import AppLogo from '@/components/AppLogo.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import UserMenuContent from '@/components/UserMenuContent.vue';
import { useAppearance } from '@/composables/useAppearance';
import { getInitials } from '@/composables/useInitials';
import { toUrl } from '@/lib/utils';
import { Link, usePage } from '@inertiajs/vue3';
import {
    Bell,
    FolderTree,
    LayoutDashboard,
    Menu,
    Moon,
    Package,
    Search,
    Settings,
    ShoppingCart,
    Sun,
    Tag,
    UserCog,
    Users,
    X,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { Toaster } from 'vue-sonner';

const { resolvedAppearance, updateAppearance } = useAppearance();
const isDark = computed(() => resolvedAppearance.value === 'dark');

const toggleDarkMode = () => {
    updateAppearance(isDark.value ? 'light' : 'dark');
};

const page = usePage();
const auth = computed(() => page.props.auth);
const sidebarOpen = ref(false);

const navItems = [
    {
        title: 'Dashboard',
        href: route('admin.dashboard'),
        icon: LayoutDashboard,
    },
    {
        title: 'Staff Management',
        href: route('admin.staff'),
        icon: UserCog,
    },
    { title: 'Products', href: route('admin.products'), icon: Package },
    { title: 'Orders', href: route('admin.orders'), icon: ShoppingCart },
    { title: 'Customers', href: route('admin.customers'), icon: Users },
    { title: 'Categories', href: route('admin.categories'), icon: FolderTree },
    { title: 'Coupons', href: '#', icon: Tag },
    { title: 'Settings', href: '#', icon: Settings },
];

function isActive(href) {
    const url = toUrl(href);
    return (
        typeof window !== 'undefined' &&
        window.location.pathname ===
            (url?.startsWith('/') ? url : `/${url || ''}`)
    );
}
</script>

<template>
    <Toaster
        position="top-right"
        :expand="true"
        richColors
    />
    <div class="min-h-screen bg-gray-50 dark:bg-gray-950">
        <!-- Sidebar: light mode = slate-50, dark mode = gray-900 -->
        <aside
            class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col border-r border-gray-200/80 bg-white transition-transform duration-200 ease-in-out lg:translate-x-0 dark:border-gray-700/50 dark:bg-gray-900 dark:text-white"
            :class="{ '-translate-x-full': !sidebarOpen }"
        >
            <!-- Logo -->
            <div
                class="flex h-16 items-center justify-between border-b border-gray-200/80 px-4 dark:border-gray-700/50"
            >
                <Link
                    :href="route('home')"
                    class="flex items-center gap-2 [&_span]:text-gray-900 dark:[&_span]:text-white"
                >
                    <AppLogo />
                </Link>
                <Button
                    variant="ghost"
                    size="icon"
                    class="text-gray-500 hover:bg-gray-100 hover:text-gray-900 lg:hidden dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
                    @click="sidebarOpen = false"
                >
                    <X class="h-5 w-5" />
                </Button>
            </div>

            <!-- Navigation -->
            <nav
                class="flex-1 space-y-0.5 overflow-y-auto bg-white px-3 py-4 dark:bg-gray-900"
            >
                <Link
                    v-for="item in navItems"
                    :key="item.title"
                    :href="item.href"
                    class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors"
                    :class="
                        isActive(item.href)
                            ? 'bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-white'
                            : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800/60 dark:hover:text-white'
                    "
                >
                    <component
                        :is="item.icon"
                        class="h-5 w-5 shrink-0"
                    />
                    <span>{{ item.title }}</span>
                </Link>
            </nav>
        </aside>

        <!-- Mobile overlay -->
        <div
            v-if="sidebarOpen"
            class="fixed inset-0 z-30 bg-black/50 lg:hidden"
            aria-hidden="true"
            @click="sidebarOpen = false"
        />

        <!-- Main content -->
        <div class="lg:pl-64">
            <!-- Top header -->
            <header
                class="sticky top-0 z-20 flex h-16 items-center justify-between gap-4 border-b border-gray-200/80 bg-white/95 px-4 shadow-sm backdrop-blur supports-backdrop-filter:bg-white/80 sm:px-6 dark:border-gray-800 dark:bg-gray-900/95 dark:supports-backdrop-filter:bg-gray-900/80"
            >
                <Button
                    variant="ghost"
                    size="icon"
                    class="lg:hidden"
                    @click="sidebarOpen = true"
                >
                    <Menu class="h-5 w-5" />
                </Button>

                <!-- Search -->
                <div class="relative hidden max-w-md flex-1 sm:block">
                    <Search
                        class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400"
                    />
                    <Input
                        type="search"
                        placeholder="Search products, orders..."
                        class="h-10 w-full pr-4 pl-10"
                    />
                </div>

                <div class="flex items-center gap-2">
                    <!-- Theme toggle -->
                    <Button
                        variant="ghost"
                        size="icon"
                        class="text-gray-600 dark:text-gray-400"
                        aria-label="Toggle theme"
                        @click="toggleDarkMode"
                    >
                        <Sun
                            v-if="isDark"
                            class="h-5 w-5"
                        />
                        <Moon
                            v-else
                            class="h-5 w-5"
                        />
                    </Button>

                    <!-- Notifications -->
                    <Button
                        variant="ghost"
                        size="icon"
                        class="relative"
                    >
                        <Bell
                            class="h-5 w-5 text-gray-600 dark:text-gray-400"
                        />
                        <span
                            class="absolute top-1.5 right-1.5 h-2 w-2 rounded-full bg-red-500"
                            aria-hidden="true"
                        />
                    </Button>

                    <!-- User dropdown -->
                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button
                                variant="ghost"
                                class="relative h-10 w-auto gap-2 rounded-full p-1 pr-2"
                            >
                                <Avatar class="h-8 w-8">
                                    <AvatarImage
                                        v-if="auth?.user?.avatar"
                                        :src="auth.user.avatar"
                                        :alt="auth.user.name"
                                    />
                                    <AvatarFallback
                                        class="bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200"
                                    >
                                        {{ getInitials(auth?.user?.name) }}
                                    </AvatarFallback>
                                </Avatar>
                                <span
                                    class="hidden text-sm font-medium text-gray-700 sm:inline-block dark:text-gray-300"
                                >
                                    {{ auth?.user?.name }}
                                </span>
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent
                            align="end"
                            class="w-56"
                        >
                            <UserMenuContent
                                v-if="auth?.user"
                                :user="auth.user"
                            />
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </header>

            <!-- Page content -->
            <main class="p-4 sm:p-6">
                <slot />
            </main>
        </div>
    </div>
</template>
