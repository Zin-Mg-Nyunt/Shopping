<script setup>
import { Link } from '@inertiajs/vue3';
import { Moon, Sun } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import UserNavAvatar from '@/components/UserNavAvatar.vue';
import { useAppearance } from '@/composables/useAppearance';
import { useCartCount } from '@/composables/useCartCount';
import { Toaster } from 'vue-sonner';
import 'vue-sonner/style.css';
// import { useCurrentUrl } from '@/composables/useCurrentUrl';

// const { isCurrentUrl } = useCurrentUrl();
const { cartCount } = useCartCount();
const bounce = ref(false);
const { resolvedAppearance, updateAppearance } = useAppearance();
const isDark = computed(() => resolvedAppearance.value === 'dark');
const navItems = [
    {
        label: 'Dashboard',
        href: route('user.dashboard'),
        match: () => route().current('user.dashboard'),
    },
    {
        label: 'My Profile',
        href: route('profile.edit'),
        match: () => route().current('profile.edit'),
    },
    {
        label: 'Order History',
        href: route('user.orders.list'),
        match: () => route().current('user.orders.list'),
    },
    {
        label: 'My Wishlist',
        href: route('user.wishlist'),
        match: () => route().current('user.wishlist'),
    },
    {
        label: 'Shipping Address',
        href: route('user.shipping.address'),
        match: () => route().current('user.shipping.address'),
    },
];
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
    <div class="flex min-h-screen flex-col bg-muted text-foreground">
        <Toaster position="top-right" richColors />
        <header
            class="border-b border-border bg-card shadow-sm dark:bg-card/95"
        >
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
                        class="hidden rounded-full px-3 py-2 text-sm font-medium text-muted-foreground transition hover:bg-muted hover:text-foreground sm:inline"
                    >
                        Shop more
                    </Link>
                </div>

                <nav class="ml-auto flex shrink-0 items-center gap-2 sm:gap-3">
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
                            class="absolute right-0 z-20 mt-2 w-52 rounded-xl border border-border bg-popover p-2 text-popover-foreground shadow-lg"
                        >
                            <template v-if="$page.props.auth?.user">
                                <p
                                    class="px-3 py-2 text-xs font-medium text-muted-foreground"
                                >
                                    {{ $page.props.auth.user.name }}
                                </p>
                                <Link
                                    :href="route('user.dashboard')"
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

        <main class="flex-1">
            <div
                class="mx-auto flex w-full max-w-7xl flex-col gap-6 px-6 py-8 lg:flex-row lg:gap-8 lg:px-8"
            >
                <aside class="w-full shrink-0 lg:w-64">
                    <nav
                        class="rounded-2xl border border-border bg-card p-3 shadow-sm"
                        aria-label="Account navigation"
                    >
                        <p
                            class="px-3 pb-2 text-xs font-semibold tracking-wide text-muted-foreground uppercase"
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
                                            ? 'border border-primary/25 bg-primary/12 text-primary shadow-sm'
                                            : 'text-muted-foreground hover:bg-muted hover:text-foreground'
                                    "
                                >
                                    <span
                                        class="h-1.5 w-1.5 shrink-0 rounded-full"
                                        :class="
                                            item.match()
                                                ? 'bg-primary'
                                                : 'bg-muted-foreground/40'
                                        "
                                    />
                                    {{ item.label }}
                                </Link>
                                <span
                                    v-else
                                    class="flex cursor-not-allowed items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-muted-foreground/60"
                                >
                                    <span
                                        class="h-1.5 w-1.5 shrink-0 rounded-full bg-muted-foreground/30"
                                    />
                                    {{ item.label }}
                                    <span
                                        class="ml-auto text-[10px] font-normal text-muted-foreground"
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

        <footer class="mt-auto border-t border-border bg-card">
            <div
                class="mx-auto flex w-full max-w-7xl flex-col gap-4 px-6 py-8 text-sm text-muted-foreground md:flex-row md:items-center md:justify-between lg:px-8"
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
