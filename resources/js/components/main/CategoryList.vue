<script setup>
import { useFilter } from '@/composables/useFilter';
const { categories, url } = defineProps({
    categories: Array,
    url: String,
});
const { activeCategory, filterBy } = useFilter();
</script>
<template>
    <section
        id="categories"
        class="container mx-auto px-4 py-12 sm:px-6 lg:px-8"
        aria-label="Product categories"
    >
        <h2 class="mb-6 text-center text-2xl font-bold sm:text-3xl">
            Shop by Category
        </h2>
        <div
            class="mb-8 overflow-x-auto pb-2"
            style="scrollbar-width: none; -ms-overflow-style: none"
        >
            <div class="flex w-max shrink-0 gap-2 sm:gap-3">
                <button
                    type="button"
                    :class="[
                        'rounded-full px-4 py-2 text-sm font-medium transition-all duration-200',
                        activeCategory == 'all'
                            ? 'bg-primary text-white shadow-md dark:bg-primary dark:text-white'
                            : 'bg-muted/80 text-muted-foreground hover:bg-muted hover:text-foreground dark:bg-slate-800/80 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-slate-100',
                    ]"
                    @click="filterBy(url, 'category')"
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
                    @click="filterBy(url, 'category', cat.slug)"
                >
                    {{ cat.name }}
                </button>
            </div>
        </div>
    </section>
</template>
