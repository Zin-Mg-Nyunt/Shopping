<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import UserDashboardLayout from '@/layouts/UserDashboardLayout.vue';
import Pagination from '@/components/ui/Pagination.vue';

defineOptions({
    layout: UserDashboardLayout,
});

const { orders, statusCount } = defineProps({
    orders: Object,
    statusCount: Object,
});

const filters = [
    { id: null, label: 'All', count: statusCount.total },
    { id: 'pending', label: 'Pending', count: statusCount.pending },
    { id: 'processing', label: 'Processing', count: statusCount.processing },
    { id: 'shipped', label: 'Shipped', count: statusCount.shipped },
    { id: 'delivered', label: 'Delivered', count: statusCount.delivered },
    { id: 'cancelled', label: 'Cancelled', count: statusCount.cancelled },
];

const activeFilter = ref(null);

function orderFilter(status) {
    router.get(route('orders.list', { status }), {}, { preserveState: true });
    activeFilter.value = status;
}

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
</script>

<template>
    <Head title="Order history" />

    <div class="space-y-8">
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
        >
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-foreground">
                    Order history
                </h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Track shipments, review past purchases, and open order
                    details.
                </p>
            </div>
            <button
                type="button"
                class="inline-flex shrink-0 items-center justify-center rounded-full border border-border bg-card px-4 py-2 text-sm font-semibold text-foreground shadow-sm transition hover:border-primary/40 hover:bg-muted/50"
            >
                Download invoices
            </button>
        </div>

        <div class="flex flex-wrap gap-2">
            <button
                v-for="f in filters"
                :key="f.id"
                type="button"
                class="rounded-full border px-3.5 py-1.5 text-xs font-semibold transition"
                :class="
                    activeFilter === f.id
                        ? 'border-primary bg-primary/12 text-primary shadow-sm'
                        : 'border-border bg-card text-muted-foreground hover:border-primary/30 hover:text-foreground'
                "
                @click="orderFilter(f.id)"
            >
                {{ f.label }} ({{ f.count }})
            </button>
        </div>

        <div v-if="orders.data && orders.data.length > 0" class="space-y-4">
            <article
                v-for="order in orders.data"
                :key="order.id"
                class="overflow-hidden rounded-2xl border border-border bg-card shadow-lg shadow-black/5 transition hover:shadow-md dark:shadow-black/20"
            >
                <div
                    class="flex flex-col gap-4 border-b border-border bg-muted/30 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div class="w-full space-y-1">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-foreground">
                                    {{ order.order_number }}
                                </p>
                                <span
                                    class="inline-flex rounded-full border px-2.5 py-0.5 text-xs font-semibold capitalize"
                                    :class="statusStyles[order.status]"
                                >
                                    {{ order.status }}
                                </span>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-muted-foreground">
                                    Total
                                </p>
                                <p
                                    class="text-lg font-bold text-foreground tabular-nums"
                                >
                                    {{ order.total_amount }}
                                </p>
                            </div>
                        </div>
                        <p class="text-sm text-muted-foreground">
                            Placed {{ formatDate(order.created_at) }}
                        </p>
                    </div>
                    <div class="flex w-full shrink-0 items-center sm:w-auto">
                        <button
                            type="button"
                            class="w-full rounded-full border border-primary bg-card py-2 text-sm font-semibold text-primary transition hover:bg-primary hover:text-primary-foreground sm:w-auto sm:px-4 sm:py-2"
                        >
                            View details
                        </button>
                    </div>
                </div>

                <div class="px-5 py-4">
                    <p class="text-xs font-medium text-muted-foreground">
                        {{
                            order.order_details.length === 0
                                ? 'No items'
                                : `${order.order_details.length} item${order.order_details.length === 1 ? '' : 's'}`
                        }}
                    </p>
                    <ul
                        class="mt-3 divide-y divide-border rounded-xl border border-border bg-muted/20"
                    >
                        <li
                            v-for="line in order.order_details.slice(0, 3)"
                            :key="line.id"
                            class="flex items-center gap-3 px-3 py-2.5 first:rounded-t-xl last:rounded-b-xl"
                        >
                            <img
                                v-if="line.product_image"
                                :src="line.product_image"
                                alt=""
                            />
                            <div
                                v-else
                                class="h-11 w-11 shrink-0 rounded-lg bg-muted"
                            />
                            <div
                                class="flex w-full items-start justify-between"
                            >
                                <div>
                                    <p
                                        class="truncate text-sm font-medium text-foreground"
                                    >
                                        {{ line.product_name }}
                                    </p>
                                    <p
                                        class="truncate text-xs text-muted-foreground"
                                    >
                                        {{ line.price }} x
                                        {{ line.quantity }}
                                    </p>
                                </div>
                                <p class="text-sm font-medium text-foreground">
                                    {{ line.sub_total }}
                                </p>
                            </div>
                        </li>
                    </ul>
                    <p
                        v-if="order.order_details.length > 3"
                        class="mt-2 text-xs text-muted-foreground"
                    >
                        +{{ order.order_details.length - 3 }} more in this order
                    </p>
                </div>
            </article>
            <Pagination :pagination="orders" />
        </div>

        <template v-else>
            <div
                class="rounded-2xl border border-dashed border-primary/50 px-6 py-14 text-center"
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
                        <path d="M9 11V6a3 3 0 0 1 6 0v5M5 9h14l-1 12H6L5 9Z" />
                    </svg>
                </div>
                <p class="mt-4 text-base font-semibold text-foreground">
                    No orders in this view
                </p>
                <p class="mt-1 text-sm text-muted-foreground">
                    Try another filter or check back after your next purchase.
                </p>
            </div>
        </template>
    </div>
</template>
