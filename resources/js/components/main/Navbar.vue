<script setup >
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet'; 
import { Link } from '@inertiajs/vue3';
import { Menu, Search } from 'lucide-vue-next';
import { useAppearance } from '@/composables/useAppearance';
import { computed, ref } from 'vue';

// Dark mode toggle
const { resolvedAppearance, updateAppearance } = useAppearance();
const isDark = computed(() => resolvedAppearance.value === 'dark');

const toggleDarkMode = () => {
    updateAppearance(isDark.value ? 'light' : 'dark');
};

// Cart badge count
const cartItemsCount = ref(3);

// Mobile menu state
const isMobileMenuOpen = ref(false);
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
                <div class="hidden flex-1 max-w-md mx-8 md:block">
                    <div class="relative">
                        <input
                            type="search"
                            placeholder="Search products..."
                            class="w-full h-10 pl-10 pr-4 rounded-lg border border-input bg-background text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                            aria-label="Search products"
                        />
                        <Search
                            class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-muted-foreground"
                            aria-hidden="true"
                        />
                    </div>
                </div>

                <!-- Desktop: Navigation Links -->
                <div class="hidden md:flex items-center gap-6">
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
                        href="#contact-us"
                        class="text-sm font-medium text-foreground transition-colors hover:text-primary"
                        aria-label="Contact Us"
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
                                class="p-2 rounded-lg transition-colors hover:bg-accent"
                                aria-label="Open menu"
                            >
                                <Menu class="h-6 w-6" />
                            </button>
                        </SheetTrigger>
                        <SheetContent side="right" class="w-[300px] sm:w-[400px]">
                            <SheetHeader>
                                <SheetTitle class="text-left">Menu</SheetTitle>
                            </SheetHeader>
                            <div class="mt-6 space-y-6">
                                <!-- Mobile Search Bar -->
                                <div class="px-4">
                                    <label
                                        for="mobile-search"
                                        class="block text-sm font-medium mb-2"
                                    >
                                        Search
                                    </label>
                                    <div class="relative">
                                        <input
                                            id="mobile-search"
                                            type="search"
                                            placeholder="Search products..."
                                            class="w-full h-10 pl-10 pr-4 rounded-md border border-input bg-background text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-transparent"
                                            aria-label="Search products"
                                        />
                                        <Search
                                            class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-muted-foreground"
                                            aria-hidden="true"
                                        />
                                    </div>
                                </div>

                                <!-- Mobile Navigation Links -->
                                <nav class="space-y-2">
                                    <Link
                                        href="/"
                                        @click="isMobileMenuOpen = false"
                                        class="block px-4 py-3 text-base font-medium text-foreground rounded-lg transition-colors hover:bg-accent hover:text-primary"
                                        aria-label="Browse categories"
                                    >
                                        Home
                                    </Link>
                                    <Link
                                        href="#categories"
                                        @click="isMobileMenuOpen = false"
                                        class="block px-4 py-3 text-base font-medium text-foreground rounded-lg transition-colors hover:bg-accent hover:text-primary"
                                        aria-label="Browse categories"
                                    >
                                        Categories
                                    </Link>
                                    <Link
                                        href="#contact-us"
                                        @click="isMobileMenuOpen = false"
                                        class="block px-4 py-3 text-base font-medium text-foreground rounded-lg transition-colors hover:bg-accent hover:text-primary"
                                        aria-label="Contact Us"
                                    >
                                        Products
                                    </Link>
                                    <Link
                                        href="#contact-us"
                                        @click="isMobileMenuOpen = false"
                                        class="block px-4 py-3 text-base font-medium text-foreground rounded-lg transition-colors hover:bg-accent hover:text-primary"
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
                        class="p-2 rounded-lg transition-colors hover:text-primary cursor-pointer"
                        :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
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
                        class="relative p-2 rounded-lg transition-colors hover:text-primary cursor-pointer"
                        aria-label="View shopping cart"
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
                        <span
                            v-if="cartItemsCount > 0"
                            class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-xs font-bold text-primary-foreground"
                            aria-label="Items in cart: {{ cartItemsCount }}"
                        >
                            {{ cartItemsCount }}
                        </span>
                    </button>

                    <!-- Account -->
                    <button
                        class="p-2 rounded-lg transition-colors hover:text-primary cursor-pointer"
                        aria-label="Account menu"
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
                </div>
            </div>
        </div>
    </nav>
</template>