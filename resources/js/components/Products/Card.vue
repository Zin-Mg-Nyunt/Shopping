<script setup>
import { route } from 'ziggy-js';
import { router } from '@inertiajs/vue3';

const { products } = defineProps({
    products: Array,
});

const addToCard = (product) => {
    alert(product.name);
};
</script>

<template>
    <article
        v-for="product in products"
        :key="product.id"
        class="group relative cursor-pointer overflow-hidden rounded-2xl border border-border bg-card p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
        @click="router.visit(route('product.detail', { slug: product.slug }))"
    >
        <button
            type="button"
            class="absolute top-4 right-4 rounded-full border border-border bg-card p-1.5 text-muted-foreground transition hover:border-primary/40 hover:text-primary"
        >
            <span class="sr-only">Add to wishlist</span>
            <svg
                class="h-4 w-4"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
            >
                <path
                    d="M12 20s-7-4.4-9-8.6C1.4 8.1 3.1 5 6.3 5A5 5 0 0 1 12 8a5 5 0 0 1 5.7-3c3.2 0 4.9 3.1 3.3 6.4-2 4.2-9 8.6-9 8.6Z"
                />
            </svg>
        </button>

        <div class="mb-4 aspect-4/5 rounded-xl bg-muted">
            <img
                :src="product.thumbnail"
                alt="Product Image"
                class="h-full w-full object-cover"
            />
        </div>

        <div class="space-y-2">
            <h3 class="text-sm font-semibold text-foreground">
                {{ product.title }}
            </h3>
            <p class="truncate text-sm text-muted-foreground">
                {{ product.description }}
            </p>
            <p class="text-lg font-bold text-primary">
                {{ product.price }}
            </p>
        </div>

        <button
            type="button"
            class="mt-4 w-full rounded-full border border-primary bg-card px-4 py-2 text-sm font-semibold text-primary transition hover:bg-primary hover:text-primary-foreground"
            @click.stop="addToCard(product)"
        >
            Add to Cart
        </button>
    </article>
</template>
