<script setup>
import Footer from '@/components/main/Footer.vue';
import Navbar from '@/components/main/Navbar.vue';
</script>
<template>
    <div
        class="bg-background text-foreground min-h-screen"
    >
        <Navbar />
        <slot />
        <Footer />
    </div>
</template>
