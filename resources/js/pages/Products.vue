<script setup>
import ProductCard from '@/components/main/ProductCard.vue';
import { useFilter } from '@/composables/useFilter';
import { Head, Link } from '@inertiajs/vue3';
import { ChevronRight, Star } from 'lucide-vue-next';
import { ref } from 'vue';

const selectedRating = ref(4);

const starOptions = [4, 3, 2, 1];

const { products, categories, brands } = defineProps({
    products: Array,
    categories: Array,
    brands: Array,
});
const sortOptions = [
    {
        value: 'default',
        label: 'Latest (Default)',
    },
    {
        value: 'oldest',
        label: 'Oldest',
    },
    {
        value: 'price_asc',
        label: 'Price: Low to High',
    },
    {
        value: 'price_desc',
        label: 'Price: High to Low',
    },
];

const { activeCategory, filterBy, sortBy, priceMax, brand } = useFilter();
</script>

<template>
    <Head title="Products" />
    <div
        class="min-h-screen bg-background text-foreground dark:bg-slate-950 dark:text-slate-100"
    >
        <div class="container mx-auto px-4 py-8 sm:px-6 lg:px-8">
            <!-- Header & Breadcrumbs -->
            <nav
                class="mb-6 flex items-center gap-1.5 text-sm text-muted-foreground transition-colors dark:text-slate-400"
                aria-label="Breadcrumb"
            >
                <Link
                    :href="route('home')"
                    class="inline-flex items-center gap-1.5 rounded px-1 py-0.5 transition-colors hover:text-foreground focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:outline-none dark:hover:text-slate-100"
                >
                    Home
                </Link>
                <ChevronRight
                    class="size-4 shrink-0 text-muted-foreground/60 dark:text-slate-500"
                    aria-hidden="true"
                />
                <span
                    class="font-medium text-foreground dark:text-slate-100"
                    aria-current="page"
                >
                    Products
                </span>
            </nav>

            <h1
                class="mb-8 font-serif text-3xl font-bold tracking-tight text-foreground sm:text-4xl dark:text-slate-50"
            >
                All Products
            </h1>

            <!-- Category Pills -->
            <div class="mb-8 overflow-x-auto pb-2">
                <div
                    class="flex w-max shrink-0 gap-2 sm:w-auto sm:flex-wrap sm:gap-3"
                >
                    <button
                        type="button"
                        :class="[
                            'rounded-full px-4 py-2 text-sm font-medium transition-all duration-200',
                            activeCategory == 'all'
                                ? 'bg-primary text-white shadow-md dark:bg-primary dark:text-white'
                                : 'bg-muted/80 text-muted-foreground hover:bg-muted hover:text-foreground dark:bg-slate-800/80 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-slate-100',
                        ]"
                        @click="filterBy('category')"
                    >
                        All
                    </button>
                    <button
                        v-for="cat in categories"
                        :key="cat.id"
                        type="button"
                        :class="[
                            'rounded-full px-4 py-2 text-sm font-medium transition-all duration-200',
                            activeCategory == cat.slug
                                ? 'bg-primary text-white shadow-md dark:bg-primary dark:text-white'
                                : 'bg-muted/80 text-muted-foreground hover:bg-muted hover:text-foreground dark:bg-slate-800/80 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-slate-100',
                        ]"
                        @click="filterBy('category', cat.slug)"
                    >
                        {{ cat.name }}
                    </button>
                </div>
            </div>

            <!-- Split Layout: Sidebar + Grid -->
            <div class="flex flex-col gap-8 lg:flex-row">
                <!-- Sidebar (Design Only) -->
                <aside
                    class="w-full shrink-0 lg:w-64 xl:w-72"
                    aria-label="Filters"
                >
                    <div
                        class="rounded-xl border border-border bg-card p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900/50"
                    >
                        <h2
                            class="mb-4 font-semibold text-foreground dark:text-slate-100"
                        >
                            Filters
                        </h2>

                        <!-- Brand -->
                        <div class="mb-6">
                            <label
                                for="filter-brand"
                                class="mb-2 block text-sm font-medium text-muted-foreground dark:text-slate-400"
                            >
                                Brand
                            </label>
                            <select
                                id="filter-brand"
                                v-model="brand"
                                @change="filterBy('brand', brand)"
                                class="w-full cursor-pointer appearance-none rounded-lg border border-border bg-background px-3 py-2 pr-9 text-sm text-foreground transition-colors focus:border-primary focus:outline-none dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:focus:border-primary"
                            >
                                <option
                                    :value="null"
                                    selected
                                >
                                    All brands
                                </option>
                                <option
                                    v-for="brand in brands"
                                    :key="brand.id"
                                    :value="brand.slug"
                                >
                                    {{ brand.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Price Slider -->
                        <div class="mb-6">
                            <label
                                for="price-range"
                                class="mb-2 block text-sm font-medium text-muted-foreground dark:text-slate-400"
                            >
                                Max price
                            </label>
                            <div class="flex items-center gap-3">
                                <input
                                    id="price-range"
                                    v-model.number="priceMax"
                                    @change="filterBy('price', priceMax)"
                                    type="range"
                                    min="0"
                                    max="2000"
                                    step="10"
                                    class="price-slider h-2 w-full cursor-pointer appearance-none rounded-full bg-muted dark:bg-slate-800 [&::-moz-range-thumb]:h-4 [&::-moz-range-thumb]:w-4 [&::-moz-range-thumb]:rounded-full [&::-moz-range-thumb]:border-0 [&::-moz-range-thumb]:bg-primary [&::-moz-range-thumb]:transition-transform [&::-moz-range-thumb]:duration-200 [&::-webkit-slider-thumb]:h-4 [&::-webkit-slider-thumb]:w-4 [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:bg-primary [&::-webkit-slider-thumb]:shadow-md [&::-webkit-slider-thumb]:transition-all [&::-webkit-slider-thumb]:duration-200 [&::-webkit-slider-thumb]:hover:scale-110"
                                />
                                <input
                                    type="number"
                                    max="2000"
                                    v-model.number="priceMax"
                                    @input="filterBy('price', priceMax)"
                                    class="w-12 text-right text-sm font-medium text-foreground tabular-nums focus-visible:ring-0 focus-visible:outline-none dark:text-slate-200"
                                />
                            </div>
                        </div>

                        <!-- Star Rating -->
                        <div>
                            <p
                                class="mb-3 text-sm font-medium text-muted-foreground dark:text-slate-400"
                            >
                                Rating
                            </p>
                            <div class="space-y-2">
                                <button
                                    v-for="stars in starOptions"
                                    :key="stars"
                                    type="button"
                                    :class="[
                                        'flex w-full items-center gap-2 rounded-lg border px-3 py-2 text-left text-sm transition-all duration-200',
                                        selectedRating === stars
                                            ? 'border-primary bg-primary/10 text-foreground dark:border-primary dark:bg-primary/20 dark:text-slate-100'
                                            : 'border-border bg-transparent text-muted-foreground hover:border-muted-foreground/40 hover:bg-muted/50 hover:text-foreground dark:border-slate-800 dark:text-slate-400 dark:hover:border-slate-600 dark:hover:bg-slate-800/50 dark:hover:text-slate-200',
                                    ]"
                                    @click="
                                        selectedRating =
                                            selectedRating === stars
                                                ? null
                                                : stars
                                    "
                                >
                                    <span
                                        class="flex shrink-0 gap-0.5 text-amber-500 dark:text-amber-400"
                                        aria-hidden="true"
                                    >
                                        <Star
                                            v-for="i in 5"
                                            :key="i"
                                            :class="[
                                                'size-4 transition-colors',
                                                i <= stars
                                                    ? 'fill-current'
                                                    : 'fill-none text-muted-foreground/40 dark:text-slate-500',
                                            ]"
                                        />
                                    </span>
                                    <span class="font-medium"> & up </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Product Grid -->
                <div class="min-w-0 flex-1">
                    <div
                        class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <p
                            class="text-sm text-muted-foreground dark:text-slate-400"
                        >
                            {{ products.length }}
                            products
                        </p>
                        <div class="flex items-center gap-2">
                            <label
                                for="sort-by"
                                class="text-sm font-medium text-foreground dark:text-slate-200"
                            >
                                Sort by
                            </label>
                            <select
                                id="sort-by"
                                v-model="sortBy"
                                @change="
                                    filterBy('sortBy', $event.target.value)
                                "
                                class="min-w-44 cursor-pointer rounded-lg border border-border bg-background px-3 py-2 pr-9 text-sm text-foreground transition-colors focus:border-primary focus:outline-none dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:focus:border-primary"
                            >
                                <option
                                    v-for="opt in sortOptions"
                                    :key="opt.value"
                                    :value="opt.value"
                                >
                                    {{ opt.label }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <ProductCard :products="products" />
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.price-slider {
    --track-height: 0.5rem;
    --thumb-size: 1rem;
}
/* Firefox range thumb */
.price-slider::-moz-range-thumb {
    cursor: pointer;
}
.price-slider::-webkit-slider-thumb {
    cursor: pointer;
}
</style>
