<script setup>
import ProductCard from '@/components/main/ProductCard.vue';
import { Button } from '@/components/ui/button';
import { useFilter } from '@/composables/useFilter';

const { latestProducts } = defineProps({
    latestProducts: Array,
});
const popularProducts = [
    {
        id: 1,
        name: 'Wireless Headphones',
        price: 129.99,
        originalPrice: 179.99,
        rating: 4.5,
        reviews: 234,
        image: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop',
    },
    {
        id: 2,
        name: 'Smart Watch Pro',
        price: 299.99,
        originalPrice: 349.99,
        rating: 4.8,
        reviews: 567,
        image: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&h=400&fit=crop',
    },
    {
        id: 3,
        name: 'Bluetooth Speaker',
        price: 79.99,
        originalPrice: 99.99,
        rating: 4.3,
        reviews: 189,
        image: 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=400&h=400&fit=crop',
    },
    {
        id: 4,
        name: 'Laptop Stand',
        price: 49.99,
        originalPrice: 69.99,
        rating: 4.6,
        reviews: 312,
        image: 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=400&h=400&fit=crop',
    },
];

const { filterBy } = useFilter();
</script>
<template>
    <!-- Popular Products -->
    <section
        class="container mx-auto px-4 py-12 sm:px-6 lg:px-8"
        aria-label="Popular products"
    >
        <div class="mb-8 flex items-center justify-between">
            <h2 class="text-2xl font-bold sm:text-3xl">Popular Products</h2>
            <Button
                variant="outline"
                aria-label="View all popular products"
            >
                See All
            </Button>
        </div>
        <ProductCard :products="popularProducts" />
    </section>

    <!-- Latest Products -->
    <section
        class="container mx-auto px-4 py-12 sm:px-6 lg:px-8"
        aria-label="Latest products"
    >
        <div class="mb-8 flex items-center justify-between">
            <h2 class="text-2xl font-bold sm:text-3xl">New Arrivals</h2>
            <Button
                variant="outline"
                aria-label="View all new arrivals"
                @click="filterBy('/products', 'sortBy', 'default')"
            >
                See All
            </Button>
        </div>
        <ProductCard :products="latestProducts" />
    </section>
</template>
