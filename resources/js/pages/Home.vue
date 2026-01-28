<script setup>
import { Head } from '@inertiajs/vue3';
import Navbar from '@/components/main/Navbar.vue';
import Hero from '@/components/main/Hero.vue';
import CategoryList from '@/components/main/CategoryList.vue';
import ProductList from '@/components/main/ProductList.vue';
import Contact from '@/components/main/Contact.vue';
import Footer from '@/components/main/Footer.vue';
</script>

<template>
    <Head title="Home - Shop the Latest Trends" />

    <div class="min-h-screen bg-background text-foreground">
        <Navbar />
        <Hero />
        <CategoryList />
        <ProductList />
        <Contact />
        <Footer />
    </div>
</template>
