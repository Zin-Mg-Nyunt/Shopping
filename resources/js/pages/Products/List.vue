<script setup lang="ts">
import { ref } from 'vue';
import ProductsCard from '@/components/Products/Card.vue';

const minPrice = 0;
const maxPrice = 1000;
const selectedPrice = ref(20);

function clampPrice(value: number): number {
    if (Number.isNaN(value)) {
        return minPrice;
    }
    return Math.min(maxPrice, Math.max(minPrice, value));
}

function updatePriceFromInput(event: Event): void {
    const target = event.target as HTMLInputElement;
    const parsedValue = Number(target?.value);
    selectedPrice.value = clampPrice(parsedValue);
}

const products = [
    {
        id: 1,
        name: 'Aero Running Shoes',
        description: 'Lightweight daily trainers with breathable mesh comfort.',
        price: '$129.00',
    },
    {
        id: 2,
        name: 'Classic Leather Backpack',
        description: 'Minimal silhouette with premium full-grain leather.',
        price: '$179.00',
    },
    {
        id: 3,
        name: 'Smart Fitness Watch',
        description: 'Track performance, sleep, and wellness all day long.',
        price: '$249.00',
    },
    {
        id: 4,
        name: 'Noise-Canceling Headphones',
        description: 'Immersive sound in a clean and comfortable design.',
        price: '$299.00',
    },
    {
        id: 5,
        name: 'Portable Espresso Maker',
        description: 'Brew cafe-quality coffee anywhere in minutes.',
        price: '$89.00',
    },
    {
        id: 6,
        name: 'Organic Cotton Hoodie',
        description: 'Soft everyday layer with a timeless modern cut.',
        price: '$74.00',
    },
    {
        id: 7,
        name: 'Desk Organizer Set',
        description: 'Keep your workspace tidy with modular essentials.',
        price: '$59.00',
    },
    {
        id: 8,
        name: 'Ceramic Water Bottle',
        description: 'Insulated and sleek with a clean matte finish.',
        price: '$45.00',
    },
];
</script>

<template>
    <main class="bg-background">
        <div class="mx-auto w-full max-w-7xl px-6 py-10 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-[280px_minmax(0,1fr)]">
                <aside
                    class="h-fit rounded-2xl border border-border bg-card p-6 shadow-sm"
                >
                    <h2 class="text-lg font-semibold text-foreground">Filters</h2>

                    <div class="mt-6 space-y-6">
                        <section>
                            <h3 class="text-sm font-semibold text-foreground">
                                Categories
                            </h3>
                            <div class="mt-3 flex flex-col gap-2">
                                <button
                                    class="w-fit rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary"
                                >
                                    Sneakers
                                </button>
                                <label
                                    class="flex items-center gap-2 text-sm text-muted-foreground"
                                >
                                    <input
                                        type="checkbox"
                                        class="accent-primary"
                                    />
                                    Apparel
                                </label>
                                <label
                                    class="flex items-center gap-2 text-sm text-muted-foreground"
                                >
                                    <input
                                        type="checkbox"
                                        class="accent-primary"
                                    />
                                    Electronics
                                </label>
                                <label
                                    class="flex items-center gap-2 text-sm text-muted-foreground"
                                >
                                    <input
                                        type="checkbox"
                                        class="accent-primary"
                                    />
                                    Home & Living
                                </label>
                            </div>
                        </section>

                        <section>
                            <h3 class="text-sm font-semibold text-foreground">
                                Price Range
                            </h3>
                            <div class="mt-3 space-y-2">
                                <div class="flex items-center gap-3">
                                    <div>
                                        <input
                                            v-model.number="selectedPrice"
                                            type="range"
                                            :min="minPrice"
                                            :max="maxPrice"
                                            class="accent-primary"
                                        />
                                        <div
                                            class="flex items-center justify-between text-xs text-muted-foreground"
                                        >
                                            <span>${{ minPrice }}</span>
                                            <span>${{ maxPrice }}</span>
                                        </div>
                                    </div>
                                    <div class="relative w-24 shrink-0">
                                        <span
                                            class="pointer-events-none absolute top-1/2 left-3 -translate-y-1/2 text-xs text-muted-foreground"
                                        >
                                            $
                                        </span>
                                        <input
                                            :value="selectedPrice"
                                            type="number"
                                            :min="minPrice"
                                            :max="maxPrice"
                                            class="w-full rounded-lg border border-border bg-background py-1.5 pr-2 pl-6 text-sm text-foreground focus:border-primary focus:outline-none"
                                            @input="updatePriceFromInput"
                                            @blur="updatePriceFromInput"
                                        />
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section>
                            <h3 class="text-sm font-semibold text-foreground">
                                Ratings
                            </h3>
                            <div
                                class="mt-3 flex flex-col gap-2 text-sm text-muted-foreground"
                            >
                                <label class="flex items-center gap-2">
                                    <input
                                        type="radio"
                                        name="rating"
                                        class="accent-primary"
                                        checked
                                    />
                                    4 stars & up
                                </label>
                                <label class="flex items-center gap-2">
                                    <input
                                        type="radio"
                                        name="rating"
                                        class="accent-primary"
                                    />
                                    3 stars & up
                                </label>
                                <label class="flex items-center gap-2">
                                    <input
                                        type="radio"
                                        name="rating"
                                        class="accent-primary"
                                    />
                                    2 stars & up
                                </label>
                            </div>
                        </section>

                        <section>
                            <h3 class="text-sm font-semibold text-foreground">
                                Brands
                            </h3>
                            <div
                                class="mt-3 flex flex-col gap-2 text-sm text-muted-foreground"
                            >
                                <label class="flex items-center gap-2">
                                    <input
                                        type="checkbox"
                                        class="accent-primary"
                                        checked
                                    />
                                    Nova
                                </label>
                                <label class="flex items-center gap-2">
                                    <input
                                        type="checkbox"
                                        class="accent-primary"
                                    />
                                    Urban Line
                                </label>
                                <label class="flex items-center gap-2">
                                    <input
                                        type="checkbox"
                                        class="accent-primary"
                                    />
                                    Peak Studio
                                </label>
                            </div>
                        </section>
                    </div>
                </aside>

                <section class="space-y-6">
                    <div
                        class="flex flex-col gap-3 rounded-2xl border border-border bg-card px-5 py-4 shadow-sm sm:flex-row sm:items-center sm:justify-between"
                    >
                        <p class="text-sm text-muted-foreground">
                            Showing 1-20 of 150 products
                        </p>

                        <div class="flex items-center gap-2">
                            <label for="sort" class="text-sm text-muted-foreground"
                                >Sort By</label
                            >
                            <select
                                id="sort"
                                class="rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:border-primary focus:outline-none"
                            >
                                <option>Newest</option>
                                <option>Price: Low to High</option>
                                <option>Price: High to Low</option>
                                <option>Top Rated</option>
                            </select>
                        </div>
                    </div>

                    <div
                        class="grid grid-cols-2 gap-5 sm:grid-cols-3 xl:grid-cols-4"
                    >
                        <ProductsCard :products="products" />
                    </div>

                    <nav class="flex items-center justify-center gap-2 pt-2">
                        <button
                            type="button"
                            class="rounded-lg border border-border bg-card px-3 py-2 text-sm text-muted-foreground transition hover:border-primary/40 hover:text-primary"
                        >
                            Prev
                        </button>
                        <button
                            type="button"
                            class="rounded-lg bg-primary px-3 py-2 text-sm font-semibold text-primary-foreground"
                        >
                            1
                        </button>
                        <button
                            type="button"
                            class="rounded-lg border border-border bg-card px-3 py-2 text-sm text-foreground transition hover:border-primary/40 hover:text-primary"
                        >
                            2
                        </button>
                        <button
                            type="button"
                            class="rounded-lg border border-border bg-card px-3 py-2 text-sm text-foreground transition hover:border-primary/40 hover:text-primary"
                        >
                            3
                        </button>
                        <span class="px-1 text-muted-foreground">...</span>
                        <button
                            type="button"
                            class="rounded-lg border border-border bg-card px-3 py-2 text-sm text-foreground transition hover:border-primary/40 hover:text-primary"
                        >
                            8
                        </button>
                        <button
                            type="button"
                            class="rounded-lg border border-border bg-card px-3 py-2 text-sm text-muted-foreground transition hover:border-primary/40 hover:text-primary"
                        >
                            Next
                        </button>
                    </nav>
                </section>
            </div>
        </div>
    </main>
</template>
