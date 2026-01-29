<script setup>
import ProductCard from '@/components/main/ProductCard.vue';
import { Button } from '@/components/ui/button';

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

const latestProducts = [
    {
        id: 5,
        name: 'Gaming Mouse',
        price: 59.99,
        rating: 4.7,
        reviews: 145,
        image: 'https://images.unsplash.com/photo-1527814050087-3793815479db?w=400&h=400&fit=crop',
        isNew: true,
    },
    {
        id: 6,
        name: 'Mechanical Keyboard',
        price: 149.99,
        rating: 4.9,
        reviews: 278,
        image: 'https://images.unsplash.com/photo-1541140532154-b024d705b90a?w=400&h=400&fit=crop',
        isNew: true,
    },
    {
        id: 7,
        name: 'USB-C Hub',
        price: 39.99,
        rating: 4.4,
        reviews: 92,
        image: 'https://images.unsplash.com/photo-1587825140708-dfaf72ae4b04?w=400&h=400&fit=crop',
        isNew: true,
    },
    {
        id: 8,
        name: 'Wireless Charger',
        price: 34.99,
        rating: 4.2,
        reviews: 203,
        image: 'https://images.unsplash.com/photo-1580910051074-3eb694886505?w=400&h=400&fit=crop',
        isNew: true,
    },
];
</script>
<template>
    <!-- Popular Products -->
    <section
        class="container mx-auto px-4 py-12 sm:px-6 lg:px-8"
        aria-label="Popular products"
    >
        <div
            class="mb-8 flex items-center justify-between"
        >
            <h2
                class="text-2xl font-bold sm:text-3xl"
            >
                Popular Products
            </h2>
            <Button
                variant="outline"
                aria-label="View all popular products"
            >
                See All
            </Button>
        </div>
        <ProductCard
            :products="popularProducts"
        />
    </section>

    <!-- Latest Products -->
    <section
        class="container mx-auto px-4 py-12 sm:px-6 lg:px-8"
        aria-label="Latest products"
    >
        <div
            class="mb-8 flex items-center justify-between"
        >
            <h2
                class="text-2xl font-bold sm:text-3xl"
            >
                New Arrivals
            </h2>
            <Button
                variant="outline"
                aria-label="View all new arrivals"
            >
                See All
            </Button>
        </div>
        <ProductCard
            :products="latestProducts"
        />
    </section>
</template>
