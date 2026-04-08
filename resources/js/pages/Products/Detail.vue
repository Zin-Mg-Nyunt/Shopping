<script setup>
import { useCustomToast } from '@/composables/useCustomToast';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const { product } = defineProps({
    product: Object,
});
const { customToast } = useCustomToast();
const quantity = ref(1);

function addToCart(product) {
    router.post(
        route('cart.add'),
        {
            product_id: product.id,
            quantity: quantity.value,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                customToast(
                    product.thumbnail,
                    ` x${quantity.value} added to cart`,
                );
            },
        },
    );
}

function calQuantity(event) {
    const target = event.target;
    if (target.value == '') {
        quantity.value = '';
        return;
    }
    quantity.value = Math.max(1, Math.min(product.stock, target.value));
    target.value = quantity.value;
}

function blurQuantity(event) {
    if (event.target.value == '') {
        quantity.value = 1;
    }
}
</script>
<template>
    <main class="bg-background">
        <div class="mx-auto w-full max-w-7xl px-6 py-10 lg:px-8">
            <section class="grid gap-8 lg:grid-cols-2">
                <div class="space-y-4">
                    <div
                        class="overflow-hidden rounded-3xl border border-border bg-card p-4 shadow-sm"
                    >
                        <div class="aspect-square rounded-2xl bg-muted">
                            <img
                                :src="product.thumbnail"
                                alt="Product Image"
                                class="h-full w-full object-cover"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-4 gap-3">
                        <button
                            v-for="thumbnail in product.images"
                            :key="thumbnail"
                            type="button"
                            class="overflow-hidden rounded-xl border border-border bg-card p-2 shadow-sm transition hover:border-primary/40"
                        >
                            <div class="aspect-square rounded-lg bg-muted">
                                <img
                                    :src="thumbnail.url"
                                    alt="Product Image"
                                    class="h-full w-full object-cover"
                                />
                            </div>
                        </button>
                    </div>
                </div>

                <div class="space-y-6">
                    <nav
                        class="flex items-center gap-2 text-sm text-muted-foreground"
                    >
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
                        <span class="text-foreground">{{ product.title }}</span>
                    </nav>

                    <div class="space-y-3">
                        <h1
                            class="text-3xl font-bold tracking-tight text-foreground lg:text-4xl"
                        >
                            {{ product.title }}
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
                            <p class="text-sm text-muted-foreground">
                                4.8 (214 reviews)
                            </p>
                        </div>
                    </div>

                    <p class="text-4xl font-bold text-primary">
                        ${{ product.price }}
                    </p>

                    <p
                        class="max-w-xl text-base leading-relaxed text-muted-foreground"
                    >
                        {{ product.description }}
                    </p>

                    <div class="space-y-3">
                        <p class="text-sm font-semibold text-foreground">
                            Quantity
                        </p>
                        <div
                            class="flex w-fit items-center rounded-full border border-border bg-card p-1 shadow-sm"
                        >
                            <button
                                type="button"
                                class="flex h-9 w-9 items-center justify-center rounded-full text-lg text-muted-foreground transition hover:bg-muted"
                                @click="quantity = Math.max(1, (quantity -= 1))"
                                :disabled="quantity <= 1"
                            >
                                -
                            </button>
                            <input
                                type="number"
                                class="w-10 text-center text-sm font-semibold text-foreground"
                                :value="quantity"
                                @input="calQuantity($event)"
                                @blur="blurQuantity($event)"
                            />
                            <button
                                type="button"
                                class="flex h-9 w-9 items-center justify-center rounded-full text-lg text-muted-foreground transition hover:bg-muted"
                                @click="
                                    quantity = Math.min(
                                        product.stock,
                                        (quantity += 1),
                                    )
                                "
                                :disabled="quantity >= product.stock"
                            >
                                +
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button
                            type="button"
                            class="rounded-full bg-primary px-8 py-3 text-sm font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90"
                            @click="addToCart(product)"
                        >
                            Add to Cart
                        </button>
                        <button
                            type="button"
                            class="rounded-full bg-primary/90 px-8 py-3 text-sm font-semibold text-primary-foreground shadow-sm transition hover:bg-primary"
                        >
                            Buy Now
                        </button>
                        <button
                            type="button"
                            class="cursor-pointer rounded-full bg-primary/10 p-3 text-primary shadow-md transition hover:bg-primary/20"
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
                class="mt-12 rounded-3xl border border-border bg-card p-6 shadow-sm lg:p-8"
            >
                <div class="flex flex-wrap gap-2 border-b border-border pb-4">
                    <button
                        type="button"
                        class="rounded-full bg-primary px-5 py-2 text-sm font-semibold text-primary-foreground"
                    >
                        Long Description
                    </button>
                    <button
                        type="button"
                        class="rounded-full border border-border px-5 py-2 text-sm font-semibold text-muted-foreground transition hover:border-primary/40 hover:text-primary"
                    >
                        Specifications
                    </button>
                    <button
                        type="button"
                        class="rounded-full border border-border px-5 py-2 text-sm font-semibold text-muted-foreground transition hover:border-primary/40 hover:text-primary"
                    >
                        Customer Reviews
                    </button>
                </div>

                <div class="pt-6">
                    <p class="leading-relaxed text-muted-foreground">
                        {{ product.description }}
                    </p>
                </div>
            </section>
        </div>
    </main>
</template>
