<script setup>
    const categories = [
    { id: 1, name: 'Electronics', icon: '📱' },
    { id: 2, name: 'Fashion', icon: '👕' },
    { id: 3, name: 'Home & Garden', icon: '🏠' },
    { id: 4, name: 'Sports', icon: '⚽' },
    { id: 5, name: 'Books', icon: '📚' },
    { id: 6, name: 'Toys', icon: '🧸' },
    { id: 7, name: 'Beauty', icon: '💄' },
    { id: 8, name: 'Automotive', icon: '🚗' },
];
</script>
<template>
  <section
            id="categories"
            class="container mx-auto px-4 sm:px-6 lg:px-8 py-12"
            aria-label="Product categories"
        >
            <h2 class="text-2xl sm:text-3xl font-bold mb-6 text-center">
                Shop by Category
            </h2>
            <div
                class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide"
                style="scrollbar-width: none; -ms-overflow-style: none"
            >
                <div
                    v-for="category in categories"
                    :key="category.id"
                    class="shrink-0 group cursor-pointer"
                >
                    <div
                        class="flex flex-col items-center gap-3 p-6 rounded-2xl bg-card border border-border hover:border-primary transition-all hover:shadow-lg min-w-[120px]"
                    >
                        <div
                            class="text-4xl transition-transform group-hover:scale-110"
                            role="img"
                            :aria-label="category.name"
                        >
                            {{ category.icon }}
                        </div>
                        <span class="text-sm font-medium text-center">
                            {{ category.name }}
                        </span>
                    </div>
                </div>
            </div>
        </section>
</template>