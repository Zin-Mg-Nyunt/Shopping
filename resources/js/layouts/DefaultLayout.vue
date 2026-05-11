<script setup>
import { Link } from '@inertiajs/vue3';
import {
    Moon,
    Sun,
    UserCircleIcon,
    UserIcon,
    UserRound,
} from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';
import UserNavAvatar from '@/components/UserNavAvatar.vue';
import { useAppearance } from '@/composables/useAppearance';
import useFilterBy from '@/composables/useFilterBy';
import { useCartCount } from '@/composables/useCartCount';
import { computed, ref, watch } from 'vue';
import { Toaster } from 'vue-sonner';
import 'vue-sonner/style.css';

const bounce = ref(false);
const { cartCount } = useCartCount();
const { filterBy } = useFilterBy();
const { resolvedAppearance, updateAppearance } = useAppearance();
const isDark = computed(() => resolvedAppearance.value === 'dark');
watch(cartCount, (newCount, oldCount) => {
    if (newCount > oldCount) {
        bounce.value = true;
        setTimeout(() => {
            bounce.value = false;
        }, 1000);
    }
});

function toggleTheme() {
    updateAppearance(isDark.value ? 'light' : 'dark');
}
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
                    <button
                        type="button"
                        class="rounded-full border border-border bg-card p-2 text-muted-foreground transition hover:border-primary/40 hover:text-primary"
                        aria-label="Toggle dark mode"
                        @click="toggleTheme"
                    >
                        <Sun v-if="isDark" class="h-5 w-5" />
                        <Moon v-else class="h-5 w-5" />
                    </button>
                    <Link
                        :href="route('cart')"
                        class="relative rounded-full border border-border bg-card p-2 text-muted-foreground transition hover:border-primary/40 hover:text-primary"
                    >
                        <span class="sr-only">Cart</span>
                        <svg
                            class="h-5 w-5"
                            :class="{ 'cart-shake': bounce }"
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
                            v-if="cartCount > 0"
                            class="absolute -top-1 -right-1 inline-flex min-h-5 min-w-5 items-center justify-center rounded-full bg-primary px-1 text-[11px] leading-none font-semibold text-primary-foreground"
                        >
                            {{ cartCount }}
                        </span>
                    </Link>
                    <details class="relative">
                        <summary
                            class="list-none [&::-webkit-details-marker]:hidden"
                        >
                            <span class="sr-only">Account menu</span>
                            <UserNavAvatar
                                v-if="$page.props.auth?.user"
                                :user="$page.props.auth.user"
                            />
                            <span
                                v-else
                                class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-border bg-card text-muted-foreground transition hover:border-primary/40 hover:text-primary"
                            >
                                <UserRound class="h-5 w-5 stroke-1" />
                            </span>
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

<style scoped>
@keyframes cart-shake {
    0%,
    100% {
        transform: rotate(0deg);
    }
    20% {
        transform: rotate(-10deg);
    }
    40% {
        transform: rotate(8deg);
    }
    60% {
        transform: rotate(-6deg);
    }
    80% {
        transform: rotate(4deg);
    }
}

.cart-shake {
    animation: cart-shake 420ms ease-in-out;
    transform-origin: center;
}
</style>
