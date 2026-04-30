<script setup lang="ts">
import { Package } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';

defineProps({
    open: { type: Boolean, required: true },
    product: { type: Object, default: null },
});

const emit = defineEmits(['update:open']);

const currency = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
});

function formatPrice(value) {
    return currency.format(Number(value || 0));
}
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent class="sm:max-w-3xl">
            <DialogHeader>
                <DialogTitle>Product details</DialogTitle>
                <DialogDescription>Read-only preview</DialogDescription>
            </DialogHeader>

            <div
                v-if="product"
                class="grid gap-6 py-1 md:grid-cols-[minmax(0,1fr)_minmax(0,1.2fr)]"
            >
                <div class="overflow-hidden rounded-2xl border border-border bg-card p-3">
                    <div class="aspect-square overflow-hidden rounded-xl bg-muted">
                        <img
                            v-if="product.thumbnail"
                            :src="product.thumbnail"
                            :alt="product.title"
                            class="h-full w-full object-cover"
                        />
                        <div
                            v-else
                            class="flex h-full w-full items-center justify-center"
                        >
                            <Package class="h-12 w-12 text-muted-foreground/50" />
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-muted-foreground">Title</p>
                        <h3 class="text-xl font-bold text-foreground">
                            {{ product.title }}
                        </h3>
                    </div>

                    <div>
                        <p class="text-xs text-muted-foreground">Description</p>
                        <p class="text-sm leading-relaxed text-foreground/85">
                            {{ product.description || '-' }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded-lg border border-border bg-muted/30 p-3">
                            <p class="text-xs text-muted-foreground">Category</p>
                            <p class="font-medium">{{ product.category_name || '-' }}</p>
                        </div>
                        <div class="rounded-lg border border-border bg-muted/30 p-3">
                            <p class="text-xs text-muted-foreground">Stock</p>
                            <p class="font-medium">{{ product.stock }}</p>
                        </div>
                    </div>

                    <div class="rounded-xl border border-border bg-card p-4">
                        <p class="text-xs text-muted-foreground">Price</p>
                        <p class="text-2xl font-bold text-[#FF6600]">
                            {{ formatPrice(product.price) }}
                        </p>
                        <p
                            v-if="product.discount_price"
                            class="mt-1 text-sm font-semibold text-emerald-600"
                        >
                            Discount: {{ formatPrice(product.discount_price) }}
                        </p>
                    </div>
                </div>
            </div>

            <DialogFooter>
                <Button
                    class="bg-[#FF6600] text-white hover:bg-[#FF6600]/90"
                    @click="emit('update:open', false)"
                >
                    Close
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
