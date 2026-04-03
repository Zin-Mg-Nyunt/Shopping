<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Minus, Plus, ShoppingBag, Trash2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { route } from 'ziggy-js';

const props = defineProps({
    items: {
        type: Array,
        required: true,
    },
});

const lines = ref(props.items.map((item) => ({ ...item })));

const promoInput = ref('');
const appliedPromo = ref(null);
const promoFeedback = ref('idle');

const currency = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
});

function formatMoney(value) {
    return currency.format(value);
}

const subtotal = computed(() =>
    lines.value.reduce((sum, line) => sum + line.unit_price * line.quantity, 0),
);

const discountAmount = computed(() => {
    if (appliedPromo.value === 'SAVE10') {
        return Math.round(subtotal.value * 0.1 * 100) / 100;
    }

    return 0;
});

const afterDiscount = computed(() =>
    Math.max(0, subtotal.value - discountAmount.value),
);

const shipping = computed(() => (afterDiscount.value >= 200 ? 0 : 9.99));

const estimatedTax = computed(
    () => Math.round(afterDiscount.value * 0.08 * 100) / 100,
);

const orderTotal = computed(
    () => afterDiscount.value + shipping.value + estimatedTax.value,
);

const isEmpty = computed(() => lines.value.length === 0);

function increment(id) {
    const line = lines.value.find((l) => l.id === id);

    if (line && line.quantity < 99) {
        line.quantity += 1;
    }
}

function decrement(id) {
    const line = lines.value.find((l) => l.id === id);

    if (line && line.quantity > 1) {
        line.quantity -= 1;
    }
}

function removeLine(id) {
    lines.value = lines.value.filter((l) => l.id !== id);
}

function applyPromo() {
    const code = promoInput.value.trim().toUpperCase();

    if (!code) {
        promoFeedback.value = 'idle';
        appliedPromo.value = null;

        return;
    }

    if (code === 'SAVE10') {
        appliedPromo.value = 'SAVE10';
        promoFeedback.value = 'success';
    } else {
        appliedPromo.value = null;
        promoFeedback.value = 'invalid';
    }
}

function lineSubtotal(line) {
    return line.unit_price * line.quantity;
}
</script>

<template>
    <Head title="Shopping Cart" />

    <main class="">
        <div class="mx-auto w-full max-w-7xl px-6 py-10 lg:px-8">
            <!-- Empty state -->
            <div
                v-if="isEmpty"
                class="mx-auto flex max-w-lg flex-col items-center text-center"
            >
                <div
                    class="mb-8 flex h-48 w-full max-w-sm items-center justify-center rounded-3xl border border-dashed border-border bg-card px-8 py-10 shadow-sm"
                    aria-hidden="true"
                >
                    <svg
                        class="h-full max-h-36 w-full text-primary/90"
                        viewBox="0 0 320 240"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <rect
                            x="48"
                            y="72"
                            width="200"
                            height="120"
                            rx="16"
                            stroke="currentColor"
                            stroke-width="2.5"
                            class="opacity-40"
                        />
                        <path
                            d="M88 112h144M88 136h96"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            class="opacity-30"
                        />
                        <circle
                            cx="108"
                            cy="188"
                            r="10"
                            stroke="currentColor"
                            stroke-width="2"
                        />
                        <circle
                            cx="212"
                            cy="188"
                            r="10"
                            stroke="currentColor"
                            stroke-width="2"
                        />
                        <path
                            d="M72 72h176l-16-40H88L72 72Z"
                            stroke="currentColor"
                            stroke-width="2.5"
                            stroke-linejoin="round"
                            fill="currentColor"
                            fill-opacity="0.08"
                        />
                        <circle
                            cx="248"
                            cy="56"
                            r="28"
                            fill="currentColor"
                            fill-opacity="0.08"
                            stroke="currentColor"
                            stroke-width="2"
                        />
                        <path
                            d="M248 44v24M236 56h24"
                            stroke="currentColor"
                            stroke-width="2.5"
                            stroke-linecap="round"
                        />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-foreground">
                    Your cart is empty
                </h1>
                <p class="mt-1 max-w-sm text-sm text-muted-foreground">
                    Looks like you have not added anything yet. Browse our
                    catalog and find something you love.
                </p>
                <Link
                    :href="route('products.list')"
                    class="mt-8 inline-flex items-center justify-center rounded-full bg-primary px-8 py-3 text-sm font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90"
                >
                    Continue shopping
                </Link>
            </div>

            <!-- Cart with items -->
            <div v-else>
                <div
                    class="mb-8 flex flex-col gap-1 border-b border-border pb-6"
                >
                    <h1
                        class="text-2xl font-bold tracking-tight text-foreground"
                    >
                        Shopping cart
                    </h1>
                    <p class="text-sm text-muted-foreground">
                        Review your items and proceed to checkout when ready.
                    </p>
                </div>

                <div
                    class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_380px] lg:items-start"
                >
                    <!-- Line items -->
                    <div class="space-y-4">
                        <article
                            v-for="line in lines"
                            :key="line.id"
                            class="flex gap-4 rounded-2xl border border-border bg-card p-4 shadow-sm transition hover:shadow-md sm:gap-6 sm:p-5"
                        >
                            <div class="relative shrink-0">
                                <img
                                    v-if="line.image_url"
                                    :src="line.image_url"
                                    :alt="line.name"
                                    class="h-24 w-24 rounded-xl object-cover sm:h-28 sm:w-28"
                                />
                                <div
                                    v-else
                                    class="flex h-24 w-24 items-center justify-center rounded-xl bg-muted sm:h-28 sm:w-28"
                                >
                                    <ShoppingBag
                                        class="h-10 w-10 text-muted-foreground/50"
                                        stroke-width="1.5"
                                    />
                                </div>
                            </div>

                            <div class="mt-0.5 min-w-0 flex-1">
                                <p
                                    class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
                                >
                                    {{ line.category }}
                                </p>
                                <h2
                                    class="mt-0.5 font-semibold text-foreground"
                                >
                                    {{ line.name }}
                                </h2>
                                <p class="mt-1 text-sm text-muted-foreground">
                                    {{ formatMoney(line.unit_price) }} each
                                </p>

                                <div
                                    class="mt-4 flex flex-wrap items-center gap-4"
                                >
                                    <div
                                        class="inline-flex items-center rounded-xl border border-border bg-muted/50 p-0.5"
                                    >
                                        <button
                                            type="button"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg text-primary transition hover:bg-primary/10 disabled:cursor-not-allowed disabled:opacity-40"
                                            :disabled="line.quantity <= 1"
                                            aria-label="Decrease quantity"
                                            @click="decrement(line.id)"
                                        >
                                            <Minus class="h-4 w-4" />
                                        </button>
                                        <span
                                            class="min-w-8 text-center text-sm font-semibold text-foreground tabular-nums"
                                        >
                                            {{ line.quantity }}
                                        </span>
                                        <button
                                            type="button"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg text-primary transition hover:bg-primary/10 disabled:cursor-not-allowed disabled:opacity-40"
                                            :disabled="line.quantity >= 99"
                                            aria-label="Increase quantity"
                                            @click="increment(line.id)"
                                        >
                                            <Plus class="h-4 w-4" />
                                        </button>
                                    </div>

                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-1.5 rounded-lg px-2 py-1.5 text-sm font-medium text-muted-foreground transition hover:bg-destructive/10 hover:text-destructive"
                                        aria-label="Remove item"
                                        @click="removeLine(line.id)"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                        <span class="hidden sm:inline"
                                            >Remove</span
                                        >
                                    </button>
                                </div>
                            </div>

                            <div
                                class="flex shrink-0 flex-col items-end justify-between self-stretch sm:min-w-22"
                            >
                                <p
                                    class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
                                >
                                    Subtotal
                                </p>
                                <p
                                    class="text-lg font-bold text-primary tabular-nums"
                                >
                                    {{ formatMoney(lineSubtotal(line)) }}
                                </p>
                            </div>
                        </article>
                    </div>

                    <!-- Order summary -->
                    <aside class="lg:sticky lg:top-24 lg:self-start">
                        <div
                            class="rounded-2xl border border-border bg-card p-6 shadow-sm"
                        >
                            <h2 class="text-lg font-semibold text-foreground">
                                Order summary
                            </h2>

                            <dl
                                class="mt-6 space-y-3 border-b border-border pb-6 text-sm"
                            >
                                <div class="flex justify-between gap-4">
                                    <dt class="text-muted-foreground">
                                        Subtotal
                                    </dt>
                                    <dd
                                        class="font-medium text-foreground tabular-nums"
                                    >
                                        {{ formatMoney(subtotal) }}
                                    </dd>
                                </div>
                                <div
                                    v-if="discountAmount > 0"
                                    class="flex justify-between gap-4"
                                >
                                    <dt class="text-muted-foreground">
                                        Discount (SAVE10)
                                    </dt>
                                    <dd
                                        class="font-medium text-primary tabular-nums"
                                    >
                                        −{{ formatMoney(discountAmount) }}
                                    </dd>
                                </div>
                                <div class="flex justify-between gap-4">
                                    <dt class="text-muted-foreground">
                                        Shipping
                                    </dt>
                                    <dd
                                        class="font-medium text-foreground tabular-nums"
                                    >
                                        {{
                                            shipping === 0
                                                ? 'Free'
                                                : formatMoney(shipping)
                                        }}
                                    </dd>
                                </div>
                                <div class="flex justify-between gap-4">
                                    <dt class="text-muted-foreground">
                                        Estimated tax
                                    </dt>
                                    <dd
                                        class="font-medium text-foreground tabular-nums"
                                    >
                                        {{ formatMoney(estimatedTax) }}
                                    </dd>
                                </div>
                            </dl>

                            <div
                                class="flex items-baseline justify-between gap-4 pt-2"
                            >
                                <span
                                    class="text-base font-semibold text-foreground"
                                    >Total</span
                                >
                                <span
                                    class="text-2xl font-bold text-primary tabular-nums"
                                >
                                    {{ formatMoney(orderTotal) }}
                                </span>
                            </div>

                            <div class="mt-6 space-y-2">
                                <label
                                    for="promo-code"
                                    class="text-xs font-medium text-muted-foreground"
                                >
                                    Promo code
                                </label>
                                <div class="flex gap-2">
                                    <input
                                        id="promo-code"
                                        v-model="promoInput"
                                        type="text"
                                        placeholder="Enter code"
                                        autocomplete="off"
                                        class="min-w-0 flex-1 rounded-xl border border-border bg-background px-3 py-2.5 text-sm text-foreground placeholder:text-muted-foreground focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none"
                                        @keydown.enter.prevent="applyPromo"
                                    />
                                    <button
                                        type="button"
                                        class="shrink-0 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90"
                                        @click="applyPromo"
                                    >
                                        Apply
                                    </button>
                                </div>
                                <p
                                    v-if="promoFeedback === 'success'"
                                    class="text-xs font-medium text-primary"
                                >
                                    Promo applied — 10% off your subtotal.
                                </p>
                                <p
                                    v-else-if="promoFeedback === 'invalid'"
                                    class="text-xs text-destructive"
                                >
                                    Invalid code. Try
                                    <span class="font-mono">SAVE10</span>.
                                </p>
                            </div>

                            <button
                                type="button"
                                class="mt-6 w-full rounded-xl bg-primary py-3.5 text-base font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90"
                            >
                                Proceed to checkout
                            </button>
                            <p
                                class="mt-3 text-center text-xs text-muted-foreground"
                            >
                                Secure checkout · Free returns within 30 days
                            </p>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </main>
</template>
