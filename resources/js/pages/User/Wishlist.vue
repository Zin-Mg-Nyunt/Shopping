<script setup>
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import UserLayout from '@/layouts/UserLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Heart, ShoppingCart, Trash2, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';

defineOptions({
    name: 'UserWishlist',
    layout: UserLayout,
});

// UI-only mock wishlist items
const items = ref([
    {
        id: 1,
        name: 'Wireless Earbuds Pro',
        price: 79.99,
        original_price: 99.99,
        image_url: null,
        in_stock: true,
        slug: 'wireless-earbuds-pro',
    },
    {
        id: 2,
        name: 'Phone Stand',
        price: 34.99,
        original_price: null,
        image_url: null,
        in_stock: true,
        slug: 'phone-stand',
    },
    {
        id: 3,
        name: 'USB-C Hub 7-in-1',
        price: 72.99,
        original_price: 89.50,
        image_url: null,
        in_stock: true,
        slug: 'usb-c-hub',
    },
    {
        id: 4,
        name: 'Laptop Sleeve',
        price: 45.00,
        original_price: null,
        image_url: null,
        in_stock: false,
        slug: 'laptop-sleeve',
    },
    {
        id: 5,
        name: 'Monitor Arm',
        price: 165.00,
        original_price: 199.00,
        image_url: null,
        in_stock: true,
        slug: 'monitor-arm',
    },
]);

const totalCount = computed(() => items.value.length);
const isEmpty = computed(() => totalCount.value === 0);

function formatMoney(amount) {
    if (amount == null) return '—';
    return `$${Number(amount).toFixed(2)}`;
}

function hasDiscount(item) {
    return item.original_price != null && item.original_price > (item.price ?? 0);
}

function clearAll() {
    items.value = [];
}

function removeItem(id) {
    items.value = items.value.filter((i) => i.id !== id);
}

function addAllToCart() {
    // UI only – no-op or link to cart
}

function addToCart(item) {
    // UI only – no-op
}
</script>

<template>
    <Head title="My Wishlist" />
    <div class="space-y-6">
        <!-- Header -->
        <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1
                    class="font-serif text-2xl font-bold tracking-tight text-foreground sm:text-3xl"
                >
                    My Wishlist
                </h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    <span v-if="!isEmpty">{{ totalCount }} {{ totalCount === 1 ? 'Item' : 'Items' }}</span>
                    <span v-else>0 Items</span>
                </p>
            </div>
            <div
                v-if="!isEmpty"
                class="flex flex-wrap gap-2"
            >
                <Button
                    variant="outline"
                    size="sm"
                    class="border-border"
                    @click="clearAll"
                >
                    <Trash2 class="mr-2 size-4" aria-hidden="true" />
                    Clear All
                </Button>
                <Button
                    size="sm"
                    class="bg-primary text-primary-foreground hover:bg-primary/90"
                    @click="addAllToCart"
                >
                    <ShoppingCart class="mr-2 size-4" aria-hidden="true" />
                    Add All to Cart
                </Button>
            </div>
        </header>

        <!-- Product Grid -->
        <div
            v-if="!isEmpty"
            class="grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4"
        >
            <Card
                v-for="item in items"
                :key="item.id"
                class="overflow-hidden border-border bg-card shadow-sm transition-shadow hover:shadow-md"
            >
                <CardContent class="p-0">
                    <!-- Image with Remove (X) -->
                    <div class="relative aspect-square overflow-hidden bg-muted">
                        <img
                            v-if="item.image_url"
                            :src="item.image_url"
                            :alt="item.name"
                            class="size-full object-cover"
                        />
                        <div
                            v-else
                            class="flex size-full items-center justify-center text-muted-foreground"
                        >
                            <Heart class="size-12 opacity-50" aria-hidden="true" />
                        </div>
                        <button
                            type="button"
                            class="absolute right-2 top-2 flex size-8 items-center justify-center rounded-full border border-border bg-background/90 text-foreground shadow-sm transition-colors hover:bg-destructive hover:text-destructive-foreground focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none"
                            aria-label="Remove from wishlist"
                            @click="removeItem(item.id)"
                        >
                            <X class="size-4" aria-hidden="true" />
                        </button>
                    </div>

                    <!-- Details -->
                    <div class="flex flex-col gap-3 p-4">
                        <h2 class="line-clamp-2 font-semibold text-foreground">
                            {{ item.name }}
                        </h2>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-lg font-semibold text-foreground">
                                {{ formatMoney(item.price) }}
                            </span>
                            <span
                                v-if="hasDiscount(item)"
                                class="text-sm text-muted-foreground line-through"
                            >
                                {{ formatMoney(item.original_price) }}
                            </span>
                        </div>

                        <!-- Availability -->
                        <div>
                            <Badge
                                :class="
                                    item.in_stock
                                        ? 'border-transparent bg-emerald-500/15 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400'
                                        : 'border-transparent bg-muted text-muted-foreground'
                                "
                            >
                                {{ item.in_stock ? 'In Stock' : 'Out of Stock' }}
                            </Badge>
                        </div>

                        <!-- Add to Cart -->
                        <Button
                            variant="default"
                            size="sm"
                            class="w-full bg-primary text-primary-foreground hover:bg-primary/90"
                            :disabled="!item.in_stock"
                            @click="addToCart(item)"
                        >
                            <ShoppingCart class="mr-2 size-4" aria-hidden="true" />
                            Add to Cart
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Empty State -->
        <Card
            v-else
            class="border-border bg-card py-16 text-center shadow-sm"
        >
            <CardContent class="flex flex-col items-center gap-6 px-6">
                <div
                    class="flex size-20 items-center justify-center rounded-full bg-muted text-muted-foreground sm:size-24"
                >
                    <Heart class="size-10 sm:size-12" aria-hidden="true" />
                </div>
                <div class="space-y-1">
                    <h2 class="text-lg font-semibold text-foreground">
                        Your wishlist is empty
                    </h2>
                    <p class="text-sm text-muted-foreground">
                        Save items you like and add them to your cart later.
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
</template>
