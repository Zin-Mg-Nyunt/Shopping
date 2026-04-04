<script setup>
import ProductsCard from '@/components/Products/Card.vue';
import { route } from 'ziggy-js';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const categories = page.props.categories;
const { products } = defineProps({
    products: Array,
});
</script>
<template>
    <main
        class="mx-auto flex w-full max-w-7xl flex-col gap-16 px-6 py-10 lg:px-8 lg:py-14"
    >
        <section
            class="overflow-hidden rounded-3xl border border-border bg-card shadow-sm"
        >
            <div class="grid gap-8 p-8 md:grid-cols-2 md:items-center md:p-12">
                <div class="space-y-5">
                    <p
                        class="text-sm font-medium tracking-[0.2em] text-primary uppercase"
                    >
                        New season drop
                    </p>
                    <h1
                        class="text-3xl leading-tight font-semibold text-foreground md:text-5xl"
                    >
                        Minimal design.
                        <span class="text-primary">Maximum comfort.</span>
                    </h1>
                    <p
                        class="max-w-md text-base leading-relaxed text-muted-foreground"
                    >
                        Discover curated essentials for your everyday lifestyle
                        with clean silhouettes and premium quality.
                    </p>
                    <div class="flex flex-wrap items-center gap-3">
                        <a
                            href="#"
                            class="rounded-full bg-primary px-6 py-3 text-sm font-semibold text-primary-foreground transition hover:bg-primary/90"
                        >
                            Shop Now
                        </a>
                        <a
                            href="#"
                            class="text-sm font-semibold text-primary transition hover:text-primary/90"
                        >
                            View Collection
                        </a>
                    </div>
                </div>
                <div class="rounded-2xl bg-muted p-8">
                    <div
                        class="mx-auto aspect-square max-w-sm rounded-2xl bg-card p-6 shadow-sm"
                    >
                        <div
                            class="h-full rounded-xl bg-linear-to-br from-muted to-border"
                        ></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="space-y-6">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-primary">
                        Handpicked for you
                    </p>
                    <h2 class="text-2xl font-semibold text-foreground">
                        Featured Products
                    </h2>
                </div>
                <a
                    :href="route('products.list')"
                    class="text-sm font-semibold text-primary hover:text-primary/90"
                    >See All</a
                >
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <ProductsCard :products="products" />
            </div>
        </section>

        <section class="space-y-6">
            <div>
                <p class="text-sm font-medium text-primary">Browse quickly</p>
                <h2 class="text-2xl font-semibold text-foreground">
                    Shop by Category
                </h2>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <article
                    v-for="category in categories"
                    :key="category.id"
                    class="group rounded-2xl border border-border bg-card p-5 shadow-sm transition hover:border-primary/40"
                >
                    <div
                        class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10 text-primary transition group-hover:bg-primary/20"
                    >
                        <svg
                            class="h-6 w-6"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <rect x="4" y="4" width="16" height="16" rx="3" />
                            <path d="M8 12h8M12 8v8" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-foreground">
                        {{ category.name }}
                    </h3>
                    <a
                        href="#"
                        class="mt-2 inline-block text-sm font-medium text-primary hover:text-primary/90"
                    >
                        Explore
                    </a>
                </article>
            </div>
        </section>
    </main>
</template>
