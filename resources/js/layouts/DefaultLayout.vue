<script setup>
import Footer from '@/components/main/Footer.vue';
import Navbar from '@/components/main/Navbar.vue';
import { Toaster } from 'vue-sonner';
import 'vue-sonner/style.css';
</script>
<template>
    <div class="min-h-screen bg-background text-foreground">
        <Toaster
            position="top-right"
            :expand="true"
            richColors
        />
        <Navbar />
        <slot />
        <Footer />
    </div>
</template>
