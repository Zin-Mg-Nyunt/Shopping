<script setup>
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { route } from 'ziggy-js';
import Pagination from '@/components/ui/Pagination.vue';
import AdminDashboardLayout from '@/layouts/AdminDashboardLayout.vue';
import { usePage } from '@inertiajs/vue3';

defineOptions({
    layout: AdminDashboardLayout,
});

const page = usePage();
const { orders, statusCount } = defineProps({
    orders: Object,
    statusCount: Object,
});

const search = ref('');
const activeStatus = ref(null);
const statusFilters = computed(() =>
    page.props.statusFilters.map((status) => ({
        ...status,
        count: statusCount[status.label.toLowerCase()],
    })),
);

const statusStyles = {
    pending:
        'border-amber-500/25 bg-amber-500/10 text-amber-800 dark:text-amber-400',
    processing: 'border-primary/25 bg-primary/12 text-primary',
    shipped: 'border-sky-500/25 bg-sky-500/10 text-sky-800 dark:text-sky-300',
    delivered:
        'border-emerald-500/25 bg-emerald-500/10 text-emerald-800 dark:text-emerald-400',
    cancelled: 'border-destructive/30 bg-destructive/10 text-destructive',
};

const rows = computed(() => orders?.data ?? []);

function applyFilters(status) {
    activeStatus.value = status;

    router.get(
        route('admin.orders'),
        {
            status: status ?? undefined,
            search: search.value || undefined,
        },
        { preserveState: true, preserveScroll: true, replace: true },
    );
}

function formatDate(dateValue) {
    return new Date(dateValue).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
}
</script>

<template>
    <Head title="Admin · Orders" />
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-foreground">
                Orders
            </h1>
            <p class="mt-1 text-sm text-muted-foreground">
                Review all customer orders and monitor fulfillment statuses.
            </p>
        </div>

        <div class="rounded-2xl border border-border bg-card p-4 shadow-sm">
            <div
                class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between"
            >
                <div class="w-full lg:max-w-xs">
                    <label for="order-search" class="sr-only"
                        >Search orders</label
                    >
                    <input
                        id="order-search"
                        v-model="search"
                        type="search"
                        placeholder="Search by order no, name, email..."
                        class="w-full rounded-xl border border-border bg-muted/40 px-3 py-2 text-sm text-foreground placeholder:text-muted-foreground focus:border-[#FF6600] focus:ring-2 focus:ring-[#FF6600]/25 focus:outline-none"
                        @keyup.enter="applyFilters()"
                        @blur="search === '' ? applyFilters() : null"
                    />
                </div>

                <div class="flex flex-wrap gap-2">
                    <button
                        v-for="f in statusFilters"
                        :key="f.id"
                        type="button"
                        class="rounded-full border px-3.5 py-1.5 text-xs font-semibold transition"
                        :class="
                            activeStatus === f.id
                                ? 'border-primary bg-primary/12 text-primary'
                                : 'border-border bg-card text-muted-foreground hover:border-primary/30 hover:text-foreground'
                        "
                        @click="applyFilters(f.id)"
                    >
                        {{ f.label }} ({{ f.count }})
                    </button>
                </div>
            </div>
        </div>

        <div
            class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm"
        >
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px]">
                    <thead
                        class="bg-muted/30 text-left text-xs font-semibold text-muted-foreground uppercase"
                    >
                        <tr>
                            <th class="px-4 py-3">Order</th>
                            <th class="px-4 py-3">Customer</th>
                            <th class="px-4 py-3">Items</th>
                            <th class="px-4 py-3">Total</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Placed at</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <tr
                            v-for="order in rows"
                            :key="order.id"
                            class="hover:bg-muted/20"
                        >
                            <td class="px-4 py-3">
                                <p class="font-semibold text-foreground">
                                    {{ order.order_number }}
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    #{{ order.id }}
                                </p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-sm font-medium text-foreground">
                                    {{ order.user?.name ?? 'Unknown customer' }}
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    {{ order.user?.email ?? '-' }}
                                </p>
                            </td>
                            <td class="px-4 py-3 text-sm text-foreground">
                                {{ order.total_quantity }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm font-semibold text-foreground tabular-nums"
                            >
                                {{ Number(order.total_amount).toFixed(2) }}
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex rounded-full border px-2.5 py-0.5 text-xs font-semibold capitalize"
                                    :class="
                                        statusStyles[order.status] ??
                                        'border-border bg-muted text-foreground'
                                    "
                                >
                                    {{ order.status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-muted-foreground">
                                {{ formatDate(order.created_at) }}
                            </td>
                        </tr>
                        <tr v-if="rows.length === 0">
                            <td
                                colspan="6"
                                class="px-4 py-10 text-center text-sm text-muted-foreground"
                            >
                                No orders found for current filters.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <Pagination :pagination="orders" />
    </div>
</template>
