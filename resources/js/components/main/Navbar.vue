<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Link, usePage } from '@inertiajs/vue3';
import { Menu, Search, ShoppingCart, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

const page = usePage();
const auth = computed(() => page.props.auth);
const isAuthenticated = computed(() => !!auth.value?.user);

const isMobileMenuOpen = ref(false);
const searchItem = ref('');

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const navLinks = [
    { name: 'Home', href: '/' },
    { name: 'Products', href: '/products' },
];
</script>

<template>
    <nav
        class="sticky top-0 z-50 w-full border-b border-border bg-background/95 backdrop-blur supports-backdrop-filter:bg-background/60"
    >
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <!-- Left Section: Logo + Nav Links -->
                <div class="flex items-center">
                    <!-- Logo -->
                    <Link href="/" class="flex items-center gap-2">
                        <div
                            class="flex"    
                        >
                            <AppLogoIcon class="size-30 fill-current" />
                        </div>
                        
                    </Link>

                    <!-- Desktop Nav Links -->
                    <div class="hidden items-center gap-6 md:flex">
                        <Link
                            v-for="link in navLinks"
                            :key="link.name"
                            :href="link.href"
                            class="text-sm font-medium text-foreground transition-colors hover:text-primary"
                            :class="{
                                'text-primary': $page.url === link.href,
                            }"
                        >
                            {{ link.name }}
                        </Link>
                    </div>
                </div>

                <!-- Middle Section: Search Bar -->
                <div class="hidden flex-1 items-center justify-center px-4 md:flex lg:px-8">
                    <div class="relative w-full max-w-lg">
                        <Search
                            class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground"
                        />
                        <Input
                            v-model="searchItem"
                            type="text"
                            placeholder="Search products..."
                            class="w-full pl-10 pr-4 rounded-none border-0 border-b shadow-none focus-visible:ring-0 focus-visible:border-[#e5e5e5] focus-visible:shadow-md"
                        />
                    </div>
                </div>

                <!-- Right Section: Cart + Auth Buttons -->
                <div class="flex items-center gap-4">
                    <!-- Cart Icon -->
                    <Button
                        variant="ghost"
                        size="icon"
                        class="relative"
                        as-child
                    >
                        <Link href="/cart">
                            <ShoppingCart class="size-5" />
                            <span
                                class="absolute -right-1 -top-1 flex size-5 items-center justify-center rounded-full bg-primary text-primary-foreground text-xs font-medium "
                            >
                                0
                            </span>
                        </Link>
                    </Button>

                    <!-- Desktop Auth Buttons -->
                    <div class="hidden items-center gap-2 md:flex">
                        <Button
                            v-if="!isAuthenticated"
                            variant="ghost"
                            as-child
                        >
                            <Link href="/login" >Log in</Link>
                        </Button>
                        <Button
                            v-if="!isAuthenticated"
                            as-child
                        >
                            <Link href="/register" >Sign up</Link>
                        </Button>
                        <Button
                            v-if="isAuthenticated"
                            variant="ghost"
                            as-child
                        >
                            <Link href="/dashboard">Dashboard</Link>
                        </Button>
                    </div>

                    <!-- Mobile Menu Button -->
                    <Button
                        variant="ghost"
                        size="icon"
                        class="md:hidden"
                        @click="toggleMobileMenu"
                    >
                        <Menu v-if="!isMobileMenuOpen" class="size-5" />
                        <X v-else class="size-5" />
                    </Button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div
                v-if="isMobileMenuOpen"
                class="border-t border-border py-4 md:hidden"
            >
                <!-- Mobile Search -->
                <div class="mb-4 px-2">
                    <div class="relative">
                        <Search
                            class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground"
                        />
                        <Input
                            v-model="searchItem"
                            type="text"
                            placeholder="Search products..."
                            class="w-full pl-10 pr-4 focus-visible:ring-0 focus-visible:border-[#e5e5e5] focus-visible:shadow-md"
                        />
                    </div>
                </div>

                <!-- Mobile Nav Links -->
                <div class="space-y-1 px-2">
                    <Link
                        v-for="link in navLinks"
                        :key="link.name"
                        :href="link.href"
                        class="block rounded-md px-3 py-2 text-base font-medium text-foreground transition-colors hover:bg-accent hover:text-accent-foreground"
                        :class="{
                            'bg-accent text-primary':
                                $page.url === link.href,
                        }"
                        @click="toggleMobileMenu"
                    >
                        {{ link.name }}
                    </Link>
                </div>

                <!-- Mobile Auth Buttons -->
                <div class="mt-4 space-y-2 border-t border-border px-2 pt-4">
                    <Button
                        v-if="!isAuthenticated"
                        variant="ghost"
                        class="w-full justify-start"
                        as-child
                    >
                        <Link href="/login" @click="toggleMobileMenu"
                            >Log in</Link
                        >
                    </Button>
                    <Button
                        v-if="!isAuthenticated"
                        class="w-full"
                        as-child
                    >
                        <Link href="/register" @click="toggleMobileMenu"
                            >Sign up</Link
                        >
                    </Button>
                    <Button
                        v-if="isAuthenticated"
                        variant="ghost"
                        class="w-full justify-start"
                        as-child
                    >
                        <Link href="/dashboard" @click="toggleMobileMenu"
                            >Dashboard</Link
                        >
                    </Button>
                </div>
            </div>
        </div>
    </nav>
</template>
