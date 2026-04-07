<script setup>
import { ref, watch } from 'vue';
import ProductsCard from '@/components/Products/Card.vue';
import Pagination from '@/components/ui/Pagination.vue';
import useFilterBy from '@/composables/useFilterBy';
import { usePage } from '@inertiajs/vue3';

const { products } = defineProps({
    products: Object,
});

const minPrice = 0;
const maxPrice = 1000;
const page = usePage();
const selectedPrice = ref(route().params.price ?? 0);
const currentBrand = ref(null);
const currentCategory = ref(null);

function clampPrice(value) {
    if (isNaN(value)) {
        return minPrice;
    }
    return Math.min(maxPrice, Math.max(minPrice, value));
}

function updatePriceFromInput(event) {
    const target = event.target;
    const parsedValue = Number(target?.value);
    selectedPrice.value = clampPrice(parsedValue);
    if (target?.value) {
        target.value = selectedPrice.value;
    }
}

const { filterBy } = useFilterBy();

watch(
    () => page.url,
    () => {
        currentCategory.value = route().params.category ?? null;
        currentBrand.value = route().params.brand ?? null;
    },
    { deep: true, immediate: true },
);
watch(
    selectedPrice,
    () => {
        filterBy('price', selectedPrice.value);
    },
    { deep: true },
);
</script>

<template>
    <main class="bg-background">
        <div class="mx-auto w-full max-w-7xl px-6 py-10 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-[280px_minmax(0,1fr)]">
                <aside
                    class="h-fit rounded-2xl border border-border bg-card p-6 shadow-sm"
                >
                    <h2 class="text-lg font-semibold text-foreground">
                        Filters
                    </h2>

                    <div class="mt-6 space-y-6">
                        <section>
                            <h3 class="text-sm font-semibold text-foreground">
                                Categories
                            </h3>
                            <div class="mt-3 flex flex-col gap-2">
                                <button
                                    type="button"
                                    class="flex cursor-pointer items-center gap-2 text-sm text-muted-foreground hover:text-primary"
                                    :class="{
                                        'text-primary':
                                            currentCategory === null,
                                    }"
                                    @click="filterBy('category', null)"
                                >
                                    All
                                </button>
                                <button
                                    v-for="category in $page.props.categories"
                                    :key="category.id"
                                    type="button"
                                    class="flex cursor-pointer items-center gap-2 text-sm text-muted-foreground hover:text-primary"
                                    :class="{
                                        'text-primary':
                                            currentCategory === category.slug,
                                    }"
                                    @click="filterBy('category', category.slug)"
                                >
                                    {{ category.name }}
                                </button>
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
                                <button
                                    type="button"
                                    class="flex cursor-pointer items-center gap-2 text-sm text-muted-foreground hover:text-primary"
                                    :class="{
                                        'text-primary': currentBrand === null,
                                    }"
                                    @click="filterBy('brand', null)"
                                >
                                    All
                                </button>
                                <button
                                    v-for="brand in $page.props.brands"
                                    :key="brand.id"
                                    type="button"
                                    class="flex cursor-pointer items-center gap-2 text-sm text-muted-foreground hover:text-primary"
                                    :class="{
                                        'text-primary':
                                            currentBrand === brand.slug,
                                    }"
                                    @click="filterBy('brand', brand.slug)"
                                >
                                    {{ brand.name }}
                                </button>
                            </div>
                        </section>
                    </div>
                </aside>

                <section class="space-y-6">
                    <div
                        class="flex flex-col gap-3 rounded-2xl border border-border bg-card px-5 py-4 shadow-sm sm:flex-row sm:items-center sm:justify-between"
                    >
                        <p class="text-sm text-muted-foreground">
                            <template v-if="products.total">
                                Showing
                                {{ products.from }}–{{ products.to }} of
                                {{ products.total }} products
                            </template>
                            <template v-else> No products found </template>
                        </p>

                        <div class="flex items-center gap-2">
                            <label
                                for="sort"
                                class="text-sm text-muted-foreground"
                                >Sort By</label
                            >
                            <select
                                id="sort"
                                class="rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:border-primary focus:outline-none"
                                :value="route().params.sort ?? 'newest'"
                                @change="filterBy('sort', $event.target.value)"
                            >
                                <option value="newest">Newest</option>
                                <option value="low-to-high">
                                    Price: Low to High
                                </option>
                                <option value="high-to-low">
                                    Price: High to Low
                                </option>
                                <option value="top-rated">Top Rated</option>
                            </select>
                        </div>
                    </div>

                    <div
                        class="grid grid-cols-2 gap-5 sm:grid-cols-3 xl:grid-cols-4"
                    >
                        <ProductsCard :products="products.data" />
                    </div>

                    <Pagination :pagination="products" />
                </section>
            </div>
        </div>
    </main>
</template>
