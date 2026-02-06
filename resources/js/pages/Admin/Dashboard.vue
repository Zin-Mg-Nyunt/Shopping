<script setup>
import RevenueLineChart from '@/components/charts/RevenueLineChart.vue';
import TopProductsBarChart from '@/components/charts/TopProductsBarChart.vue';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useAppearance } from '@/composables/useAppearance';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import {
    AlertTriangle,
    BarChart3,
    DollarSign,
    LineChart,
    ShoppingCart,
    TrendingUp,
    Users,
} from 'lucide-vue-next';

// Placeholder data - replace with Inertia props when backend is ready
const stats = [
    {
        title: 'Total Sales',
        value: '$24,580',
        change: '+12.5%',
        trend: 'up',
        icon: DollarSign,
        iconBg: 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400',
    },
    {
        title: 'New Orders',
        value: '156',
        change: '+8',
        trend: 'up',
        icon: ShoppingCart,
        iconBg: 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
    },
    {
        title: 'Total Customers',
        value: '1,248',
        change: '+24',
        trend: 'up',
        icon: Users,
        iconBg: 'bg-violet-100 text-violet-600 dark:bg-violet-900/30 dark:text-violet-400',
    },
    {
        title: 'Low Stock',
        value: '12',
        change: 'Items',
        trend: 'warning',
        icon: AlertTriangle,
        iconBg: 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400',
    },
];

const recentOrders = [
    {
        id: 'ORD-001',
        customer: 'John Smith',
        amount: '$129.99',
        status: 'Pending',
    },
    {
        id: 'ORD-002',
        customer: 'Sarah Johnson',
        amount: '$89.50',
        status: 'Shipped',
    },
    {
        id: 'ORD-003',
        customer: 'Michael Brown',
        amount: '$256.00',
        status: 'Delivered',
    },
    {
        id: 'ORD-004',
        customer: 'Emily Davis',
        amount: '$45.99',
        status: 'Pending',
    },
    {
        id: 'ORD-005',
        customer: 'David Wilson',
        amount: '$189.75',
        status: 'Shipped',
    },
];

const { resolvedAppearance } = useAppearance();

function getStatusClasses(status) {
    switch (status.toLowerCase()) {
        case 'delivered':
            return 'border-emerald-200 bg-emerald-100 text-emerald-800 dark:border-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300';
        case 'shipped':
            return 'border-blue-200 bg-blue-100 text-blue-800 dark:border-blue-800 dark:bg-blue-900/40 dark:text-blue-300';
        case 'pending':
            return 'border-amber-200 bg-amber-100 text-amber-800 dark:border-amber-800 dark:bg-amber-900/40 dark:text-amber-300';
        default:
            return 'border-gray-200 bg-gray-100 text-gray-800 dark:border-gray-700 dark:bg-gray-800/40 dark:text-gray-300';
    }
}
defineOptions({
    layout: AdminLayout,
});
</script>

<template>
    <Head title="Admin Dashboard" />

    <div class="space-y-6">
        <!-- Page title -->
        <div>
            <h1
                class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100"
            >
                Dashboard
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Welcome back! Here's what's happening with your store today.
            </p>
        </div>

        <!-- Statistics cards -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <Card
                v-for="stat in stats"
                :key="stat.title"
                class="overflow-hidden border-gray-200/80 bg-white shadow-sm transition-shadow hover:shadow-md dark:border-gray-800 dark:bg-gray-900/50 dark:hover:shadow-lg"
            >
                <CardContent class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="space-y-2">
                            <p
                                class="text-sm font-medium text-gray-500 dark:text-gray-400"
                            >
                                {{ stat.title }}
                            </p>
                            <p
                                class="text-2xl font-bold text-gray-900 dark:text-gray-100"
                            >
                                {{ stat.value }}
                            </p>
                            <p
                                v-if="stat.trend === 'up'"
                                class="flex items-center gap-1 text-sm font-medium text-emerald-600 dark:text-emerald-400"
                            >
                                <TrendingUp class="h-4 w-4" />
                                {{ stat.change }}
                                <span
                                    class="font-normal text-gray-500 dark:text-gray-400"
                                    >vs last month</span
                                >
                            </p>
                            <p
                                v-else-if="stat.trend === 'warning'"
                                class="text-sm font-medium text-red-600 dark:text-red-400"
                            >
                                {{ stat.change }} need attention
                            </p>
                        </div>
                        <div
                            :class="[
                                'flex h-12 w-12 shrink-0 items-center justify-center rounded-xl',
                                stat.iconBg,
                            ]"
                        >
                            <component
                                :is="stat.icon"
                                class="h-6 w-6"
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Charts section -->
        <div class="grid gap-6 lg:grid-cols-2">
            <!-- Revenue line chart -->
            <Card
                class="overflow-hidden border-gray-200/80 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900/50"
            >
                <CardHeader class="pb-2">
                    <CardTitle
                        class="flex items-center gap-2 text-base font-semibold text-gray-900 dark:text-gray-100"
                    >
                        <LineChart
                            class="h-5 w-5 text-gray-600 dark:text-gray-400"
                        />
                        Revenue Line Chart
                    </CardTitle>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Monthly revenue overview
                    </p>
                </CardHeader>
                <CardContent>
                    <RevenueLineChart :key="resolvedAppearance" />
                </CardContent>
            </Card>

            <!-- Top selling products bar chart -->
            <Card
                class="overflow-hidden border-gray-200/80 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900/50"
            >
                <CardHeader class="pb-2">
                    <CardTitle
                        class="flex items-center gap-2 text-base font-semibold text-gray-900 dark:text-gray-100"
                    >
                        <BarChart3
                            class="h-5 w-5 text-gray-600 dark:text-gray-400"
                        />
                        Top Selling Products
                    </CardTitle>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Best performers this month
                    </p>
                </CardHeader>
                <CardContent>
                    <TopProductsBarChart :key="resolvedAppearance" />
                </CardContent>
            </Card>
        </div>

        <!-- Recent orders table -->
        <Card
            class="overflow-hidden border-gray-200/80 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900/50"
        >
            <CardHeader class="pb-4">
                <CardTitle
                    class="text-base font-semibold text-gray-900 dark:text-gray-100"
                >
                    Recent Orders
                </CardTitle>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Latest customer orders
                </p>
            </CardHeader>
            <CardContent class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[500px]">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50/80">
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold tracking-wider text-gray-500 uppercase dark:text-gray-400"
                                >
                                    Order ID
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold tracking-wider text-gray-500 uppercase dark:text-gray-400"
                                >
                                    Customer
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold tracking-wider text-gray-500 uppercase dark:text-gray-400"
                                >
                                    Total
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold tracking-wider text-gray-500 uppercase dark:text-gray-400"
                                >
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="divide-y divide-gray-100 dark:divide-gray-800"
                        >
                            <tr
                                v-for="order in recentOrders"
                                :key="order.id"
                                class="transition-colors hover:bg-gray-50/50 dark:hover:bg-gray-800/50"
                            >
                                <td class="px-6 py-4">
                                    <span
                                        class="font-mono text-sm font-medium text-gray-900 dark:text-gray-100"
                                    >
                                        {{ order.id }}
                                    </span>
                                </td>
                                <td
                                    class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300"
                                >
                                    {{ order.customer }}
                                </td>
                                <td
                                    class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100"
                                >
                                    {{ order.amount }}
                                </td>
                                <td class="px-6 py-4">
                                    <Badge
                                        variant="outline"
                                        :class="getStatusClasses(order.status)"
                                    >
                                        {{ order.status }}
                                    </Badge>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
