<script setup>
const categories = [
    {
        id: 1,
        name: 'Electronics',
        icon: '📱',
    },
    {
        id: 2,
        name: 'Fashion',
        icon: '👕',
    },
    {
        id: 3,
        name: 'Home & Garden',
        icon: '🏠',
    },
    {
        id: 4,
        name: 'Sports',
        icon: '⚽',
    },
    {
        id: 5,
        name: 'Books',
        icon: '📚',
    },
    { id: 6, name: 'Toys', icon: '🧸' },
    {
        id: 7,
        name: 'Beauty',
        icon: '💄',
    },
    {
        id: 8,
        name: 'Automotive',
        icon: '🚗',
    },
];
</script>
<template>
    <section
        id="categories"
        class="container mx-auto px-4 py-12 sm:px-6 lg:px-8"
        aria-label="Product categories"
    >
        <h2
            class="mb-6 text-center text-2xl font-bold sm:text-3xl"
        >
            Shop by Category
        </h2>
        <div
            class="scrollbar-hide flex gap-4 overflow-x-auto pb-4"
            style="
                scrollbar-width: none;
                -ms-overflow-style: none;
            "
        >
            <div
                v-for="category in categories"
                :key="category.id"
                class="group shrink-0 cursor-pointer"
            >
                <div
                    class="bg-card border-border hover:border-primary flex min-w-[120px] flex-col items-center gap-3 rounded-2xl border p-6 transition-all hover:shadow-lg"
                >
                    <div
                        class="text-4xl transition-transform group-hover:scale-110"
                        role="img"
                        :aria-label="
                            category.name
                        "
                    >
                        {{
                            category.icon
                        }}
                    </div>
                    <span
                        class="text-center text-sm font-medium"
                    >
                        {{
                            category.name
                        }}
                    </span>
                </div>
            </div>
        </div>
    </section>
</template>
