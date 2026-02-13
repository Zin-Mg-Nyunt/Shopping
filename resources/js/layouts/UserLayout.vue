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
    Heart,
    LayoutDashboard,
    MapPin,
    Moon,
    Package,
    Search,
    Settings,
    ShoppingCart,
    Sun,
} from 'lucide-vue-next';
import { computed } from 'vue';

defineOptions({ name: 'UserLayout' });

const { resolvedAppearance, updateAppearance } = useAppearance();
const isDark = computed(() => resolvedAppearance.value === 'dark');

const toggleTheme = () => {
    updateAppearance(isDark.value ? 'light' : 'dark');
};

const page = usePage();
const auth = computed(() => page.props.auth);

// Cart count – replace with real prop when wired to backend
const cartCount = computed(() => 3);

const navItems = [
    {
        title: 'Dashboard',
        href: route('user.dashboard'),
        icon: LayoutDashboard,
    },
    { title: 'My Orders', href: route('user.orders'), icon: Package },
    { title: 'Wishlist', href: '#', icon: Heart },
    { title: 'Address Book', href: '#', icon: MapPin },
    { title: 'Account Settings', href: route('profile.edit'), icon: Settings },
];

function isActive(href) {
    const url = toUrl(href);
    if (!url || url === '#') return false;
    const path = url.startsWith('/') ? url : `/${url || ''}`;
    return typeof window !== 'undefined' && window.location.pathname === path;
}
</script>

<template>
    <div class="min-h-screen bg-background text-foreground">
        <!-- Top navigation: dark (background) -->
        <header
            class="sticky top-0 z-30 flex h-16 items-center gap-4 border-b border-border bg-background px-4 sm:px-6"
        >
            <Link
                :href="route('home')"
                class="flex shrink-0 items-center gap-2 [&_span]:text-foreground"
            >
                <AppLogo />
            </Link>

            <!-- Search bar -->
            <div class="relative hidden max-w-md flex-1 sm:block">
                <Search
                    class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                    aria-hidden="true"
                />
                <Input
                    type="search"
                    placeholder="Search products, orders..."
                    class="h-10 w-full border-input bg-muted/50 pl-10 text-foreground placeholder:text-muted-foreground focus-visible:border-primary focus-visible:ring-primary"
                />
            </div>

            <div class="flex flex-1 items-center justify-end gap-1 sm:gap-2">
                <!-- Cart with badge -->
                <Button
                    variant="ghost"
                    size="icon"
                    class="relative text-foreground hover:bg-accent hover:text-accent-foreground"
                    aria-label="Shopping cart"
                >
                    <ShoppingCart
                        class="size-5"
                        aria-hidden="true"
                    />
                    <span
                        v-if="cartCount > 0"
                        class="absolute -top-0.5 -right-0.5 flex size-5 items-center justify-center rounded-full bg-primary text-xs font-semibold text-primary-foreground"
                    >
                        {{ cartCount > 99 ? '99+' : cartCount }}
                    </span>
                </Button>

                <!-- Theme toggle -->
                <Button
                    variant="ghost"
                    size="icon"
                    class="text-foreground hover:bg-accent hover:text-accent-foreground"
                    aria-label="Toggle dark or light mode"
                    @click="toggleTheme"
                >
                    <Sun
                        v-if="isDark"
                        class="size-5"
                        aria-hidden="true"
                    />
                    <Moon
                        v-else
                        class="size-5"
                        aria-hidden="true"
                    />
                </Button>

                <!-- User profile dropdown -->
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <Button
                            variant="ghost"
                            class="relative h-10 w-auto gap-2 rounded-full p-1 pr-2 text-foreground hover:bg-accent hover:text-accent-foreground"
                        >
                            <Avatar class="size-8 border border-border">
                                <AvatarImage
                                    v-if="auth?.user?.avatar"
                                    :src="auth.user.avatar"
                                    :alt="auth.user.name"
                                />
                                <AvatarFallback
                                    class="bg-muted text-foreground"
                                >
                                    {{ getInitials(auth?.user?.name) }}
                                </AvatarFallback>
                            </Avatar>
                            <span
                                class="hidden max-w-24 truncate text-sm font-medium sm:inline-block"
                            >
                                {{ auth?.user?.name }}
                            </span>
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent
                        align="end"
                        class="w-56 border-border bg-popover text-popover-foreground"
                    >
                        <UserMenuContent
                            v-if="auth?.user"
                            :user="auth.user"
                        />
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>
        </header>

        <!-- Main: 2-column layout -->
        <div class="flex flex-col lg:flex-row">
            <!-- Left column: vertical menu card (1/4) -->
            <aside
                class="shrink-0 border-b border-border bg-background lg:w-1/4 lg:border-r lg:border-b-0 lg:border-border"
            >
                <nav
                    class="p-4 lg:sticky lg:top-16 lg:max-h-[calc(100vh-4rem)] lg:overflow-y-auto lg:p-6"
                    aria-label="Account menu"
                >
                    <div
                        class="rounded-xl border border-border bg-card p-2 shadow-sm"
                    >
                        <Link
                            v-for="item in navItems"
                            :key="item.title"
                            :href="item.href"
                            class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors"
                            :class="
                                isActive(item.href)
                                    ? 'bg-primary/15 text-primary'
                                    : 'text-foreground hover:bg-accent hover:text-accent-foreground'
                            "
                        >
                            <component
                                :is="item.icon"
                                class="size-5 shrink-0"
                                aria-hidden="true"
                            />
                            <span>{{ item.title }}</span>
                        </Link>
                    </div>
                </nav>
            </aside>

            <!-- Right column: dynamic content (3/4) -->
            <main class="min-w-0 flex-1 p-6 lg:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>
