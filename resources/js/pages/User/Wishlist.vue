<script setup>
import { ref, watch } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import UserDashboardLayout from '@/layouts/UserDashboardLayout.vue';
import useFilterBy from '@/composables/useFilterBy';
import { useAddToCart } from '@/composables/useAddToCart';
import { toast } from 'vue-sonner';

defineOptions({
    layout: UserDashboardLayout,
});

const props = defineProps({
    wishlist: Object,
    total: Number,
});
const { addToCart } = useAddToCart();
const { filterBy } = useFilterBy();
const page = usePage();

const activeCategory = ref(null);

watch(
    () => page.url,
    () => {
        activeCategory.value = route().params.category ?? null;
    },
    { deep: true, immediate: true },
);

function removeFromWishlist(id) {
    router.delete(route('user.wishlist.remove', id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Product removed from wishlist');
        },
    });
}
</script>

<template>
    <Head title="My Wishlist" />

    <div class="space-y-8">
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
        >
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-foreground">
                    My wishlist
                </h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Save your favorite products and move them to cart anytime.
                </p>
            </div>
            <button
                type="button"
                class="inline-flex shrink-0 items-center justify-center rounded-full border border-border bg-card px-4 py-2 text-sm font-semibold text-foreground shadow-sm transition hover:border-primary/40 hover:bg-muted/50"
            >
                Share wishlist
            </button>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <div
                class="rounded-2xl border border-border bg-card p-5 shadow-lg shadow-black/5 dark:shadow-black/20"
            >
                <p class="text-sm font-medium text-muted-foreground">
                    Total saved
                </p>
                <p class="mt-2 text-3xl font-bold text-foreground tabular-nums">
                    {{ total }}
                </p>
            </div>
            <div
                class="rounded-2xl border border-border bg-card p-5 shadow-lg shadow-black/5 dark:shadow-black/20"
            >
                <p class="text-sm font-medium text-muted-foreground">
                    In stock Products
                </p>
                <p class="mt-2 text-3xl font-bold text-foreground tabular-nums">
                    {{ wishlist.data.filter((item) => item.stock > 0).length }}
                </p>
            </div>
            <div
                class="rounded-2xl border border-border bg-card p-5 shadow-lg shadow-black/5 dark:shadow-black/20"
            >
                <p class="text-sm font-medium text-muted-foreground">
                    Discounted Products
                </p>
                <p class="mt-2 text-3xl font-bold text-foreground tabular-nums">
                    {{
                        wishlist.data.filter((item) => item.discount_price)
                            .length
                    }}
                </p>
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            <button
                type="button"
                class="rounded-full border px-3.5 py-1.5 text-xs font-semibold transition"
                :class="
                    activeCategory === null
                        ? 'border-primary bg-primary/12 text-primary shadow-sm'
                        : 'border-border bg-card text-muted-foreground hover:border-primary/30 hover:text-foreground'
                "
                @click="filterBy('category', null, 'user.wishlist')"
            >
                All
            </button>
            <button
                v-for="category in page.props.categories"
                :key="category.slug"
                type="button"
                class="rounded-full border px-3.5 py-1.5 text-xs font-semibold transition"
                :class="
                    activeCategory === category.slug
                        ? 'border-primary bg-primary/12 text-primary shadow-sm'
                        : 'border-border bg-card text-muted-foreground hover:border-primary/30 hover:text-foreground'
                "
                @click="filterBy('category', category.slug, 'user.wishlist')"
            >
                {{ category.name }}
            </button>
        </div>

        <div
            v-if="wishlist.data.length"
            class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3"
        >
            <article
                v-for="item in wishlist.data"
                :key="item.id"
                class="group overflow-hidden rounded-2xl border border-border bg-card shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
            >
                <div class="relative aspect-4/3 overflow-hidden bg-muted">
                    <img
                        :src="item.thumbnail"
                        :alt="item.title"
                        class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]"
                    />
                    <div class="absolute top-3 left-3 flex gap-2">
                        <span
                            v-if="item.stock === 0"
                            class="rounded-full border border-destructive/30 bg-destructive/10 px-2.5 py-1 text-[11px] font-semibold text-destructive"
                        >
                            Out of stock
                        </span>
                    </div>
                    <button
                        type="button"
                        class="absolute top-3 right-3 rounded-full border border-border bg-card/95 p-2 text-muted-foreground transition hover:text-destructive"
                        @click="removeFromWishlist(item.id)"
                    >
                        <span class="sr-only">Remove from wishlist</span>
                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path d="M18 6 6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-4 p-4">
                    <div class="space-y-1">
                        <h3
                            class="line-clamp-1 text-sm font-semibold text-foreground"
                        >
                            {{ item.title }}
                        </h3>
                        <p class="line-clamp-1 text-sm text-muted-foreground">
                            {{ item.description }}
                        </p>
                    </div>

                    <div class="flex items-end gap-2">
                        <p class="text-lg font-bold text-primary">
                            {{ item.discount_price ?? item.price }}
                        </p>
                        <p
                            v-if="item.discount_price"
                            class="text-sm text-muted-foreground line-through"
                        >
                            {{ item.price }}
                        </p>
                    </div>

                    <div class="flex gap-2">
                        <button
                            type="button"
                            v-if="item.stock > 0"
                            class="w-full rounded-full border border-primary bg-card px-4 py-2 text-sm font-semibold text-primary transition hover:bg-primary hover:text-primary-foreground"
                            @click.stop="addToCart(item, 1)"
                        >
                            Add to Cart
                        </button>
                        <span
                            v-else
                            class="flex w-full cursor-not-allowed items-center justify-center gap-2 rounded-full border border-destructive/40 bg-destructive/10 px-4 py-2 text-sm font-semibold text-destructive"
                        >
                            Out of stock
                        </span>
                        <button
                            type="button"
                            class="rounded-full border border-border bg-card px-4 py-2 text-sm font-semibold text-foreground transition hover:bg-muted"
                            @click="
                                router.get(
                                    route('product.detail', {
                                        slug: item.slug,
                                    }),
                                )
                            "
                        >
                            Details
                        </button>
                    </div>
                </div>
            </article>
        </div>

        <div
            v-else
            class="rounded-2xl border border-dashed border-border bg-muted/30 px-6 py-14 text-center"
        >
            <div
                class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-muted text-muted-foreground"
            >
                <svg
                    class="h-7 w-7"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.6"
                >
                    <path
                        d="M12 20s-7-4.4-9-8.6C1.4 8.1 3.1 5 6.3 5A5 5 0 0 1 12 8a5 5 0 0 1 5.7-3c3.2 0 4.9 3.1 3.3 6.4-2 4.2-9 8.6-9 8.6Z"
                    />
                </svg>
            </div>
            <p class="mt-4 text-base font-semibold text-foreground">
                No products in this filter
            </p>
            <p class="mt-1 text-sm text-muted-foreground">
                Try another filter or browse products to add more favorites.
            </p>
        </div>
    </div>
</template>
