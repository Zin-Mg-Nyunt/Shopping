<script setup>
import CategoryList from '@/components/main/CategoryList.vue';
import Contact from '@/components/main/Contact.vue';
import Hero from '@/components/main/Hero.vue';
import ProductList from '@/components/main/ProductList.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head
        title="Home - Shop the Latest Trends"
    />
    <Hero />
    <CategoryList />
    <ProductList />
    <Contact />
</template>
