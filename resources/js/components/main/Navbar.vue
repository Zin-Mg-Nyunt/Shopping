<script setup>
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import { useAppearance } from '@/composables/useAppearance';
import { useFilter } from '@/composables/useFilter';
import { Link, usePage } from '@inertiajs/vue3';
import {
    LayoutDashboard,
    LogIn,
    Menu,
    Search,
    User,
    UserPlus,
} from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';

// Dark mode toggle
const { resolvedAppearance, updateAppearance } = useAppearance();
const isDark = computed(() => resolvedAppearance.value === 'dark');

const toggleDarkMode = () => {
    updateAppearance(isDark.value ? 'light' : 'dark');
};

// Cart badge count from local storage
const getLocalCartCount = () => Number(localStorage.getItem('cart_count')) || 0;
const cartItemsCount = ref(getLocalCartCount());

onMounted(() => {
    window.addEventListener('cart-count-updated', () => {
        cartItemsCount.value = getLocalCartCount();
    });
});
// for animation when cart count increases
const cartIconBounce = ref(false);
watch(cartItemsCount, (newCount, oldCount) => {
    if (newCount > oldCount) {
        cartIconBounce.value = true;
        setTimeout(() => {
            cartIconBounce.value = false;
        }, 500);
    }
});

// Mobile menu state
const isMobileMenuOpen = ref(false);
const isOpen = ref(false);
const user = computed(() => usePage().props.auth.user);
const dropdownContainer = ref(null);
const closeDropdown = (e) => {
    if (
        dropdownContainer.value &&
        !dropdownContainer.value.contains(e.target)
    ) {
        isOpen.value = false;
    }
};

onMounted(() => {
    window.addEventListener('click', closeDropdown);
});
onUnmounted(() => {
    window.removeEventListener('click', closeDropdown);
});

const { search, filterBy } = useFilter();
</script>

<template>
    <!-- Navbar -->
    <nav
        class="sticky top-0 z-50 w-full border-b bg-background/95 backdrop-blur supports-backdrop-filter:bg-background/60"
        role="navigation"
        aria-label="Main navigation"
    >
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <!-- Logo -->
                <Link
                    href="/"
                    class="flex items-center text-primary transition-opacity hover:opacity-80"
                    aria-label="Home"
                >
                    <AppLogoIcon class="size-30" />
                </Link>

                <!-- Desktop: Search Bar -->
                <div class="mx-8 hidden max-w-md flex-1 md:block">
                    <div class="relative">
                        <input
                            type="search"
                            placeholder="Search products..."
                            class="h-10 w-full rounded-lg border border-input bg-background pr-4 pl-10 text-sm focus:border-transparent focus:ring-2 focus:ring-primary focus:outline-none"
                            aria-label="Search products"
                            @input="
                                filterBy(
                                    '/products',
                                    'search',
                                    $event.target.value,
                                )
                            "
                            v-model="search"
                        />
                        <Search
                            class="absolute top-1/2 left-3 h-5 w-5 -translate-y-1/2 text-muted-foreground"
                            aria-hidden="true"
                        />
                    </div>
                </div>

                <!-- Desktop: Navigation Links -->
                <div class="hidden items-center gap-6 md:flex">
                    <Link
                        href="/"
                        class="text-sm font-medium text-foreground transition-colors hover:text-primary"
                        aria-label="Browse categories"
                    >
                        Home
                    </Link>
                    <Link
                        href="#categories"
                        class="text-sm font-medium text-foreground transition-colors hover:text-primary"
                        aria-label="Browse categories"
                    >
                        Categories
                    </Link>

                    <Link
                        :href="route('products')"
                        class="text-sm font-medium text-foreground transition-colors hover:text-primary"
                        aria-label="Products"
                    >
                        Products
                    </Link>
                    <Link
                        href="#contact-us"
                        class="text-sm font-medium text-foreground transition-colors hover:text-primary"
                        aria-label="Contact Us"
                    >
                        Contact Us
                    </Link>
                </div>

                <!-- Action Icons -->
                <div class="flex items-center gap-2 sm:gap-4">
                    <!-- Mobile: Hamburger Menu -->
                    <Sheet v-model:open="isMobileMenuOpen">
                        <SheetTrigger
                            as-child
                            class="md:hidden"
                        >
                            <button
                                class="rounded-lg p-2 transition-colors hover:bg-accent"
                                aria-label="Open menu"
                            >
                                <Menu class="h-6 w-6" />
                            </button>
                        </SheetTrigger>
                        <SheetContent
                            side="right"
                            class="w-[300px] sm:w-[400px]"
                        >
                            <SheetHeader>
                                <SheetTitle class="text-left">
                                    Menu
                                </SheetTitle>
                            </SheetHeader>
                            <div class="mt-6 space-y-6">
                                <!-- Mobile Search Bar -->
                                <div class="px-4">
                                    <label
                                        for="mobile-search"
                                        class="mb-2 block text-sm font-medium"
                                    >
                                        Search
                                    </label>
                                    <div class="relative">
                                        <input
                                            id="mobile-search"
                                            type="search"
                                            placeholder="Search products..."
                                            class="h-10 w-full rounded-md border border-input bg-background pr-4 pl-10 text-sm focus:border-transparent focus:ring-1 focus:ring-primary focus:outline-none"
                                            aria-label="Search products"
                                        />
                                        <Search
                                            class="absolute top-1/2 left-3 h-5 w-5 -translate-y-1/2 text-muted-foreground"
                                            aria-hidden="true"
                                        />
                                    </div>
                                </div>

                                <!-- Mobile Navigation Links -->
                                <nav class="space-y-2">
                                    <Link
                                        href="/"
                                        @click="isMobileMenuOpen = false"
                                        class="block rounded-lg px-4 py-3 text-base font-medium text-foreground transition-colors hover:bg-accent hover:text-primary"
                                        aria-label="Browse categories"
                                    >
                                        Home
                                    </Link>
                                    <Link
                                        href="#categories"
                                        @click="isMobileMenuOpen = false"
                                        class="block rounded-lg px-4 py-3 text-base font-medium text-foreground transition-colors hover:bg-accent hover:text-primary"
                                        aria-label="Browse categories"
                                    >
                                        Categories
                                    </Link>
                                    <Link
                                        :href="route('products')"
                                        @click="isMobileMenuOpen = false"
                                        class="block rounded-lg px-4 py-3 text-base font-medium text-foreground transition-colors hover:bg-accent hover:text-primary"
                                        aria-label="Products"
                                    >
                                        Products
                                    </Link>
                                    <Link
                                        href="#contact-us"
                                        @click="isMobileMenuOpen = false"
                                        class="block rounded-lg px-4 py-3 text-base font-medium text-foreground transition-colors hover:bg-accent hover:text-primary"
                                        aria-label="Contact Us"
                                    >
                                        Contact Us
                                    </Link>
                                </nav>
                            </div>
                        </SheetContent>
                    </Sheet>

                    <!-- Dark Mode Toggle -->
                    <button
                        @click="toggleDarkMode"
                        class="cursor-pointer rounded-lg p-2 transition-colors hover:text-primary"
                        :aria-label="
                            isDark
                                ? 'Switch to light mode'
                                : 'Switch to dark mode'
                        "
                    >
                        <svg
                            v-if="isDark"
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"
                            />
                        </svg>
                        <svg
                            v-else
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
                            />
                        </svg>
                    </button>

                    <!-- Cart -->
                    <button
                        class="relative cursor-pointer rounded-lg p-2 transition-colors hover:text-primary"
                        aria-label="View shopping cart"
                    >
                        <span
                            class="inline-block transition-transform"
                            :class="{
                                'animate-cart-bounce': cartIconBounce,
                            }"
                        >
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                                />
                            </svg>
                        </span>
                        <span
                            v-if="cartItemsCount > 0"
                            class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-xs font-bold text-primary-foreground"
                            :aria-label="`Items in cart: ${cartItemsCount}`"
                        >
                            {{ cartItemsCount }}
                        </span>
                    </button>

                    <!-- Account -->
                    <div
                        ref="dropdownContainer"
                        class="relative"
                    >
                        <button
                            type="button"
                            class="cursor-pointer rounded-lg p-2 transition-colors hover:text-primary"
                            aria-label="Account menu"
                            @click="isOpen = !isOpen"
                        >
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                />
                            </svg>
                        </button>
                        <div
                            v-show="isOpen"
                            class="absolute right-0 mt-2 w-48 rounded-lg border border-border bg-background py-2 shadow-lg"
                        >
                            <template v-if="user">
                                <Link
                                    as="button"
                                    href="/dashboard"
                                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-foreground transition-colors hover:text-primary"
                                >
                                    <LayoutDashboard class="size-4 shrink-0" />
                                    Dashboard
                                </Link>
                                <Link
                                    as="button"
                                    href="/profile"
                                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-foreground transition-colors hover:text-primary"
                                >
                                    <User class="size-4 shrink-0" />
                                    Profile
                                </Link>
                                <Link
                                    as="button"
                                    href="/logout"
                                    method="post"
                                    class="w-full cursor-pointer px-4 py-2 text-left text-sm font-medium text-destructive transition-colors hover:text-destructive/80"
                                    >Logout</Link
                                >
                            </template>
                            <template v-else>
                                <Link
                                    as="button"
                                    href="/login"
                                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-foreground transition-colors hover:text-primary"
                                >
                                    <LogIn class="size-4 shrink-0" />
                                    Login
                                </Link>
                                <Link
                                    as="button"
                                    href="/register"
                                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-foreground transition-colors hover:text-primary"
                                >
                                    <UserPlus class="size-4 shrink-0" />
                                    Register
                                </Link>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>
