<script setup>
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Head, Link } from '@inertiajs/vue3';
import {
    Minus,
    Plus,
    ShoppingBag,
    ShoppingCart,
    Trash2,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

// UI-only mock cart items
const items = ref([
    {
        id: 1,
        name: 'Wireless Earbuds Pro',
        price: 79.99,
        quantity: 1,
        image_url: null,
        slug: 'wireless-earbuds-pro',
    },
    {
        id: 2,
        name: 'USB-C Hub 7-in-1',
        price: 72.99,
        quantity: 1,
        image_url: null,
        slug: 'usb-c-hub',
    },
    {
        id: 3,
        name: 'Laptop Sleeve',
        price: 45.0,
        quantity: 1,
        image_url: null,
        slug: 'laptop-sleeve',
    },
]);

const isEmpty = computed(() => items.value.length === 0);
const totalItems = computed(() =>
    items.value.reduce((sum, item) => sum + item.quantity, 0),
);
const subtotal = computed(() =>
    items.value.reduce((sum, item) => sum + item.price * item.quantity, 0),
);
const shippingEstimate = computed(() => (subtotal.value >= 100 ? 0 : 9.99));
const total = computed(() => subtotal.value + shippingEstimate.value);

function formatMoney(amount) {
    if (amount == null) return '—';
    return `$${Number(amount).toFixed(2)}`;
}

function lineTotal(item) {
    return item.price * item.quantity;
}

function updateQuantity(id, delta) {
    const item = items.value.find((i) => i.id === id);
    if (!item) return;
    const next = item.quantity + delta;
    if (next < 1) return;
    item.quantity = next;
}

function setQuantity(id, value) {
    const num = Number.parseInt(value, 10);
    if (Number.isNaN(num) || num < 1) return;
    const item = items.value.find((i) => i.id === id);
    if (item) item.quantity = num;
}

function removeItem(id) {
    items.value = items.value.filter((i) => i.id !== id);
}
</script>

<template>
    <Head title="Shopping Cart" />
    <div
        class="min-h-screen bg-background text-foreground dark:bg-slate-950 dark:text-slate-100"
    >
        <div class="container mx-auto px-4 py-8 sm:px-6 lg:px-8">
            <!-- Header -->
            <header class="mb-6 flex flex-col gap-1">
                <h1
                    class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl"
                >
                    Shopping Cart
                </h1>
                <p class="text-sm text-muted-foreground">
                    <template v-if="!isEmpty">
                        {{ totalItems }}
                        {{ totalItems === 1 ? 'item' : 'items' }} in your cart
                    </template>
                    <template v-else> Your cart is empty </template>
                </p>
            </header>

            <template v-if="!isEmpty">
                <div class="grid gap-8 lg:grid-cols-[1fr_360px]">
                    <!-- Cart items list -->
                    <div class="space-y-4">
                        <Card
                            v-for="item in items"
                            :key="item.id"
                            class="overflow-hidden border-border bg-card shadow-sm"
                        >
                            <CardContent class="p-0">
                                <div
                                    class="flex flex-col gap-4 p-4 sm:flex-row sm:items-center sm:gap-6"
                                >
                                    <!-- Product image -->
                                    <div
                                        class="flex size-24 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-muted sm:size-28"
                                    >
                                        <img
                                            v-if="item.image_url"
                                            :src="item.image_url"
                                            :alt="item.name"
                                            class="size-full object-cover"
                                        />
                                        <ShoppingBag
                                            v-else
                                            class="size-10 text-muted-foreground opacity-50"
                                            aria-hidden="true"
                                        />
                                    </div>

                                    <!-- Details + quantity + actions -->
                                    <div class="min-w-0 flex-1 space-y-3">
                                        <div
                                            class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between"
                                        >
                                            <div>
                                                <h2
                                                    class="font-semibold text-foreground"
                                                >
                                                    {{ item.name }}
                                                </h2>
                                                <p
                                                    class="text-lg font-semibold text-foreground"
                                                >
                                                    {{
                                                        formatMoney(item.price)
                                                    }}
                                                </p>
                                            </div>
                                            <p
                                                class="text-lg font-semibold text-foreground sm:shrink-0"
                                            >
                                                {{
                                                    formatMoney(lineTotal(item))
                                                }}
                                            </p>
                                        </div>

                                        <div
                                            class="flex flex-wrap items-center justify-between gap-3"
                                        >
                                            <!-- Quantity controls -->
                                            <div
                                                class="flex items-center gap-1 rounded-md border border-input bg-background"
                                            >
                                                <Button
                                                    type="button"
                                                    variant="ghost"
                                                    size="icon"
                                                    class="size-9 shrink-0 rounded-r-none text-foreground hover:bg-accent"
                                                    aria-label="Decrease quantity"
                                                    @click="
                                                        updateQuantity(
                                                            item.id,
                                                            -1,
                                                        )
                                                    "
                                                >
                                                    <Minus
                                                        class="size-4"
                                                        aria-hidden="true"
                                                    />
                                                </Button>
                                                <Input
                                                    type="number"
                                                    min="1"
                                                    :model-value="item.quantity"
                                                    class="h-9 w-14 border-0 bg-transparent text-center text-sm [-moz-appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                                                    @update:model-value="
                                                        (v) =>
                                                            setQuantity(
                                                                item.id,
                                                                v,
                                                            )
                                                    "
                                                />
                                                <Button
                                                    type="button"
                                                    variant="ghost"
                                                    size="icon"
                                                    class="size-9 shrink-0 rounded-l-none text-foreground hover:bg-accent"
                                                    aria-label="Increase quantity"
                                                    @click="
                                                        updateQuantity(
                                                            item.id,
                                                            1,
                                                        )
                                                    "
                                                >
                                                    <Plus
                                                        class="size-4"
                                                        aria-hidden="true"
                                                    />
                                                </Button>
                                            </div>

                                            <Button
                                                type="button"
                                                variant="ghost"
                                                size="sm"
                                                class="text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
                                                aria-label="Remove from cart"
                                                @click="removeItem(item.id)"
                                            >
                                                <Trash2
                                                    class="mr-1.5 size-4"
                                                    aria-hidden="true"
                                                />
                                                Remove
                                            </Button>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>

                    <!-- Order summary -->
                    <div class="lg:sticky lg:top-24 lg:self-start">
                        <Card class="border-border bg-card shadow-sm">
                            <CardHeader>
                                <h2
                                    class="text-lg font-semibold text-foreground"
                                >
                                    Order Summary
                                </h2>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <div class="flex justify-between text-sm">
                                    <span class="text-muted-foreground"
                                        >Subtotal</span
                                    >
                                    <span class="font-medium text-foreground">
                                        {{ formatMoney(subtotal) }}
                                    </span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-muted-foreground"
                                        >Shipping</span
                                    >
                                    <span class="font-medium text-foreground">
                                        {{
                                            shippingEstimate === 0
                                                ? 'Free'
                                                : formatMoney(shippingEstimate)
                                        }}
                                    </span>
                                </div>
                                <div
                                    class="flex justify-between border-t border-border pt-4 text-base font-semibold text-foreground"
                                >
                                    <span>Total</span>
                                    <span>{{ formatMoney(total) }}</span>
                                </div>
                                <Button
                                    class="w-full bg-primary text-primary-foreground hover:bg-primary/90"
                                    as-child
                                >
                                    <Link href="#">Proceed to Checkout</Link>
                                </Button>
                                <Button
                                    variant="outline"
                                    class="w-full border-border"
                                    as-child
                                >
                                    <Link :href="route('products')">
                                        Continue Shopping
                                    </Link>
                                </Button>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </template>

            <!-- Empty state -->
            <Card
                v-else
                class="border-border bg-card py-16 text-center shadow-sm"
            >
                <CardContent class="flex flex-col items-center gap-6 px-6">
                    <div
                        class="flex size-20 items-center justify-center rounded-full bg-muted text-muted-foreground sm:size-24"
                    >
                        <ShoppingCart
                            class="size-10 sm:size-12"
                            aria-hidden="true"
                        />
                    </div>
                    <div class="space-y-1">
                        <h2 class="text-lg font-semibold text-foreground">
                            Your cart is empty
                        </h2>
                        <p class="text-sm text-muted-foreground">
                            Add items from the store to get started.
                        </p>
                    </div>
                    <Button
                        size="sm"
                        class="bg-primary text-primary-foreground hover:bg-primary/90"
                        as-child
                    >
                        <Link :href="route('products')">
                            Continue Shopping
                        </Link>
                    </Button>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
