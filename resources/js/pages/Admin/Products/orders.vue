<script setup>
import Pagination from '@/components/main/Pagination.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Calendar,
    CreditCard,
    Eye,
    Search,
    ShoppingBag,
    TrendingDown,
    TrendingUp,
    X,
} from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref } from 'vue';

// Backend: pass paginated orders, filter state, and optional stats (totalRevenue, newOrdersCount, pendingOrdersCount)
const props = defineProps({
    orders: Object,
    filter: Object,
    stats: Object,
});

const adminOrdersUrl = route('admin.orders');
const searchQuery = ref(props.filter?.search ?? '');
const statusFilter = ref(props.filter?.status ?? '');
const dateFrom = ref(props.filter?.dateFrom ?? '');
const dateTo = ref(props.filter?.dateTo ?? '');

// Backend: apply filters with query params (search, status, dateFrom, dateTo)
function applyFilters() {
    const params = {};
    if (searchQuery.value) params.search = searchQuery.value;
    if (statusFilter.value) params.status = statusFilter.value;
    if (dateFrom.value) params.dateFrom = dateFrom.value;
    if (dateTo.value) params.dateTo = dateTo.value;
    router.get(adminOrdersUrl, params, { preserveState: true });
}

// Status badge: Yellow=Pending, Blue=Paid, Green=Delivered, Red=Cancelled (Backend: map your statuses accordingly)
function statusBadge(status) {
    const s = (status || '').toLowerCase();
    const map = {
        pending: {
            label: 'Pending',
            class: 'border-amber-500/40 bg-amber-500/15 text-amber-400 dark:bg-amber-500/20 dark:text-amber-300',
        },
        paid: {
            label: 'Paid',
            class: 'border-blue-500/40 bg-blue-500/15 text-blue-400 dark:bg-blue-500/20 dark:text-blue-300',
        },
        processing: {
            label: 'Processing',
            class: 'border-blue-500/40 bg-blue-500/15 text-blue-400 dark:bg-blue-500/20 dark:text-blue-300',
        },
        shipped: {
            label: 'Shipped',
            class: 'border-emerald-500/40 bg-emerald-500/15 text-emerald-400 dark:bg-emerald-500/20 dark:text-emerald-300',
        },
        delivered: {
            label: 'Delivered',
            class: 'border-emerald-500/40 bg-emerald-500/15 text-emerald-400 dark:bg-emerald-500/20 dark:text-emerald-300',
        },
        cancelled: {
            label: 'Cancelled',
            class: 'border-red-500/40 bg-red-500/15 text-red-400 dark:bg-red-500/20 dark:text-red-300',
        },
    };
    return (
        map[s] ?? {
            label: status || '—',
            class: 'border-white/20 bg-white/10 text-zinc-400',
        }
    );
}

// Backend: order.payment_method e.g. 'kpay', 'wave', 'card', 'cash'
function paymentDisplay(method) {
    const m = (method || '').toLowerCase();
    const map = {
        kpay: { label: 'KPay', icon: CreditCard },
        wave: { label: 'Wave', icon: CreditCard },
        card: { label: 'Card', icon: CreditCard },
        cash: { label: 'Cash', icon: CreditCard },
    };
    return map[m] ?? { label: method || '—', icon: CreditCard };
}

function formatDate(dateStr) {
    if (!dateStr) return '—';
    const d = new Date(dateStr);
    return d.toLocaleDateString(undefined, {
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

const orderList = computed(() => props.orders?.data ?? []);
const totalOrders = computed(() => props.orders?.total ?? 0);

// Backend: pass stats.totalRevenue, stats.newOrdersCount, stats.pendingOrdersCount (and optional trend values)
const totalRevenue = computed(() => props.stats?.totalRevenue ?? 0);
const newOrdersCount = computed(() => props.stats?.newOrdersCount ?? 0);
const pendingOrdersCount = computed(() => props.stats?.pendingOrdersCount ?? 0);

// Real-time notification: Backend can push via Echo/Broadcast; for UI demo we show once on mount
const showNewOrderNotification = ref(false);
let notificationTimer;
onMounted(() => {
    showNewOrderNotification.value = true;
    notificationTimer = setTimeout(() => {
        showNewOrderNotification.value = false;
    }, 6000);
});
onUnmounted(() => {
    if (notificationTimer) clearTimeout(notificationTimer);
});
function dismissNotification() {
    showNewOrderNotification.value = false;
}

defineOptions({
    layout: AdminLayout,
});
</script>

<template>
    <Head title="Orders - Admin" />
    <div class="-m-4 min-h-[60vh] sm:-m-6 dark:text-white">
        <div class="space-y-8 p-6 sm:p-8">
            <!-- Page title -->
            <div class="flex items-center justify-between">
                <h1
                    class="text-2xl font-semibold tracking-tight sm:text-3xl dark:text-white"
                >
                    Order Management
                </h1>
            </div>

            <!-- Stats cards -->
            <section
                class="grid gap-4 sm:grid-cols-3"
                aria-label="Order statistics"
            >
                <!-- Backend: pass stats.totalRevenue -->
                <div
                    class="relative overflow-hidden rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900/50"
                >
                    <div class="flex items-start justify-between">
                        <div>
                            <p
                                class="text-sm font-medium text-gray-400 dark:text-gray-500"
                            >
                                Total Revenue
                            </p>
                            <p
                                class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl dark:text-gray-100"
                            >
                                {{ formatMoney(totalRevenue) }}
                            </p>
                            <p
                                class="mt-1 flex items-center gap-1 text-xs text-emerald-400"
                            >
                                <TrendingUp class="h-3.5 w-3.5" />
                                <span>+12% from last month</span>
                            </p>
                        </div>
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-400"
                        >
                            <TrendingUp class="h-6 w-6" />
                        </div>
                    </div>
                </div>
                <!-- Backend: pass stats.newOrdersCount -->
                <div
                    class="relative overflow-hidden rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900/50"
                >
                    <div class="flex items-start justify-between">
                        <div>
                            <p
                                class="text-sm font-medium text-gray-400 dark:text-gray-500"
                            >
                                New Orders
                            </p>
                            <p
                                class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl dark:text-gray-100"
                            >
                                {{ newOrdersCount }}
                            </p>
                            <p
                                class="mt-1 flex items-center gap-1 text-xs text-emerald-400"
                            >
                                <TrendingUp class="h-3.5 w-3.5" />
                                <span>+8% this week</span>
                            </p>
                        </div>
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-500/10 text-blue-400"
                        >
                            <ShoppingBag class="h-6 w-6" />
                        </div>
                    </div>
                </div>
                <!-- Backend: pass stats.pendingOrdersCount -->
                <div
                    class="relative overflow-hidden rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900/50"
                >
                    <div class="flex items-start justify-between">
                        <div>
                            <p
                                class="text-sm font-medium text-gray-400 dark:text-gray-500"
                            >
                                Pending Orders
                            </p>
                            <p
                                class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl dark:text-gray-100"
                            >
                                {{ pendingOrdersCount }}
                            </p>
                            <p
                                class="mt-1 flex items-center gap-1 text-xs text-amber-400"
                            >
                                <TrendingDown class="h-3.5 w-3.5" />
                                <span>Requires attention</span>
                            </p>
                        </div>
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-500/10 text-amber-400"
                        >
                            <ShoppingBag class="h-6 w-6" />
                        </div>
                    </div>
                </div>
            </section>

            <!-- Filter bar -->
            <section
                class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900/50"
                aria-label="Filter orders"
            >
                <div
                    class="flex flex-col gap-4 sm:flex-row sm:items-center sm:gap-4"
                >
                    <div class="relative flex-1">
                        <Search
                            class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-500 dark:text-gray-400"
                        />
                        <Input
                            v-model="searchQuery"
                            type="search"
                            placeholder="Order ID / Customer"
                            class="h-11 w-full rounded-lg border-gray-300 bg-white pr-4 pl-10 text-sm focus:border-primary focus:ring-1 focus:ring-primary dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
                            aria-label="Search by Order ID or Customer"
                            @keydown.enter="applyFilters"
                        />
                    </div>
                    <label
                        for="admin-order-status"
                        class="sr-only"
                        >Order Status</label
                    >
                    <select
                        id="admin-order-status"
                        v-model="statusFilter"
                        class="h-11 appearance-none rounded-lg border border-gray-300 bg-white pr-10 pl-4 text-sm text-gray-700 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
                    >
                        <option value="">All statuses</option>
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                        <option value="processing">Processing</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                        <div class="flex items-center gap-2">
                            <Calendar
                                class="h-4 w-4 text-gray-500 dark:text-gray-400"
                            />
                            <input
                                v-model="dateFrom"
                                type="date"
                                aria-label="Date from"
                                class="h-11 rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-700 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
                            />
                        </div>
                        <span class="text-gray-500 dark:text-gray-400">–</span>
                        <input
                            v-model="dateTo"
                            type="date"
                            aria-label="Date to"
                            class="h-11 rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-700 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
                        />
                        <Button
                            type="button"
                            class="h-11 rounded-lg bg-primary px-5 font-medium text-white shadow-lg shadow-blue-500/20 hover:bg-primary/90"
                            @click="applyFilters"
                        >
                            Apply
                        </Button>
                    </div>
                </div>
            </section>

            <!-- Data table card -->
            <section
                class="overflow-hidden rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900/50"
                aria-label="Orders table"
            >
                <div class="hidden overflow-x-auto sm:block">
                    <!-- Desktop table -->
                    <table
                        class="w-full min-w-[800px] border-collapse text-left text-sm"
                        role="table"
                    >
                        <thead>
                            <tr
                                class="border-b border-gray-200 bg-white/50 dark:border-gray-800 dark:bg-gray-900/50"
                            >
                                <th
                                    class="px-5 py-4 font-medium text-gray-800 dark:text-white"
                                >
                                    Order ID
                                </th>
                                <th
                                    class="px-5 py-4 font-medium text-gray-800 dark:text-white"
                                >
                                    Customer
                                </th>
                                <th
                                    class="px-5 py-4 font-medium text-gray-800 dark:text-white"
                                >
                                    Date
                                </th>
                                <th
                                    class="px-5 py-4 font-medium text-gray-800 dark:text-white"
                                >
                                    Total
                                </th>
                                <th
                                    class="px-5 py-4 font-medium text-gray-800 dark:text-white"
                                >
                                    Payment
                                </th>
                                <th
                                    class="px-5 py-4 font-medium text-gray-800 dark:text-white"
                                >
                                    Status
                                </th>
                                <th
                                    class="w-24 px-5 py-4 text-right font-medium text-gray-800 dark:text-white"
                                    aria-label="Actions"
                                >
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="order in orderList"
                                :key="order.id"
                                class="border-b border-gray-200 transition-colors hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-900/50"
                            >
                                <td class="px-5 py-4">
                                    <Link
                                        v-if="order.id"
                                        :href="
                                            route('admin.orders.show', order.id)
                                        "
                                        class="font-medium text-primary hover:text-primary/80 hover:underline"
                                    >
                                        #{{ order.order_number ?? order.id }}
                                    </Link>
                                    <span
                                        v-else
                                        class="text-gray-500 dark:text-gray-400"
                                        >—</span
                                    >
                                </td>
                                <td
                                    class="px-5 py-4 text-gray-800 dark:text-white"
                                >
                                    {{ order.customer_name ?? '—' }}
                                </td>
                                <td
                                    class="px-5 py-4 text-gray-400 dark:text-gray-500"
                                >
                                    {{ formatDate(order.created_at) }}
                                </td>
                                <td
                                    class="px-5 py-4 font-medium text-gray-900 dark:text-gray-100"
                                >
                                    {{ formatMoney(order.total) }}
                                </td>
                                <td class="px-5 py-4">
                                    <span
                                        class="inline-flex items-center gap-1.5 text-gray-800 dark:text-white"
                                    >
                                        <component
                                            :is="
                                                paymentDisplay(
                                                    order.payment_method,
                                                ).icon
                                            "
                                            class="h-4 w-4 text-gray-500 dark:text-gray-400"
                                        />
                                        {{
                                            paymentDisplay(order.payment_method)
                                                .label
                                        }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <span
                                        :class="[
                                            'inline-flex rounded-full border px-2.5 py-0.5 text-xs font-medium',
                                            statusBadge(order.status).class,
                                        ]"
                                    >
                                        {{ statusBadge(order.status).label }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <Link
                                        v-if="order.id"
                                        :href="
                                            route('admin.orders.show', order.id)
                                        "
                                        aria-label="View order"
                                    >
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            class="h-9 gap-2 text-gray-400 hover:bg-gray-50 dark:text-gray-500 dark:hover:bg-gray-900/50 dark:hover:text-gray-100"
                                        >
                                            <Eye class="h-4 w-4" />
                                            View
                                        </Button>
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile card list -->
                <div class="block sm:hidden">
                    <div
                        v-for="order in orderList"
                        :key="order.id"
                        class="flex flex-wrap items-center gap-3 border-b border-gray-200 px-4 py-4 last:border-b-0 dark:border-gray-800"
                    >
                        <div class="min-w-0 flex-1">
                            <Link
                                v-if="order.id"
                                :href="route('admin.orders.show', order.id)"
                                class="font-medium text-primary hover:text-primary/80 hover:underline"
                            >
                                #{{ order.order_number ?? order.id }}
                            </Link>
                            <p class="text-sm text-gray-400 dark:text-gray-500">
                                {{ order.customer_name ?? '—' }} ·
                                {{ formatDate(order.created_at) }}
                            </p>
                            <p
                                class="mt-1 text-sm font-medium text-gray-900 dark:text-gray-100"
                            >
                                {{ formatMoney(order.total) }}
                            </p>
                            <span
                                :class="[
                                    'mt-2 inline-flex rounded-full border px-2.5 py-0.5 text-xs font-medium',
                                    statusBadge(order.status).class,
                                ]"
                            >
                                {{ statusBadge(order.status).label }}
                            </span>
                        </div>
                        <Link
                            v-if="order.id"
                            :href="route('admin.orders.show', order.id)"
                        >
                            <Button
                                variant="ghost"
                                size="sm"
                                class="gap-2 text-gray-400 dark:text-gray-500"
                            >
                                <Eye class="h-4 w-4" />
                                View
                            </Button>
                        </Link>
                    </div>
                </div>

                <!-- Empty state -->
                <div
                    v-if="orderList.length === 0"
                    class="px-6 py-16 text-center text-gray-500 dark:text-gray-400"
                >
                    No orders found. Try adjusting search or filters.
                </div>

                <!-- Pagination -->
                <div
                    v-if="orders && orderList.length > 0"
                    class="border-t border-gray-200 px-4 py-3 dark:border-gray-800"
                >
                    <Pagination :meta="orders" />
                </div>
            </section>
        </div>

        <!-- Real-time notification: Backend can trigger via Echo/Broadcast or polling -->
        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="translate-x-full opacity-0"
            enter-to-class="translate-x-0 opacity-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="translate-x-0 opacity-100"
            leave-to-class="translate-x-full opacity-0"
        >
            <div
                v-if="showNewOrderNotification"
                class="fixed top-20 right-6 z-50 flex max-w-sm items-start gap-3 rounded-xl border border-emerald-500/20 bg-gray-900 p-4 shadow-sm ring-1 shadow-black/40 ring-gray-200/5 dark:border-emerald-500/20 dark:bg-gray-900 dark:ring-gray-800/5"
                role="alert"
                aria-live="polite"
            >
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-500/20 text-emerald-400"
                >
                    <ShoppingBag class="h-5 w-5" />
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-medium text-white">New order received</p>
                    <p class="mt-0.5 text-sm text-gray-400 dark:text-gray-500">
                        Order #ORD-5502
                    </p>
                </div>
                <Button
                    variant="ghost"
                    size="icon"
                    class="h-8 w-8 shrink-0 text-gray-500 hover:text-white dark:text-gray-500 dark:hover:text-gray-100"
                    aria-label="Dismiss notification"
                    @click="dismissNotification"
                >
                    <X class="h-4 w-4" />
                </Button>
            </div>
        </Transition>
    </div>
</template>
