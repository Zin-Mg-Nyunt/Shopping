<script setup>
import Pagination from '@/components/main/Pagination.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { useFilter } from '@/composables/useFilter';
import UserLayout from '@/layouts/UserLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Package, Search } from 'lucide-vue-next';
import { onMounted } from 'vue';
import { toast } from 'vue-sonner';

const page = usePage();
onMounted(() => {
    if (page.props.flash.success) {
        toast.success(page.props.flash.success);
    }
});

defineOptions({
    name: 'UserOrders',
    layout: UserLayout,
});

const props = defineProps({
    orders: { type: Object, default: () => {} },
    filter: { type: Object, default: () => {} },
});

const tabs = [
    { key: null, label: 'All' },
    { key: 'pending', label: 'Processing' },
    { key: 'shipped', label: 'Shipped' },
    { key: 'delivered', label: 'Delivered' },
    { key: 'cancelled', label: 'Cancelled' },
];

const maxThumbnails = 5;

function formatDate(dateStr) {
    if (!dateStr) return '—';
    return new Date(dateStr).toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}

function formatMoney(amount) {
    if (amount == null) return '—';
    return `$${Number(amount).toFixed(2)}`;
}

function statusBadgeClass(status) {
    if (status === 'delivered') {
        return 'border-transparent bg-emerald-500/15 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400';
    }
    if (status === 'shipped') {
        return 'border-transparent bg-amber-500/15 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400';
    }
    if (
        status === 'processing' ||
        status === 'ordered' ||
        status === 'pending'
    ) {
        return 'border-transparent bg-primary/15 text-primary';
    }
    if (status === 'cancelled') {
        return 'border-transparent bg-destructive/15 text-destructive';
    }
    return '';
}

function statusLabel(status) {
    if (status === 'ordered' || status === 'pending') return 'Processing';
    return (status && status.charAt(0).toUpperCase() + status.slice(1)) || '—';
}

function canTrack(status) {
    return status === 'pending' || status === 'shipped';
}

function canReorder(status) {
    return status === 'delivered';
}

const { filterBy } = useFilter();
</script>

<template>
    <Head title="My Orders" />
    <div class="space-y-6">
        <!-- Top Section: Title + Tabs -->
        <header>
            <h1
                class="mb-4 text-2xl font-bold tracking-tight text-foreground sm:text-3xl"
            >
                My Orders
            </h1>
            <div
                class="flex flex-wrap items-center gap-2 border-b border-border pb-2"
            >
                <nav
                    class="flex w-full flex-wrap items-center gap-1 overflow-x-auto pb-0.5"
                    aria-label="Filter orders by status"
                >
                    <button
                        v-for="tab in tabs"
                        :key="tab.key"
                        type="button"
                        class="rounded-lg px-3 py-2 text-sm font-medium transition-colors focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none"
                        :class="
                            filter.status === tab.key
                                ? 'bg-primary text-primary-foreground'
                                : 'text-muted-foreground hover:bg-muted hover:text-foreground'
                        "
                        @click="filterBy('orders', 'status', tab.key)"
                    >
                        {{ tab.label }}
                    </button>
                    <span class="ml-auto py-2 text-sm text-muted-foreground">
                        {{ orders.total }} orders
                    </span>
                </nav>
            </div>
        </header>

        <!-- Orders List: Card-based -->
        <div
            v-if="orders.data.length > 0"
            class="space-y-4"
        >
            <Card
                v-for="order in orders.data"
                :key="order.id"
                class="overflow-hidden border-border bg-card shadow-sm transition-shadow hover:shadow-md"
            >
                <CardContent class="p-4 sm:p-5">
                    <!-- Top Row: Order ID, Date, Status Badge -->
                    <div
                        class="mb-4 flex flex-wrap items-center justify-between gap-2 border-b border-border pb-4"
                    >
                        <div class="min-w-0">
                            <span class="font-semibold text-foreground">
                                #{{ order.order_number }}
                            </span>
                            <span
                                class="ml-2 text-sm text-muted-foreground sm:ml-3"
                            >
                                {{ formatDate(order.created_at) }}
                            </span>
                        </div>
                        <Badge :class="statusBadgeClass(order.status)">
                            {{ statusLabel(order.status) }}
                        </Badge>
                    </div>

                    <!-- Middle Row: Product thumbnails (max 4 + "+X more") -->
                    <div class="mb-4 overflow-x-auto">
                        <div class="flex gap-2">
                            <template
                                v-for="item in order.order_items"
                                :key="item.id"
                            >
                                <div
                                    class="size-14 shrink-0 overflow-hidden rounded-lg bg-muted sm:size-16"
                                >
                                    <img
                                        v-if="item.thumbnail"
                                        :src="item.thumbnail"
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
                            </template>
                            <div
                                v-if="order.order_items.length > maxThumbnails"
                                class="flex size-14 shrink-0 items-center justify-center rounded-lg bg-muted text-xs font-medium text-muted-foreground sm:size-16"
                            >
                                +{{ order.order_items.length - maxThumbnails }}
                                more
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Row: Total, View Detail, Track / Reorder -->
                    <div
                        class="flex flex-wrap items-center justify-between gap-3 border-t border-border pt-4"
                    >
                        <p class="text-lg font-semibold text-foreground">
                            <span
                                class="text-sm font-normal text-muted-foreground"
                            >
                                total -
                            </span>
                            {{ formatMoney(order.total_amount) }}
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <Button
                                variant="outline"
                                size="sm"
                                class="border-border"
                                as-child
                            >
                                <Link
                                    :href="
                                        route(
                                            'user.orders.show',
                                            order.order_number,
                                        )
                                    "
                                >
                                    View Detail
                                </Link>
                            </Button>
                            <Button
                                v-if="canTrack(order.status)"
                                size="sm"
                                class="bg-primary text-primary-foreground hover:bg-primary/90"
                                as-child
                            >
                                <Link
                                    :href="
                                        route(
                                            'user.orders.show',
                                            order.order_number,
                                        )
                                    "
                                >
                                    Track
                                </Link>
                            </Button>
                            <Button
                                v-else-if="canReorder(order.status)"
                                size="sm"
                                class="bg-primary text-primary-foreground hover:bg-primary/90"
                                as-child
                            >
                                <Link :href="route('products')"> Reorder </Link>
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Empty State -->
        <Card
            v-else
            class="border-border bg-card py-12 text-center shadow-sm sm:py-16"
        >
            <CardContent class="flex flex-col items-center gap-4 px-6">
                <div
                    class="flex size-16 items-center justify-center rounded-full bg-muted text-muted-foreground sm:size-20"
                >
                    <Search
                        class="size-8 sm:size-10"
                        aria-hidden="true"
                    />
                </div>
                <div class="space-y-1">
                    <h2 class="font-semibold text-foreground">
                        No orders found
                    </h2>
                    <p class="text-sm text-muted-foreground">
                        {{
                            filter.status === ''
                                ? "You haven't placed any orders yet."
                                : `No orders with status "${tabs.find((t) => t.key === filter.status)?.label}".`
                        }}
                    </p>
                </div>
                <Button
                    variant="default"
                    size="sm"
                    class="bg-primary text-primary-foreground hover:bg-primary/90"
                    as-child
                >
                    <Link :href="route('products')"> Browse Products </Link>
                </Button>
            </CardContent>
        </Card>
        <Pagination :meta="orders" />
    </div>
</template>
