<script setup>
import { Link } from '@inertiajs/vue3';
import AppLogo from '@/components/AppLogo.vue';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { route } from 'ziggy-js';

const { isCurrentUrl } = useCurrentUrl();

const navItems = [
    {
        label: 'My Profile',
        href: route('profile.edit'),
        match: () => isCurrentUrl(route('profile.edit')),
    },
    {
        label: 'Order History',
        href: '#',
        match: () => false,
    },
    {
        label: 'My Wishlist',
        href: '#',
        match: () => false,
    },
    {
        label: 'Shipping Address',
        href: '#',
        match: () => false,
    },
    {
        label: 'Settings',
        href: route('security.edit'),
        match: () => isCurrentUrl(route('security.edit')),
    },
];
</script>

<template>
    <div class="flex min-h-screen flex-col bg-[#F4F7F6] text-gray-900">
        <header class="border-b border-gray-200/80 bg-white shadow-sm">
            <div
                class="mx-auto flex w-full max-w-7xl flex-wrap items-center gap-4 px-6 py-4 lg:px-8"
            >
                <div class="flex min-w-0 flex-1 items-center gap-4">
                    <Link
                        :href="route('home')"
                        class="shrink-0 rounded-xl transition hover:opacity-90"
                    >
                        <AppLogo />
                    </Link>
                    <Link
                        :href="route('products.list')"
                        class="hidden rounded-full px-3 py-2 text-sm font-medium text-primary transition hover:bg-primary/10 hover:text-primary sm:inline"
                    >
                        Shop more
                    </Link>
                </div>

                <div class="w-full flex-1 md:max-w-md">
                    <label for="account-search" class="sr-only"
                        >Search order with order number</label
                    >
                    <div class="relative">
                        <input
                            id="order-search"
                            type="search"
                            placeholder="Search order with order number"
                            class="w-full rounded-full border border-gray-200 bg-[#F4F7F6] py-2 pr-10 pl-4 text-sm text-gray-700 placeholder:text-gray-400 focus:border-primary focus:bg-white focus:outline-none"
                        />
                        <span
                            class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-400"
                        >
                            <svg
                                class="h-5 w-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <circle cx="11" cy="11" r="7" />
                                <path d="m20 20-3.5-3.5" />
                            </svg>
                        </span>
                    </div>
                </div>

                <nav class="ml-auto flex shrink-0 items-center gap-2 sm:gap-3">
                    <button
                        type="button"
                        class="rounded-full border border-gray-200 bg-[#F4F7F6] p-2 text-gray-600 transition hover:border-primary/40 hover:text-primary"
                    >
                        <span class="sr-only">Cart</span>
                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <circle cx="9" cy="20" r="1" />
                            <circle cx="18" cy="20" r="1" />
                            <path
                                d="M3 4h2l2.2 10.5a1 1 0 0 0 1 .8h9.6a1 1 0 0 0 1-.8L21 7H6.2"
                            />
                        </svg>
                    </button>
                    <details class="relative">
                        <summary
                            class="list-none rounded-full border border-gray-200 bg-[#F4F7F6] p-2 text-gray-600 transition hover:border-primary/40 hover:text-primary [&::-webkit-details-marker]:hidden"
                        >
                            <span class="sr-only">Account menu</span>
                            <svg
                                class="h-5 w-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <circle cx="12" cy="8" r="4" />
                                <path d="M4 20c1.5-3.5 4.5-5 8-5s6.5 1.5 8 5" />
                            </svg>
                        </summary>

                        <div
                            class="absolute right-0 z-20 mt-2 w-52 rounded-2xl border border-gray-200/80 bg-white p-2 shadow-lg"
                        >
                            <template v-if="$page.props.auth?.user">
                                <p
                                    class="px-3 py-2 text-xs font-medium text-gray-500"
                                >
                                    {{ $page.props.auth.user.name }}
                                </p>
                                <Link
                                    :href="route('dashboard')"
                                    class="block rounded-xl px-3 py-2 text-sm text-gray-700 transition hover:bg-primary/10 hover:text-primary"
                                >
                                    Dashboard
                                </Link>
                                <Link
                                    :href="route('profile.edit')"
                                    class="block rounded-xl px-3 py-2 text-sm text-gray-700 transition hover:bg-primary/10 hover:text-primary"
                                >
                                    Profile
                                </Link>
                            </template>
                            <template v-else>
                                <Link
                                    :href="route('login')"
                                    class="block rounded-xl px-3 py-2 text-sm text-gray-700 transition hover:bg-primary/10 hover:text-primary"
                                >
                                    Log In
                                </Link>
                                <Link
                                    :href="route('register')"
                                    class="block rounded-xl px-3 py-2 text-sm text-gray-700 transition hover:bg-primary/10 hover:text-primary"
                                >
                                    Register
                                </Link>
                            </template>
                        </div>
                    </details>
                </nav>
            </div>
        </header>

        <main class="flex-1">
            <div
                class="mx-auto flex w-full max-w-7xl flex-col gap-6 px-6 py-8 lg:flex-row lg:gap-8 lg:px-8"
            >
                <aside class="w-full shrink-0 lg:w-64">
                    <nav
                        class="rounded-2xl border border-gray-200/80 bg-white p-3 shadow-sm"
                        aria-label="Account navigation"
                    >
                        <p
                            class="px-3 pb-2 text-xs font-semibold tracking-wide text-gray-400 uppercase"
                        >
                            My account
                        </p>
                        <ul class="space-y-1">
                            <li v-for="item in navItems" :key="item.label">
                                <Link
                                    v-if="item.href !== '#'"
                                    :href="item.href"
                                    class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition"
                                    :class="
                                        item.match()
                                            ? 'bg-primary/10 text-primary shadow-sm'
                                            : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                                    "
                                >
                                    <span
                                        class="h-1.5 w-1.5 shrink-0 rounded-full"
                                        :class="
                                            item.match()
                                                ? 'bg-primary'
                                                : 'bg-transparent'
                                        "
                                    />
                                    {{ item.label }}
                                </Link>
                                <span
                                    v-else
                                    class="flex cursor-not-allowed items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-400"
                                >
                                    <span
                                        class="h-1.5 w-1.5 shrink-0 rounded-full bg-transparent"
                                    />
                                    {{ item.label }}
                                    <span
                                        class="ml-auto text-[10px] font-normal text-gray-400"
                                        >Soon</span
                                    >
                                </span>
                            </li>
                        </ul>
                    </nav>
                </aside>

                <div class="min-w-0 flex-1">
                    <slot />
                </div>
            </div>
        </main>

        <footer
            class="mt-auto border-t border-gray-200/80 bg-white shadow-[0_-1px_0_rgba(0,0,0,0.03)]"
        >
            <div
                class="mx-auto flex w-full max-w-7xl flex-col gap-4 px-6 py-8 text-sm text-gray-500 md:flex-row md:items-center md:justify-between lg:px-8"
            >
                <p>© 2026 Shopping. All rights reserved.</p>
                <div class="flex flex-wrap items-center gap-5">
                    <a href="#" class="transition hover:text-primary"
                        >Privacy</a
                    >
                    <a href="#" class="transition hover:text-primary">Terms</a>
                    <a href="#" class="transition hover:text-primary"
                        >Support</a
                    >
                </div>
            </div>
        </footer>
    </div>
</template>
