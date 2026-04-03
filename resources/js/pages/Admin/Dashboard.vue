<script setup>
import { Head } from '@inertiajs/vue3';
import { AlertTriangle, DollarSign, ShoppingBag, Users } from 'lucide-vue-next';
import AdminDashboardLayout from '@/layouts/AdminDashboardLayout.vue';

defineOptions({
    layout: AdminDashboardLayout,
});

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
        key: 'users',
        label: 'Users',
        value: '3,402',
        sub: '+3% vs last week',
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

        <div
            class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4"
        >
            <div
                v-for="card in stats"
                :key="card.key"
                class="rounded-2xl border border-border bg-card p-5 shadow-sm transition hover:shadow-md"
            >
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground">
                            {{ card.label }}
                        </p>
                        <p
                            class="mt-2 text-3xl font-bold tabular-nums text-[#FF6600]"
                        >
                            {{ card.value }}
                        </p>
                        <p class="mt-1 text-xs text-muted-foreground">
                            {{ card.sub }}
                        </p>
                    </div>
                    <div
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary/12 text-[#FF6600]"
                    >
                        <component
                            :is="card.icon"
                            class="h-5 w-5"
                            stroke-width="2"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div
            class="rounded-2xl border border-border bg-card p-6 shadow-sm"
        >
            <h2 class="text-lg font-semibold text-foreground">
                Quick actions
            </h2>
            <p class="mt-1 text-sm text-muted-foreground">
                This area is ready for charts, recent orders, or activity feeds.
            </p>
        </div>
    </div>
</template>
