<script setup>
import { Head } from '@inertiajs/vue3';
import { AlertTriangle, DollarSign, ShoppingBag, Users } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { useAppearance } from '@/composables/useAppearance';
import AdminDashboardLayout from '@/layouts/AdminDashboardLayout.vue';

const { appearance } = useAppearance();

defineOptions({
    layout: AdminDashboardLayout,
});

const activeStatus = ref('orders');

const stats = [
    {
        key: 'orders',
        label: 'Orders',
        value: '1,248',
        sub: '+12% vs last week',
        icon: ShoppingBag,
    },
    {
        key: 'revenue',
        label: 'Revenue',
        value: '$48.2k',
        sub: '+8% vs last week',
        icon: DollarSign,
    },
    {
        key: 'customers',
        label: 'Customers',
        value: '3,402',
        sub: '+3% vs last month',
        icon: Users,
    },
    {
        key: 'alerts',
        label: 'Alerts',
        value: '7',
        sub: '2 require action',
        icon: AlertTriangle,
    },
];

const cardColorMap = {
    orders: '#2563EB',
    revenue: '#059669',
    customers: '#7C3AED',
    alerts: '#F97316',
};

const weekCategories = computed(() => {
    const labels = [];

    for (let i = 6; i >= 0; i--) {
        const d = new Date();
        d.setHours(0, 0, 0, 0);
        d.setDate(d.getDate() - i);
        labels.push(
            d.toLocaleDateString('en-GB', {
                weekday: 'short',
                day: 'numeric',
                month: 'short',
            }),
        );
    }

    return labels;
});

const monthCategories = [
    'January',
    'February',
    'March',
    'April',
    'May',
    'June',
    'July',
    'August',
    'September',
    'October',
    'November',
    'December',
];

const commonChart = computed(() => {
    return {
        chart: {
            toolbar: { show: false },
            zoom: { enabled: false },
            fontFamily: 'inherit',
        },
        dataLabels: { enabled: false },
        grid: {
            borderColor: 'hsl(var(--border))',
            strokeDashArray: 4,
        },
        xaxis: {
            labels: {
                style: { colors: '#374151', fontSize: '12px', fontWeight: 600 },
            },
            axisBorder: { color: 'hsl(var(--border))' },
            axisTicks: { color: 'hsl(var(--border))' },
        },
        yaxis: {
            labels: {
                style: { colors: '#1F2937', fontSize: '12px', fontWeight: 600 },
            },
        },
        tooltip: { theme: appearance.value === 'dark' ? 'dark' : 'light' },
    };
});

const responsiveColumnWidth = computed(() => {
    const width = window.innerWidth;

    if (width < 640) {
        return '70%';
    }

    if (width < 1024) {
        return '60%';
    }

    return '40%';
});

const ordersSeries = [
    {
        name: 'Orders',
        data: [10, 50, 20, 35, 40, 30, 39],
    },
];

const ordersChartOptions = computed(() => ({
    ...commonChart.value,
    chart: { ...commonChart.value.chart, type: 'bar' },
    colors: [cardColorMap.orders],
    plotOptions: {
        bar: {
            borderRadius: 5,
            columnWidth: responsiveColumnWidth.value,
            horizontal: false,
        },
    },
    xaxis: {
        ...commonChart.value.xaxis,
        categories: weekCategories.value,
    },
    yaxis: {
        ...commonChart.value.yaxis,
        min: 10,
        max: 100,
        tickAmount: 9,
    },
}));

const revenueSeries = [
    {
        name: 'Revenue',
        data: [4200, 5800, 3100, 7200, 5100, 4800, 6500],
    },
];

const revenueChartOptions = computed(() => ({
    ...commonChart.value,
    chart: { ...commonChart.value.chart, type: 'bar' },
    colors: [cardColorMap.revenue],
    plotOptions: {
        bar: {
            borderRadius: 6,
            columnWidth: responsiveColumnWidth.value,
            horizontal: false,
        },
    },
    xaxis: {
        ...commonChart.value.xaxis,
        categories: weekCategories.value,
    },
    yaxis: {
        ...commonChart.value.yaxis,
        min: 1000,
        max: 10000,
        tickAmount: 9,
        labels: {
            ...commonChart.value.yaxis.labels,
            formatter: (val) => {
                if (typeof val === 'number') {
                    return val.toLocaleString();
                }

                return String(val);
            },
        },
    },
}));

const customersSeries = [
    {
        name: 'Customers',
        data: [120, 180, 240, 310, 280, 350, 420, 390, 450, 520, 610, 720],
    },
];

const customersChartOptions = computed(() => ({
    ...commonChart.value,
    chart: { ...commonChart.value.chart, type: 'line' },
    colors: [cardColorMap.customers],
    stroke: { width: 5, curve: 'straight' },
    markers: {
        size: 4,
        strokeColors: cardColorMap.customers,
        strokeWidth: 2,
        hover: { size: 6 },
    },
    xaxis: {
        ...commonChart.value.xaxis,
        categories: monthCategories,
        tickAmount: 11,
    },
    yaxis: {
        ...commonChart.value.yaxis,
        min: 10,
        max: 1000,
        tickAmount: 5,
    },
}));

const selectedCard = computed(() =>
    stats.find((stat) => stat.key === activeStatus.value),
);

const urgentAlerts = [
    {
        severity: 'danger',
        type: 'Out of stock',
        title: 'Wireless Mouse (Black)',
        details: '0 units remaining',
        actionLabel: 'Restock',
    },
    {
        severity: 'warning',
        type: 'Low stock',
        title: 'USB-C Cable (1m)',
        details: 'Only 4 units left',
        actionLabel: 'Review',
    },
    {
        severity: 'info',
        type: 'Reply needed',
        title: 'Customer: Aye Aye',
        details: 'Asked about order #INV-1023 shipping',
        actionLabel: 'Reply',
    },
];
</script>

<template>
    <Head title="Admin · Overview" />

    <div class="space-y-8">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-foreground">
                Overview
            </h1>
            <p class="mt-1 text-sm text-muted-foreground">
                Key metrics for your store at a glance.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div v-for="card in stats" :key="card.key" class="min-w-0">
                <button
                    type="button"
                    class="w-full rounded-2xl border bg-card p-5 text-left shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                    :class="[
                        activeStatus === card.key
                            ? 'border-[#FF6600] ring-2 ring-[#FF6600]/25'
                            : 'border-border',
                        card.key === 'alerts'
                            ? 'cursor-default'
                            : 'cursor-pointer',
                    ]"
                    @click="
                        card.key == 'alerts'
                            ? (activeStatus = activeStatus)
                            : (activeStatus = card.key)
                    "
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p
                                class="text-sm font-medium text-muted-foreground"
                            >
                                {{ card.label }}
                            </p>
                            <p
                                class="mt-2 text-3xl font-bold text-foreground tabular-nums"
                            >
                                {{ card.value }}
                            </p>
                            <p
                                class="mt-1 text-xs font-medium text-foreground/80"
                            >
                                {{ card.sub }}
                            </p>
                        </div>
                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-white"
                            :style="{ backgroundColor: cardColorMap[card.key] }"
                        >
                            <component
                                :is="card.icon"
                                class="h-5 w-5"
                                stroke-width="2"
                            />
                        </div>
                    </div>
                </button>
            </div>
        </div>

        <div class="flex flex-col gap-4 xl:flex-row xl:gap-2">
            <div
                class="w-full rounded-2xl border border-border bg-card p-6 shadow-sm xl:w-[70%]"
            >
                <div v-if="!selectedCard">
                    <p class="text-base font-semibold text-foreground">
                        Select a status card to show its chart.
                    </p>
                    <p class="mt-1 text-sm text-foreground/80">
                        Orders, Revenue, and Customers have charts. Alerts has
                        no chart.
                    </p>
                </div>

                <template v-else-if="selectedCard.key === 'orders'">
                    <p class="mb-3 text-lg font-semibold text-foreground">
                        Orders (last 7 days)
                    </p>
                    <apexchart
                        height="420"
                        type="bar"
                        :options="ordersChartOptions"
                        :series="ordersSeries"
                    />
                </template>

                <template v-else-if="selectedCard.key === 'revenue'">
                    <p class="mb-3 text-lg font-semibold text-foreground">
                        Revenue (last 7 days)
                    </p>
                    <apexchart
                        height="420"
                        type="bar"
                        :options="revenueChartOptions"
                        :series="revenueSeries"
                    />
                </template>

                <template v-else-if="selectedCard.key === 'customers'">
                    <p class="mb-3 text-lg font-semibold text-foreground">
                        Customers (January to December)
                    </p>
                    <apexchart
                        height="420"
                        type="line"
                        :options="customersChartOptions"
                        :series="customersSeries"
                    />
                </template>
            </div>
            <div
                class="w-full flex-1 rounded-2xl border border-dashed border-border bg-muted/30 p-5"
            >
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-base font-semibold text-foreground">
                            Urgent actions
                        </p>
                        <p class="mt-1 text-sm text-foreground/80">
                            Items that need attention right now.
                        </p>
                    </div>
                    <span
                        class="inline-flex items-center rounded-full border border-border bg-card px-3 py-1 text-xs font-semibold text-foreground"
                    >
                        {{
                            stats.find((s) => s.key === 'alerts')?.value ?? '0'
                        }}
                        alerts
                    </span>
                </div>

                <div
                    class="mt-4 overflow-hidden rounded-xl border border-border bg-card"
                >
                    <table class="w-full table-fixed">
                        <thead class="bg-muted/30">
                            <tr
                                class="text-left text-xs font-semibold text-muted-foreground uppercase"
                            >
                                <th class="w-18 px-4 py-3">Type</th>
                                <th class="px-1 py-3">Item / Customer</th>
                                <th class="w-38 px-2 py-3">Details</th>
                                <th class="w-22 px-2 py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <tr
                                v-for="alert in urgentAlerts"
                                :key="alert.title"
                                class="group"
                            >
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex items-center text-xs font-semibold"
                                        :class="
                                            alert.severity === 'danger'
                                                ? 'text-red-700'
                                                : alert.severity === 'warning'
                                                  ? 'text-amber-800'
                                                  : 'text-sky-800'
                                        "
                                    >
                                        {{ alert.type }}
                                    </span>
                                </td>
                                <td class="px-1 py-3">
                                    <p
                                        class="text-sm font-semibold text-foreground"
                                    >
                                        {{ alert.title }}
                                    </p>
                                </td>
                                <td class="px-2 py-3">
                                    <p class="text-sm text-muted-foreground">
                                        {{ alert.details }}
                                    </p>
                                </td>
                                <td class="px-2 py-3">
                                    <button
                                        type="button"
                                        class="rounded-lg border border-border bg-card px-3 py-1 text-xs font-semibold text-foreground shadow-sm transition hover:bg-muted/30"
                                    >
                                        {{ alert.actionLabel }}
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
