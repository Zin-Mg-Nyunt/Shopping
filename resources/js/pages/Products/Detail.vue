<script setup>
import { route } from 'ziggy-js';
</script>
<template>
    <main class="bg-white">
        <div class="mx-auto w-full max-w-7xl px-6 py-10 lg:px-8">
            <section class="grid gap-8 lg:grid-cols-2">
                <div class="space-y-4">
                    <div
                        class="overflow-hidden rounded-3xl border border-gray-200 bg-white p-4 shadow-sm"
                    >
                        <div
                            class="aspect-square rounded-2xl bg-gray-100"
                        ></div>
                    </div>

                    <div class="grid grid-cols-4 gap-3">
                        <button
                            v-for="thumbnail in 4"
                            :key="thumbnail"
                            class="overflow-hidden rounded-xl border border-gray-200 bg-white p-2 shadow-sm transition hover:border-primary/40"
                        >
                            <div
                                class="aspect-square rounded-lg bg-gray-100"
                            ></div>
                        </button>
                    </div>
                </div>

                <div class="space-y-6">
                    <nav class="flex items-center gap-2 text-sm text-gray-500">
                        <a
                            :href="route('home')"
                            class="transition hover:text-primary"
                            >Home</a
                        >
                        <span>></span>
                        <a
                            :href="route('products.list')"
                            class="transition hover:text-primary"
                            >Products List</a
                        >
                        <span>></span>
                        <span class="text-gray-700">Aero Running Shoes</span>
                    </nav>

                    <div class="space-y-3">
                        <h1
                            class="text-3xl font-bold tracking-tight text-gray-950 lg:text-4xl"
                        >
                            Aero Running Shoes - Ultra Comfort Edition
                        </h1>

                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-1 text-amber-400">
                                <svg
                                    v-for="star in 5"
                                    :key="star"
                                    class="h-4 w-4 fill-current"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        d="M10 1.5 12.8 7l6 .9-4.4 4.3 1 6-5.4-2.8L4.6 18l1-6L1.2 7.9l6-.9L10 1.5Z"
                                    />
                                </svg>
                            </div>
                            <p class="text-sm text-gray-600">
                                4.8 (214 reviews)
                            </p>
                        </div>
                    </div>

                    <p class="text-4xl font-bold text-primary">$129.00</p>

                    <p class="max-w-xl text-base leading-relaxed text-gray-600">
                        Performance-engineered running shoes with breathable
                        materials, responsive cushioning, and a modern aesthetic
                        designed for all-day comfort.
                    </p>

                    <div class="space-y-3">
                        <p class="text-sm font-semibold text-gray-800">
                            Quantity
                        </p>
                        <div
                            class="flex w-fit items-center rounded-full border border-gray-200 bg-white p-1 shadow-sm"
                        >
                            <button
                                class="flex h-9 w-9 items-center justify-center rounded-full text-lg text-gray-600 transition hover:bg-gray-100"
                            >
                                -
                            </button>
                            <span
                                class="w-10 text-center text-sm font-semibold text-gray-900"
                                >1</span
                            >
                            <button
                                class="flex h-9 w-9 items-center justify-center rounded-full text-lg text-gray-600 transition hover:bg-gray-100"
                            >
                                +
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button
                            class="rounded-full bg-primary px-8 py-3 text-sm font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90"
                        >
                            Add to Cart
                        </button>
                        <button
                            class="rounded-full bg-primary/90 px-8 py-3 text-sm font-semibold text-primary-foreground shadow-sm transition hover:bg-primary"
                        >
                            Buy Now
                        </button>
                        <button
                            class="cursor-pointer rounded-full bg-primary/10 p-3 text-primary shadow-md transition"
                        >
                            <span class="sr-only">Add to wishlist</span>
                            <svg
                                class="h-5 w-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    d="M12 20s-7-4.4-9-8.6C1.4 8.1 3.1 5 6.3 5A5 5 0 0 1 12 8a5 5 0 0 1 5.7-3c3.2 0 4.9 3.1 3.3 6.4-2 4.2-9 8.6-9 8.6Z"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </section>

            <section
                class="mt-12 rounded-3xl border border-gray-200 bg-white p-6 shadow-sm lg:p-8"
            >
                <div class="flex flex-wrap gap-2 border-b border-gray-200 pb-4">
                    <button
                        class="rounded-full bg-primary px-5 py-2 text-sm font-semibold text-primary-foreground"
                    >
                        Long Description
                    </button>
                    <button
                        class="rounded-full border border-gray-200 px-5 py-2 text-sm font-semibold text-gray-600 transition hover:border-primary/40 hover:text-primary"
                    >
                        Specifications
                    </button>
                    <button
                        class="rounded-full border border-gray-200 px-5 py-2 text-sm font-semibold text-gray-600 transition hover:border-primary/40 hover:text-primary"
                    >
                        Customer Reviews
                    </button>
                </div>

                <div class="pt-6">
                    <p class="leading-relaxed text-gray-600">
                        Crafted for everyday athletes and style-conscious
                        wearers, the Aero Running Shoes feature a knit upper for
                        breathability, reinforced heel support for stability,
                        and an adaptive sole that softens impact while
                        maintaining excellent energy return.
                    </p>
                </div>
            </section>
        </div>
    </main>
</template>
