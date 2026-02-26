<script setup>
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { CreditCard, MapPin, Phone } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps({
    open: {
        type: Boolean,
        default: false,
    },
    items: {
        type: Array,
        default: () => [],
    },
    address: {
        type: Object,
        default: null,
    },
    paymentMethodLabel: {
        type: String,
        default: '',
    },
    subtotal: {
        type: Number,
        default: 0,
    },
    deliveryFee: {
        type: Number,
        default: 0,
    },
    total: {
        type: Number,
        default: 0,
    },
});

const emit = defineEmits(['update:open', 'confirm']);

const fullAddressString = computed(() => {
    if (!props.address) return '—';
    const a = props.address;
    const parts = [
        a.street_address,
        a.quarter_or_village,
        a.township,
        a.state_or_region,
    ].filter(Boolean);
    return parts.length ? parts.join(', ') : '—';
});

function formatMoney(amount) {
    if (amount == null) return '—';
    return `$${Number(amount).toFixed(2)}`;
}

function lineTotal(item) {
    return item.product.price * item.quantity;
}

function close() {
    emit('update:open', false);
}

function onConfirm() {
    emit('confirm');
}
</script>

<template>
    <Dialog
        :open="open"
        @update:open="emit('update:open', $event)"
    >
        <DialogContent
            class="flex max-h-[90vh] flex-col gap-0 border-border bg-background p-0 text-foreground sm:max-w-xl"
            :aria-describedby="undefined"
        >
            <DialogHeader class="shrink-0 border-b border-border px-6 py-4">
                <DialogTitle class="text-lg font-semibold text-foreground">
                    Confirm Your Order
                </DialogTitle>
            </DialogHeader>

            <div
                class="flex min-h-0 flex-1 flex-col gap-0 overflow-y-auto px-6 py-4"
            >
                <!-- Product list -->
                <section class="space-y-3">
                    <h3
                        class="text-sm font-medium tracking-wide text-muted-foreground uppercase"
                    >
                        Items
                    </h3>
                    <ul
                        class="max-h-40 space-y-2 overflow-y-auto rounded-lg border border-border bg-muted/30 p-3"
                        role="list"
                    >
                        <li
                            v-for="item in items"
                            :key="item.id"
                            class="flex items-center justify-between gap-3 border-b border-border/50 pb-2 last:border-0 last:pb-0"
                        >
                            <img
                                :src="item.product.thumbnail"
                                :alt="item.product.name"
                                class="size-10 rounded-sm object-cover"
                            />
                            <div class="min-w-0 flex-1">
                                <p
                                    class="truncate text-sm font-medium text-foreground"
                                >
                                    {{ item.product.name }}
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    {{ item.quantity }} ×
                                    {{ formatMoney(item.product.price) }}
                                </p>
                            </div>
                            <span
                                class="shrink-0 text-sm font-semibold text-foreground"
                            >
                                {{ formatMoney(lineTotal(item)) }}
                            </span>
                        </li>
                    </ul>
                </section>

                <div class="my-4 border-t border-border" />

                <!-- Shipping address -->
                <section class="space-y-2">
                    <h3
                        class="flex items-center gap-2 text-sm font-medium tracking-wide text-muted-foreground uppercase"
                    >
                        <MapPin
                            class="size-4 shrink-0"
                            aria-hidden="true"
                        />
                        Shipping address
                    </h3>
                    <div
                        class="rounded-lg border border-border bg-muted/30 p-3 text-sm"
                    >
                        <p
                            v-if="address?.contact_name"
                            class="font-medium text-foreground"
                        >
                            {{ address.contact_name }}
                        </p>
                        <p
                            v-if="address?.phone"
                            class="mt-1 flex items-center gap-1.5 text-muted-foreground"
                        >
                            <Phone
                                class="size-3.5 shrink-0"
                                aria-hidden="true"
                            />
                            {{ address.phone }}
                        </p>
                        <p class="mt-1.5 text-muted-foreground">
                            {{ fullAddressString }}
                        </p>
                    </div>
                </section>

                <div class="my-4 border-t border-border" />

                <!-- Payment method -->
                <section class="space-y-2">
                    <h3
                        class="flex items-center gap-2 text-sm font-medium tracking-wide text-muted-foreground uppercase"
                    >
                        <CreditCard
                            class="size-4 shrink-0"
                            aria-hidden="true"
                        />
                        Payment method
                    </h3>
                    <div
                        class="rounded-lg border border-border bg-muted/30 px-3 py-2.5 text-sm font-medium text-foreground"
                    >
                        {{ paymentMethodLabel || '—' }}
                    </div>
                </section>

                <div class="my-4 border-t border-border" />

                <!-- Order summary -->
                <section class="space-y-2">
                    <h3
                        class="text-sm font-medium tracking-wide text-muted-foreground uppercase"
                    >
                        Order summary
                    </h3>
                    <div
                        class="space-y-1.5 rounded-lg border border-border bg-muted/30 p-3 text-sm"
                    >
                        <div class="flex justify-between text-foreground">
                            <span class="text-muted-foreground">Subtotal</span>
                            <span class="font-medium">
                                {{ formatMoney(subtotal) }}
                            </span>
                        </div>
                        <div class="flex justify-between text-foreground">
                            <span class="text-muted-foreground"
                                >Shipping fee</span
                            >
                            <span class="font-medium">
                                {{
                                    deliveryFee === 0
                                        ? 'Free'
                                        : formatMoney(deliveryFee)
                                }}
                            </span>
                        </div>
                        <div
                            class="flex justify-between border-t border-border pt-2 text-base font-semibold text-foreground"
                        >
                            <span>Total</span>
                            <span>{{ formatMoney(total) }}</span>
                        </div>
                    </div>
                </section>
            </div>

            <DialogFooter
                class="shrink-0 gap-2 border-t border-border px-6 py-4"
            >
                <Button
                    type="button"
                    variant="outline"
                    class="border-border"
                    @click="close"
                >
                    Cancel
                </Button>
                <Button
                    type="button"
                    class="bg-primary text-primary-foreground hover:bg-primary/90"
                    @click="onConfirm"
                >
                    Confirm Order
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
