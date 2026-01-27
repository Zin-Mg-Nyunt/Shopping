<script setup lang="ts">
import Navbar from '@/components/main/Navbar.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Home" />
    <div class="min-h-screen bg-background">
        <Navbar />
        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl font-bold tracking-tight sm:text-6xl">
                    Welcome to Shopping
                </h1>
                <p class="mt-6 text-lg leading-8 text-muted-foreground">
                    Discover amazing products at great prices
                </p>
            </div>
        </main>
    </div>
</template>