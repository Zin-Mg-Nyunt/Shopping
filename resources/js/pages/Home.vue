<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { useAppearance } from '@/composables/useAppearance';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { ShoppingBag, Image as ImageIcon, ArrowRight, Menu, X, Search } from 'lucide-vue-next';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';

// Dark mode toggle
const { resolvedAppearance, updateAppearance } = useAppearance();
const isDark = computed(() => resolvedAppearance.value === 'dark');

const toggleDarkMode = () => {
    updateAppearance(isDark.value ? 'light' : 'dark');
};

// Hero content data for full background layout
const heroContent = {
    headline: 'Discover Amazing Products',
    subHeadline: 'at Unbeatable Prices',
    description: 'Shop the latest trends and find everything you need in one place. Quality products, fast delivery, and exceptional service.',
    backgroundImage: 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1920&h=1080&fit=crop',
};

// Cart badge count
const cartItemsCount = ref(3);

// Mobile menu state
const isMobileMenuOpen = ref(false);

// Categories data
const categories = [
    { id: 1, name: 'Electronics', icon: '📱' },
    { id: 2, name: 'Fashion', icon: '👕' },
    { id: 3, name: 'Home & Garden', icon: '🏠' },
    { id: 4, name: 'Sports', icon: '⚽' },
    { id: 5, name: 'Books', icon: '📚' },
    { id: 6, name: 'Toys', icon: '🧸' },
    { id: 7, name: 'Beauty', icon: '💄' },
    { id: 8, name: 'Automotive', icon: '🚗' },
];

// Popular products data
const popularProducts = [
    {
        id: 1,
        name: 'Wireless Headphones',
        price: 129.99,
        originalPrice: 179.99,
        rating: 4.5,
        reviews: 234,
        image: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop',
    },
    {
        id: 2,
        name: 'Smart Watch Pro',
        price: 299.99,
        originalPrice: 349.99,
        rating: 4.8,
        reviews: 567,
        image: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&h=400&fit=crop',
    },
    {
        id: 3,
        name: 'Bluetooth Speaker',
        price: 79.99,
        originalPrice: 99.99,
        rating: 4.3,
        reviews: 189,
        image: 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=400&h=400&fit=crop',
    },
    {
        id: 4,
        name: 'Laptop Stand',
        price: 49.99,
        originalPrice: 69.99,
        rating: 4.6,
        reviews: 312,
        image: 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=400&h=400&fit=crop',
    },
];

// Latest products data
const latestProducts = [
    {
        id: 5,
        name: 'Gaming Mouse',
        price: 59.99,
        rating: 4.7,
        reviews: 145,
        image: 'https://images.unsplash.com/photo-1527814050087-3793815479db?w=400&h=400&fit=crop',
        isNew: true,
    },
    {
        id: 6,
        name: 'Mechanical Keyboard',
        price: 149.99,
        rating: 4.9,
        reviews: 278,
        image: 'https://images.unsplash.com/photo-1541140532154-b024d705b90a?w=400&h=400&fit=crop',
        isNew: true,
    },
    {
        id: 7,
        name: 'USB-C Hub',
        price: 39.99,
        rating: 4.4,
        reviews: 92,
        image: 'https://images.unsplash.com/photo-1587825140708-dfaf72ae4b04?w=400&h=400&fit=crop',
        isNew: true,
    },
    {
        id: 8,
        name: 'Wireless Charger',
        price: 34.99,
        rating: 4.2,
        reviews: 203,
        image: 'https://images.unsplash.com/photo-1580910051074-3eb694886505?w=400&h=400&fit=crop',
        isNew: true,
    },
];

// Newsletter form
const newsletterEmail = ref('');
const newsletterSubmitted = ref(false);

const handleNewsletterSubmit = (e) => {
    e.preventDefault();
    if (newsletterEmail.value) {
        newsletterSubmitted.value = true;
        newsletterEmail.value = '';
        setTimeout(() => {
            newsletterSubmitted.value = false;
        }, 3000);
    }
};
</script>

<template>
    <Head title="Home - Shop the Latest Trends" />

    <div class="min-h-screen bg-background text-foreground">
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

        <!-- Hero Section - Full Background Layout -->
        <section
            class="relative min-h-[600px] sm:min-h-[700px] lg:min-h-[800px] flex items-center overflow-hidden"
            aria-label="Hero section"
        >
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0 z-0">
                <img
                    :src="heroContent.backgroundImage"
                    alt="Hero background"
                    class="h-full w-full object-cover"
                />
                <!-- Dark Overlay for Text Contrast -->
                <div
                    class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/50 to-black/30 dark:from-black/80 dark:via-black/60 dark:to-black/40"
                />
            </div>

            <!-- Content Container -->
            <div class="container relative z-10 mx-auto px-4 sm:px-6 lg:px-8 py-20">
                <div class="max-w-3xl">
                    <!-- Headline -->
                    <h1
                        class="text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-bold mb-6 text-white leading-tight drop-shadow-lg"
                    >
                        {{ heroContent.headline }}
                        <span class="text-primary">{{ heroContent.subHeadline }}</span>
                    </h1>

                    <!-- Sub-headline / Description -->
                    <p
                        class="text-lg sm:text-xl lg:text-2xl text-white/90 mb-10 leading-relaxed drop-shadow-md max-w-2xl"
                    >
                        {{ heroContent.description }}
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <Button
                            size="lg"
                            class="text-base sm:text-lg px-8 py-6 group bg-primary hover:bg-primary/90 text-primary-foreground shadow-xl"
                            aria-label="Start shopping now"
                        >
                            <ShoppingBag class="mr-2 h-5 w-5" />
                            Shop Now
                            <ArrowRight
                                class="ml-2 h-5 w-5 transition-transform group-hover:translate-x-1"
                            />
                        </Button>
                        <Button
                            size="lg"
                            variant="outline"
                            class="text-base sm:text-lg px-8 py-6 group bg-white/10 backdrop-blur-sm border-white/20 text-white hover:bg-white/20"
                            aria-label="View product gallery"
                        >
                            <ImageIcon class="mr-2 h-5 w-5" />
                            View Gallery
                            <ArrowRight
                                class="ml-2 h-5 w-5 transition-transform group-hover:translate-x-1"
                            />
                        </Button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Category List -->
        <section
            id="categories"
            class="container mx-auto px-4 sm:px-6 lg:px-8 py-12"
            aria-label="Product categories"
        >
            <h2 class="text-2xl sm:text-3xl font-bold mb-6 text-center">
                Shop by Category
            </h2>
            <div
                class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide"
                style="scrollbar-width: none; -ms-overflow-style: none"
            >
                <div
                    v-for="category in categories"
                    :key="category.id"
                    class="shrink-0 group cursor-pointer"
                >
                    <div
                        class="flex flex-col items-center gap-3 p-6 rounded-2xl bg-card border border-border hover:border-primary transition-all hover:shadow-lg min-w-[120px]"
                    >
                        <div
                            class="text-4xl transition-transform group-hover:scale-110"
                            role="img"
                            :aria-label="category.name"
                        >
                            {{ category.icon }}
                        </div>
                        <span class="text-sm font-medium text-center">
                            {{ category.name }}
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Popular Products -->
        <section
            id="deals"
            class="container mx-auto px-4 sm:px-6 lg:px-8 py-12"
            aria-label="Popular products"
        >
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl sm:text-3xl font-bold">Popular Products</h2>
                <Button variant="outline" aria-label="View all popular products">
                    See All
                </Button>
            </div>
            <div
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6"
            >
                <div
                    v-for="product in popularProducts"
                    :key="product.id"
                    class="group relative bg-card rounded-xl border border-border overflow-hidden hover:shadow-lg transition-all"
                >
                    <div class="relative aspect-square overflow-hidden bg-muted">
                        <img
                            :src="product.image"
                            :alt="product.name"
                            class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-110"
                        />
                        <div
                            v-if="product.originalPrice"
                            class="absolute top-3 left-3"
                        >
                            <Badge
                                variant="destructive"
                                class="bg-primary text-primary-foreground"
                            >
                                {{
                                    Math.round(
                                        ((product.originalPrice -
                                            product.price) /
                                            product.originalPrice) *
                                            100,
                                    )
                                }}% OFF
                            </Badge>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold mb-2 line-clamp-2">
                            {{ product.name }}
                        </h3>
                        <div class="flex items-center gap-2 mb-2">
                            <div class="flex items-center">
                                <svg
                                    v-for="i in 5"
                                    :key="i"
                                    class="h-4 w-4"
                                    :class="
                                        i <= Math.floor(product.rating)
                                            ? 'text-yellow-400 fill-yellow-400'
                                            : 'text-muted-foreground'
                                    "
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                                    />
                                </svg>
                            </div>
                            <span class="text-sm text-muted-foreground">
                                ({{ product.reviews }})
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-xl font-bold text-primary">
                                    ${{ product.price.toFixed(2) }}
                                </span>
                                <span
                                    v-if="product.originalPrice"
                                    class="ml-2 text-sm text-muted-foreground line-through"
                                >
                                    ${{ product.originalPrice.toFixed(2) }}
                                </span>
                            </div>
                        </div>
                        <Button
                            class="w-full mt-4"
                            aria-label="Add {{ product.name }} to cart"
                        >
                            Add to Cart
                        </Button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Latest Products -->
        <section
            id="new-arrivals"
            class="container mx-auto px-4 sm:px-6 lg:px-8 py-12"
            aria-label="Latest products"
        >
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl sm:text-3xl font-bold">New Arrivals</h2>
                <Button variant="outline" aria-label="View all new arrivals">
                    See All
                </Button>
            </div>
            <div
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6"
            >
                <div
                    v-for="product in latestProducts"
                    :key="product.id"
                    class="group relative bg-card rounded-xl border border-border overflow-hidden hover:shadow-lg transition-all"
                >
                    <div class="relative aspect-square overflow-hidden bg-muted">
                        <img
                            :src="product.image"
                            :alt="product.name"
                            class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-110"
                        />
                        <div
                            v-if="product.isNew"
                            class="absolute top-3 right-3"
                        >
                            <Badge
                                class="bg-green-500 text-white"
                                aria-label="New product"
                            >
                                New
                            </Badge>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold mb-2 line-clamp-2">
                            {{ product.name }}
                        </h3>
                        <div class="flex items-center gap-2 mb-2">
                            <div class="flex items-center">
                                <svg
                                    v-for="i in 5"
                                    :key="i"
                                    class="h-4 w-4"
                                    :class="
                                        i <= Math.floor(product.rating)
                                            ? 'text-yellow-400 fill-yellow-400'
                                            : 'text-muted-foreground'
                                    "
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                                    />
                                </svg>
                            </div>
                            <span class="text-sm text-muted-foreground">
                                ({{ product.reviews }})
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xl font-bold text-primary">
                                ${{ product.price.toFixed(2) }}
                            </span>
                        </div>
                        <Button
                            class="w-full mt-4"
                            aria-label="Add {{ product.name }} to cart"
                        >
                            Add to Cart
                        </Button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact & Newsletter -->
        <section
            id="contact-us"
            class="container mx-auto px-4 sm:px-6 lg:px-8 py-12"
            aria-label="Contact and newsletter"
        >
            <div class="grid md:grid-cols-2 gap-12 items-start">
                <!-- Left: Contact Details -->
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold mb-6">
                        Get in Touch
                    </h2>
                    <div class="space-y-4">
                        <div class="flex items-start gap-4">
                            <div
                                class="shrink-0 w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center"
                            >
                                <svg
                                    class="h-5 w-5 text-primary"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                    />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold mb-1">Email</h3>
                                <a
                                    href="mailto:support@shophub.com"
                                    class="text-muted-foreground hover:text-primary transition-colors"
                                >
                                    support@shophub.com
                                </a>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div
                                class="shrink-0 w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center"
                            >
                                <svg
                                    class="h-5 w-5 text-primary"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                                    />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold mb-1">Phone</h3>
                                <a
                                    href="tel:+1234567890"
                                    class="text-muted-foreground hover:text-primary transition-colors"
                                >
                                    +1 (234) 567-890
                                </a>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div
                                class="shrink-0 w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center"
                            >
                                <svg
                                    class="h-5 w-5 text-primary"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                    />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold mb-1">Address</h3>
                                <p class="text-muted-foreground">
                                    123 Shopping Street<br />New York, NY 10001
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Social Icons -->
                    <div class="mt-8">
                        <h3 class="font-semibold mb-4">Follow Us</h3>
                        <div class="flex gap-4">
                            <a
                                href="#"
                                class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center hover:bg-primary hover:text-primary-foreground transition-colors"
                                aria-label="Facebook"
                            >
                                <svg
                                    class="h-5 w-5"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"
                                    />
                                </svg>
                            </a>
                            <a
                                href="#"
                                class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center hover:bg-primary hover:text-primary-foreground transition-colors"
                                aria-label="Twitter"
                            >
                                <svg
                                    class="h-5 w-5"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"
                                    />
                                </svg>
                            </a>
                            <a
                                href="#"
                                class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center hover:bg-primary hover:text-primary-foreground transition-colors"
                                aria-label="Instagram"
                            >
                                <svg
                                    class="h-5 w-5"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.17-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.057-1.274-.07-1.649-.07-4.844 0-3.196.016-3.586.074-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"
                                    />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right: Newsletter -->
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold mb-6">
                        Stay Updated
                    </h2>
                    <p class="text-muted-foreground mb-6">
                        Subscribe to our newsletter and get the latest deals, new
                        arrivals, and exclusive offers delivered straight to your inbox.
                    </p>
                    <form @submit="handleNewsletterSubmit" class="space-y-4">
                        <div>
                            <Input
                                v-model="newsletterEmail"
                                type="email"
                                placeholder="Enter your email address"
                                required
                                class="h-12 text-base"
                                aria-label="Email address for newsletter"
                            />
                        </div>
                        <Button
                            type="submit"
                            size="lg"
                            class="w-full h-12 text-base"
                            :disabled="newsletterSubmitted"
                            aria-label="Subscribe to newsletter"
                        >
                            <span v-if="!newsletterSubmitted">Subscribe</span>
                            <span v-else class="flex items-center gap-2">
                                <svg
                                    class="animate-spin h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <circle
                                        class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"
                                    />
                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                    />
                                </svg>
                                Subscribed!
                            </span>
                        </Button>
                    </form>
                    <Transition name="fade">
                        <p
                            v-if="newsletterSubmitted"
                            class="mt-4 text-sm text-green-600 dark:text-green-400"
                            role="status"
                            aria-live="polite"
                        >
                            Thank you for subscribing! Check your email for confirmation.
                        </p>
                    </Transition>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer
            class="border-t bg-card"
            role="contentinfo"
            aria-label="Site footer"
        >
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8"
                >
                    <!-- About Us -->
                    <div>
                        <h3 class="font-semibold text-lg mb-4">About Us</h3>
                        <ul class="space-y-2 text-sm text-muted-foreground">
                            <li>
                                <a
                                    href="#"
                                    class="hover:text-primary transition-colors"
                                >
                                    Our Story
                                </a>
                            </li>
                            <li>
                                <a
                                    href="#"
                                    class="hover:text-primary transition-colors"
                                >
                                    Careers
                                </a>
                            </li>
                            <li>
                                <a
                                    href="#"
                                    class="hover:text-primary transition-colors"
                                >
                                    Press
                                </a>
                            </li>
                            <li>
                                <a
                                    href="#"
                                    class="hover:text-primary transition-colors"
                                >
                                    Blog
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Customer Service -->
                    <div>
                        <h3 class="font-semibold text-lg mb-4">
                            Customer Service
                        </h3>
                        <ul class="space-y-2 text-sm text-muted-foreground">
                            <li>
                                <a
                                    href="#"
                                    class="hover:text-primary transition-colors"
                                >
                                    Contact Us
                                </a>
                            </li>
                            <li>
                                <a
                                    href="#"
                                    class="hover:text-primary transition-colors"
                                >
                                    Shipping Info
                                </a>
                            </li>
                            <li>
                                <a
                                    href="#"
                                    class="hover:text-primary transition-colors"
                                >
                                    Returns & Exchanges
                                </a>
                            </li>
                            <li>
                                <a
                                    href="#"
                                    class="hover:text-primary transition-colors"
                                >
                                    FAQ
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Privacy Policy -->
                    <div>
                        <h3 class="font-semibold text-lg mb-4">Legal</h3>
                        <ul class="space-y-2 text-sm text-muted-foreground">
                            <li>
                                <a
                                    href="#"
                                    class="hover:text-primary transition-colors"
                                >
                                    Privacy Policy
                                </a>
                            </li>
                            <li>
                                <a
                                    href="#"
                                    class="hover:text-primary transition-colors"
                                >
                                    Terms of Service
                                </a>
                            </li>
                            <li>
                                <a
                                    href="#"
                                    class="hover:text-primary transition-colors"
                                >
                                    Cookie Policy
                                </a>
                            </li>
                            <li>
                                <a
                                    href="#"
                                    class="hover:text-primary transition-colors"
                                >
                                    Accessibility
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Download Our App -->
                    <div>
                        <h3 class="font-semibold text-lg mb-4">
                            Download Our App
                        </h3>
                        <p class="text-sm text-muted-foreground mb-4">
                            Shop on the go with our mobile app
                        </p>
                        <div class="flex flex-col gap-3">
                            <a
                                href="#"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-border bg-background hover:bg-accent transition-colors"
                                aria-label="Download on App Store"
                            >
                                <svg
                                    class="h-5 w-5"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"
                                    />
                                </svg>
                                <span class="text-sm font-medium">App Store</span>
                            </a>
                            <a
                                href="#"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-border bg-background hover:bg-accent transition-colors"
                                aria-label="Get it on Google Play"
                            >
                                <svg
                                    class="h-5 w-5"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M3,20.5V3.5C3,2.91 3.34,2.39 3.84,2.15L13.69,12L3.84,21.85C3.34,21.6 3,21.09 3,20.5M16.81,15.12L6.05,21.34L14.54,12.85L16.81,15.12M20.16,10.81C20.5,11.08 20.75,11.5 20.75,12C20.75,12.5 20.5,12.92 20.16,13.19L16.81,15.12L14.54,12.85L20.16,10.81M6.05,2.66L16.81,8.88L14.54,11.15L6.05,2.66Z"
                                    />
                                </svg>
                                <span class="text-sm font-medium">Google Play</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Copyright -->
                <div
                    class="mt-8 pt-8 border-t text-center text-sm text-muted-foreground"
                >
                    <p>
                        &copy; {{ new Date().getFullYear() }} ShopHub. All rights
                        reserved.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* Slide transitions */
.slide-enter-active,
.slide-leave-active {
    transition: opacity 0.5s ease, transform 0.5s ease;
}

.slide-enter-from {
    opacity: 0;
    transform: translateX(100%);
}

.slide-leave-to {
    opacity: 0;
    transform: translateX(-100%);
}

.slide-enter-to,
.slide-leave-from {
    opacity: 1;
    transform: translateX(0);
}

/* Fade transition */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Hide scrollbar */
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

/* Line clamp utility */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
