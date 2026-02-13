<script setup>
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import UserLayout from '@/layouts/UserLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {
    ArrowLeft,
    CheckCircle2,
    Download,
    MapPin,
    Package,
    Phone,
    Truck,
} from 'lucide-vue-next';
import { computed } from 'vue';

defineOptions({
    name: 'UserOrderDetail',
    layout: UserLayout,
});

const props = defineProps({
    order: { type: Object, default: null },
});

// Use order from backend or fallback to sample for UI-only
const defaultOrder = {
    order_number: 'ORD-7721',
    created_at: '2025-02-11T14:30:00.000Z',
    status: 'shipped',
    items: [
        { id: 1, name: 'Wireless Earbuds Pro', image_url: null, quantity: 1, unit_price: 79.99, line_total: 79.99 },
        { id: 2, name: 'Phone Stand', image_url: null, quantity: 2, unit_price: 34.99, line_total: 69.98 },
        { id: 3, name: 'USB-C Hub 7-in-1', image_url: null, quantity: 1, unit_price: 89.5, line_total: 89.5 },
    ],
    subtotal: 239.47,
    shipping: 9.99,
    tax: 19.16,
    total: 268.62,
    shipping_address: '123 Main St, Yangon',
    contact_phone: '+95 9 123 456 789',
};

const order = computed(() => props.order ?? defaultOrder);

const timelineSteps = [
    { key: 'ordered', label: 'Ordered', icon: Package },
    { key: 'shipped', label: 'Shipped', icon: Truck },
    { key: 'delivered', label: 'Delivered', icon: CheckCircle2 },
];

const currentStepIndex = computed(() => {
    const i = timelineSteps.findIndex(
        (s) => s.key === (order.value.status || '').toLowerCase(),
    );
    return i >= 0 ? i : 0;
});

function formatDate(dateStr) {
    if (!dateStr) return '—';
    return new Date(dateStr).toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function formatMoney(amount) {
    if (amount == null) return '—';
    return `$${Number(amount).toFixed(2)}`;
}

function isStepComplete(stepIndex) {
    return stepIndex < currentStepIndex.value;
}

function isStepCurrent(stepIndex) {
    return stepIndex === currentStepIndex.value;
}
</script>

<template>
    <Head :title="`Order #${order.order_number}`" />
    <div class="space-y-6">
        <!-- Back link -->
        <Link
            :href="route('user.orders')"
            class="inline-flex items-center gap-2 text-sm font-medium text-muted-foreground transition-colors hover:text-foreground focus-visible:rounded focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none"
        >
            <ArrowLeft class="size-4 shrink-0" aria-hidden="true" />
            Back to Orders
        </Link>

        <!-- Order Header -->
        <Card class="border-border bg-card shadow-sm">
            <CardContent class="p-4 sm:p-6">
                <div
                    class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div class="min-w-0">
                        <h1
                            class="truncate font-serif text-xl font-bold tracking-tight text-foreground sm:text-2xl"
                        >
                            #{{ order.order_number }}
                        </h1>
                        <p
                            class="mt-1 text-sm text-muted-foreground"
                        >
                            {{ formatDate(order.created_at) }}
                        </p>
                    </div>
                    <Button
                        variant="outline"
                        size="sm"
                        class="w-full shrink-0 gap-2 border-border bg-background sm:w-auto"
                    >
                        <Download class="size-4" aria-hidden="true" />
                        Invoice Download
                    </Button>
                </div>

                <!-- Visual Tracker: horizontal timeline -->
                <nav
                    class="mt-6 overflow-x-auto pb-1"
                    aria-label="Order status"
                >
                    <div
                        class="flex min-w-max items-center gap-0 sm:gap-4"
                    >
                        <template
                            v-for="(step, i) in timelineSteps"
                            :key="step.key"
                        >
                            <div class="flex items-center">
                                <div
                                    class="flex flex-col items-center gap-1.5 sm:flex-row sm:gap-2"
                                >
                                    <span
                                        :class="[
                                            'flex size-10 shrink-0 items-center justify-center rounded-full transition-colors sm:size-11',
                                            isStepComplete(i)
                                                ? 'bg-primary text-primary-foreground'
                                                : isStepCurrent(i)
                                                  ? 'bg-primary text-primary-foreground ring-4 ring-primary/20'
                                                  : 'bg-muted text-muted-foreground',
                                        ]"
                                    >
                                        <component
                                            :is="step.icon"
                                            class="size-5 sm:size-6"
                                            aria-hidden="true"
                                        />
                                    </span>
                                    <span
                                        :class="[
                                            'text-xs font-medium sm:text-sm',
                                            i <= currentStepIndex
                                                ? 'text-foreground'
                                                : 'text-muted-foreground',
                                        ]"
                                    >
                                        {{ step.label }}
                                    </span>
                                </div>
                                <span
                                    v-if="i < timelineSteps.length - 1"
                                    class="mx-2 h-0.5 w-6 shrink-0 rounded sm:mx-4 sm:w-12"
                                    :class="
                                        isStepComplete(i) ? 'bg-primary' : 'bg-muted'
                                    "
                                    aria-hidden="true"
                                />
                            </div>
                        </template>
                    </div>
                </nav>
            </CardContent>
        </Card>

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Main: Items Table -->
            <div class="lg:col-span-2">
                <Card class="overflow-hidden border-border bg-card shadow-sm">
                    <CardHeader class="px-4 py-3 sm:px-6">
                        <CardTitle class="text-base font-semibold text-foreground">
                            Order items
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="p-0">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr class="border-b border-border bg-muted/50">
                                        <th
                                            class="px-4 py-3 font-medium text-muted-foreground sm:px-6"
                                        >
                                            Product
                                        </th>
                                        <th
                                            class="px-4 py-3 font-medium text-muted-foreground sm:px-6"
                                        >
                                            Qty
                                        </th>
                                        <th
                                            class="px-4 py-3 font-medium text-muted-foreground sm:px-6"
                                        >
                                            Price
                                        </th>
                                        <th
                                            class="w-24 px-4 py-3 sm:px-6"
                                            aria-label="Actions"
                                        />
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="item in order.items"
                                        :key="item.id"
                                        class="border-b border-border last:border-0 hover:bg-muted/30"
                                    >
                                        <td class="px-4 py-3 sm:px-6">
                                            <div
                                                class="flex items-center gap-3"
                                            >
                                                <div
                                                    class="size-12 shrink-0 overflow-hidden rounded-lg bg-muted sm:size-14"
                                                >
                                                    <img
                                                        v-if="item.image_url"
                                                        :src="item.image_url"
                                                        :alt="item.name"
                                                        class="size-full object-cover"
                                                    />
                                                    <div
                                                        v-else
                                                        class="flex size-full items-center justify-center text-muted-foreground"
                                                    >
                                                        <Package
                                                            class="size-6"
                                                            aria-hidden="true"
                                                        />
                                                    </div>
                                                </div>
                                                <span
                                                    class="font-medium text-foreground"
                                                >
                                                    {{ item.name }}
                                                </span>
                                            </div>
                                        </td>
                                        <td
                                            class="px-4 py-3 text-muted-foreground sm:px-6"
                                        >
                                            {{ item.quantity }}
                                        </td>
                                        <td
                                            class="px-4 py-3 text-foreground sm:px-6"
                                        >
                                            {{ formatMoney(item.unit_price) }}
                                        </td>
                                        <td class="px-4 py-3 sm:px-6">
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                class="text-primary hover:bg-primary/10 hover:text-primary"
                                            >
                                                Review
                                            </Button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Sidebar: Order Summary + Shipping -->
            <div class="space-y-6">
                <!-- Order Summary Card -->
                <Card class="border-border bg-card shadow-sm">
                    <CardHeader class="px-4 py-3 sm:px-6">
                        <CardTitle class="text-base font-semibold text-foreground">
                            Order summary
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-2 px-4 pb-4 sm:px-6 sm:pb-6">
                        <div
                            class="flex justify-between text-sm"
                        >
                            <span class="text-muted-foreground">Subtotal</span>
                            <span class="text-foreground">{{
                                formatMoney(order.subtotal)
                            }}</span>
                        </div>
                        <div
                            class="flex justify-between text-sm"
                        >
                            <span class="text-muted-foreground">Shipping</span>
                            <span class="text-foreground">{{
                                formatMoney(order.shipping)
                            }}</span>
                        </div>
                        <div
                            class="flex justify-between text-sm"
                        >
                            <span class="text-muted-foreground">Tax</span>
                            <span class="text-foreground">{{
                                formatMoney(order.tax)
                            }}</span>
                        </div>
                        <div
                            class="flex justify-between border-t border-border pt-3 text-base font-semibold text-foreground"
                        >
                            <span>Grand total</span>
                            <span>{{ formatMoney(order.total) }}</span>
                        </div>
                    </CardContent>
                </Card>

                <!-- Shipping Info Card -->
                <Card class="border-border bg-card shadow-sm">
                    <CardHeader class="px-4 py-3 sm:px-6">
                        <CardTitle class="text-base font-semibold text-foreground">
                            Shipping address
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-3 px-4 pb-4 sm:px-6 sm:pb-6">
                        <div
                            class="flex gap-3 text-sm text-foreground"
                        >
                            <MapPin
                                class="size-4 shrink-0 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <span>{{ order.shipping_address }}</span>
                        </div>
                        <div
                            class="flex gap-3 text-sm text-foreground"
                        >
                            <Phone
                                class="size-4 shrink-0 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <a
                                :href="`tel:${order.contact_phone}`"
                                class="hover:text-primary hover:underline"
                            >
                                {{ order.contact_phone }}
                            </a>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
