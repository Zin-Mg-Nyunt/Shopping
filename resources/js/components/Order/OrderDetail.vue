<script setup>
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetHeader,
    SheetTitle,
} from '@/components/ui/sheet';

const props = defineProps({
    selectedOrder: Object,
    detailSheetOpen: Boolean,
});

const formatDate = (iso) =>
    new Date(iso).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });

const statusStyles = {
    pending:
        'border-amber-500/25 bg-amber-500/10 text-amber-800 dark:text-amber-400',
    processing: 'border-primary/25 bg-primary/12 text-primary',
    shipped: 'border-sky-500/25 bg-sky-500/10 text-sky-800 dark:text-sky-300',
    delivered:
        'border-emerald-500/25 bg-emerald-500/10 text-emerald-800 dark:text-emerald-400',
    cancelled: 'border-destructive/30 bg-destructive/10 text-destructive',
};

const emit = defineEmits(['update:detailSheetOpen']);
</script>
<template>
    <Sheet
        :open="detailSheetOpen"
        @update:open="emit('update:detailSheetOpen', $event)"
    >
        <SheetContent
            side="right"
            class="w-full border-l border-border p-0 sm:max-w-xl"
        >
            <div
                v-if="selectedOrder"
                class="flex h-full flex-col bg-card text-card-foreground"
            >
                <SheetHeader class="border-b border-border px-6 py-5 text-left">
                    <SheetTitle class="text-xl font-semibold text-foreground">
                        Order details
                    </SheetTitle>
                    <SheetDescription class="text-sm text-muted-foreground">
                        {{ selectedOrder.order_number }} •
                        {{ formatDate(selectedOrder.created_at) }}
                    </SheetDescription>
                </SheetHeader>

                <div class="flex-1 space-y-6 overflow-y-auto px-6 py-6">
                    <div class="grid gap-3 sm:grid-cols-3">
                        <div
                            class="rounded-xl border border-border bg-muted/30 p-4"
                        >
                            <p
                                class="text-xs font-medium text-muted-foreground"
                            >
                                Status
                            </p>
                            <span
                                class="mt-2 inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold capitalize"
                                :class="statusStyles[selectedOrder.status]"
                            >
                                {{ selectedOrder.status }}
                            </span>
                        </div>
                        <div
                            class="rounded-xl border border-border bg-muted/30 p-4"
                        >
                            <p
                                class="text-xs font-medium text-muted-foreground"
                            >
                                Total amount
                            </p>
                            <p
                                class="mt-2 text-2xl font-bold text-foreground tabular-nums"
                            >
                                {{
                                    selectedOrder.status === 'cancelled'
                                        ? 0
                                        : selectedOrder.total_amount
                                }}
                            </p>
                        </div>
                        <div
                            :class="
                                selectedOrder.status === 'cancelled'
                                    ? 'rounded-xl border border-destructive/30 bg-destructive/10 p-4'
                                    : 'rounded-xl border border-border bg-muted/30 p-4'
                            "
                        >
                            <p
                                class="text-xs font-medium text-muted-foreground"
                            >
                                Payment method
                            </p>
                            <p
                                class="mt-2 text-2xl font-bold capitalize"
                                :class="
                                    selectedOrder.status === 'cancelled'
                                        ? 'text-destructive'
                                        : 'text-foreground'
                                "
                            >
                                {{
                                    selectedOrder.status === 'cancelled'
                                        ? 'Cancelled'
                                        : selectedOrder.payment_method
                                }}
                            </p>
                        </div>
                        <div
                            v-if="selectedOrder.promo_code"
                            class="rounded-xl border border-emerald-500/25 bg-emerald-500/10 p-4"
                        >
                            <p
                                class="text-xs font-medium text-emerald-800 dark:text-emerald-400"
                            >
                                Promo code used
                            </p>
                            <p
                                class="mt-2 text-2xl font-bold text-emerald-900 uppercase dark:text-emerald-300"
                            >
                                {{ selectedOrder.promo_code }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="rounded-xl border border-border bg-muted/20 p-4"
                    >
                        <h3 class="text-sm font-semibold text-foreground">
                            Shipping information
                        </h3>
                        <div
                            class="mt-3 space-y-2 text-sm text-muted-foreground"
                        >
                            <p>
                                <span class="font-medium text-foreground"
                                    >Recipient:</span
                                >
                                {{ selectedOrder.shipping_address.full_name }}
                            </p>
                            <p>
                                <span class="font-medium text-foreground"
                                    >Phone:</span
                                >
                                {{ selectedOrder.shipping_address.phone }}
                            </p>
                            <p>
                                <span class="font-medium text-foreground"
                                    >Address:</span
                                >
                                {{ selectedOrder.shipping_address.street }},
                                {{ selectedOrder.shipping_address.city }},
                                {{ selectedOrder.shipping_address.postal_code }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <h3 class="text-sm font-semibold text-foreground">
                            Items in this order
                        </h3>
                        <ul
                            class="divide-y divide-border overflow-hidden rounded-xl border border-border bg-muted/20"
                        >
                            <li
                                v-for="line in selectedOrder.order_details"
                                :key="line.id"
                                class="flex gap-3 px-4 py-3"
                            >
                                <img
                                    v-if="line.product_image"
                                    :src="line.product_image"
                                    alt=""
                                    class="h-14 w-14 shrink-0 rounded-lg border border-border object-cover"
                                />
                                <div
                                    v-else
                                    class="h-14 w-14 shrink-0 rounded-lg border border-border bg-muted"
                                />
                                <div class="min-w-0 flex-1">
                                    <p
                                        class="truncate text-sm font-medium text-foreground"
                                    >
                                        {{ line.product_name }}
                                    </p>
                                    <p
                                        class="mt-0.5 text-xs text-muted-foreground"
                                    >
                                        {{ line.quantity }} x
                                        {{ line.price }}
                                    </p>
                                </div>
                                <p
                                    class="text-sm font-semibold text-foreground tabular-nums"
                                >
                                    {{ line.sub_total }}
                                </p>
                            </li>
                        </ul>
                    </div>
                </div>

                <div
                    class="mt-auto flex flex-col-reverse gap-2 border-t border-border bg-background/70 px-6 py-4 sm:flex-row sm:justify-end"
                >
                    <button
                        type="button"
                        class="rounded-full border border-border bg-card px-4 py-2 text-sm font-semibold text-foreground transition hover:bg-muted"
                        @click="emit('update:detailSheetOpen', false)"
                    >
                        Close
                    </button>
                    <button
                        type="button"
                        class="rounded-full border border-primary bg-primary px-4 py-2 text-sm font-semibold text-primary-foreground transition hover:bg-primary/90"
                    >
                        Download receipt
                    </button>
                </div>
            </div>
        </SheetContent>
    </Sheet>
</template>

<style></style>
