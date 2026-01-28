<script setup>
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';

const {products} = defineProps({
    products: Array,
});

</script>
<template>
  <div
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6"
            >
                <div
                    v-for="product in products"
                    :key="product.id"
                    class="group relative bg-card rounded-xl border border-border overflow-hidden hover:shadow-lg transition-all"
                >
                    <div class="relative aspect-square overflow-hidden bg-muted">
                        <img
                            :src="product.image"
                            :alt="product.name"
                            class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-110"
                        />
                        <div
                            v-if="product.originalPrice"
                            class="absolute top-3 left-3"
                        >
                            <Badge
                                variant="destructive"
                                class="bg-primary text-primary-foreground"
                            >
                                {{
                                    Math.round(
                                        ((product.originalPrice -
                                            product.price) /
                                            product.originalPrice) *
                                            100,
                                    )
                                }}% OFF
                            </Badge>
                        </div>
                        <div
                            v-if="product.isNew"
                            class="absolute top-3 right-3"
                        >
                            <Badge
                                class="bg-green-500 text-white"
                                aria-label="New product"
                            >
                                New
                            </Badge>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold mb-2 line-clamp-2">
                            {{ product.name }}
                        </h3>
                        <div class="flex items-center gap-2 mb-2">
                            <div class="flex items-center">
                                <svg
                                    v-for="i in 5"
                                    :key="i"
                                    class="h-4 w-4"
                                    :class="
                                        i <= Math.floor(product.rating)
                                            ? 'text-yellow-400 fill-yellow-400'
                                            : 'text-muted-foreground'
                                    "
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                                    />
                                </svg>
                            </div>
                            <span class="text-sm text-muted-foreground">
                                ({{ product.reviews }})
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-xl font-bold text-primary">
                                    ${{ product.price.toFixed(2) }}
                                </span>
                                <span
                                    v-if="product.originalPrice"
                                    class="ml-2 text-sm text-muted-foreground line-through"
                                >
                                    ${{ product.originalPrice.toFixed(2) }}
                                </span>
                            </div>
                        </div>
                        <Button
                            class="w-full mt-4"
                            aria-label="Add {{ product.name }} to cart"
                        >
                            Add to Cart
                        </Button>
                    </div>
                </div>
            </div>
</template>