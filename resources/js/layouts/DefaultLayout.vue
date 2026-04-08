<script setup>
import { Link } from '@inertiajs/vue3';
import AppLogo from '@/components/AppLogo.vue';
import useFilterBy from '@/composables/useFilterBy';
import { Toaster } from 'vue-sonner';
import 'vue-sonner/style.css';

const { filterBy } = useFilterBy();
</script>
<template>
    <Toaster position="top-right" richColors />
    <div class="flex min-h-screen flex-col bg-muted text-foreground">
        <header
            class="border-b border-border bg-card shadow-sm dark:bg-card/95"
        >
            <div
                class="mx-auto flex w-full max-w-7xl items-center gap-4 px-6 py-4 lg:px-8"
            >
                <Link :href="route('home')">
                    <AppLogo />
                </Link>

                <div class="flex-1">
                    <label for="search" class="sr-only">Search products</label>
                    <div class="relative">
                        <input
                            id="search"
                            type="search"
                            placeholder="Search for products..."
                            class="w-full rounded-full border border-border bg-muted py-2 pr-10 pl-4 text-sm text-foreground placeholder:text-muted-foreground focus:border-primary focus:bg-card focus:outline-none"
                            @input="filterBy('search', $event.target.value)"
                        />
                        <span
                            class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-muted-foreground"
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

                <nav class="ml-auto flex items-center gap-3">
                    <Link
                        :href="route('cart')"
                        class="relative rounded-full border border-border bg-card p-2 text-muted-foreground transition hover:border-primary/40 hover:text-primary"
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
                        <span
                            v-if="cartItemCount > 0"
                            class="absolute -top-1 -right-1 inline-flex min-h-5 min-w-5 items-center justify-center rounded-full bg-primary px-1 text-[11px] leading-none font-semibold text-primary-foreground"
                        >
                            {{ cartItemCount }}
                        </span>
                    </Link>
                    <details class="relative">
                        <summary
                            class="list-none rounded-full border border-border bg-card p-2 text-muted-foreground transition hover:border-primary/40 hover:text-primary [&::-webkit-details-marker]:hidden"
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
                            class="absolute right-0 z-20 mt-2 w-48 rounded-xl border border-border bg-popover p-2 text-popover-foreground shadow-lg"
                        >
                            <template v-if="$page.props.auth?.user">
                                <p
                                    class="px-3 py-2 text-xs font-medium text-muted-foreground"
                                >
                                    {{ $page.props.auth.user.name }}
                                </p>
                                <Link
                                    :href="route('dashboard')"
                                    class="block rounded-lg px-3 py-2 text-sm transition hover:bg-accent hover:text-primary"
                                >
                                    Dashboard
                                </Link>
                                <Link
                                    :href="route('profile.edit')"
                                    class="block rounded-lg px-3 py-2 text-sm transition hover:bg-accent hover:text-primary"
                                >
                                    Profile
                                </Link>
                            </template>
                            <template v-else>
                                <Link
                                    :href="route('login')"
                                    class="block rounded-lg px-3 py-2 text-sm transition hover:bg-accent hover:text-primary"
                                >
                                    Log In
                                </Link>
                                <Link
                                    :href="route('register')"
                                    class="block rounded-lg px-3 py-2 text-sm transition hover:bg-accent hover:text-primary"
                                >
                                    Register
                                </Link>
                            </template>
                        </div>
                    </details>
                </nav>
            </div>
        </header>

        <slot />

        <footer class="mt-auto border-t border-border bg-card">
            <div
                class="mx-auto flex w-full max-w-7xl flex-col gap-3 px-6 py-8 text-sm text-muted-foreground md:flex-row md:items-center md:justify-between lg:px-8"
            >
                <p>© 2026 Shopping. All rights reserved.</p>
                <div class="flex items-center gap-5">
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
