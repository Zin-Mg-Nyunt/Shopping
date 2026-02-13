<script setup>
import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import UserLayout from '@/layouts/UserLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ChevronRight, Heart, MapPin, Package, User } from 'lucide-vue-next';
import { computed } from 'vue';

defineOptions({
    name: 'UserDashboard',
    layout: UserLayout,
});

const page = usePage();
const auth = computed(() => page.props.auth);
const userName = computed(() => auth.value?.user?.name ?? 'Guest');

// UI-only mock data
const loyaltyPoints = 1250;
const activeOrders = 2;

const recentOrders = [
    { id: 'ORD-2847', date: '2025-02-12', amount: 89.99, status: 'Shipped' },
    { id: 'ORD-2841', date: '2025-02-10', amount: 124.5, status: 'Processing' },
    { id: 'ORD-2838', date: '2025-02-08', amount: 45.0, status: 'Pending' },
    { id: 'ORD-2832', date: '2025-02-05', amount: 199.0, status: 'Shipped' },
    { id: 'ORD-2829', date: '2025-02-02', amount: 67.25, status: 'Shipped' },
];

const quickLinks = [
    { title: 'My Orders', href: '#', icon: Package },
    { title: 'Edit Profile', href: route('profile.edit'), icon: User },
    { title: 'Shipping Addresses', href: '#', icon: MapPin },
    { title: 'My Wishlist', href: '#', icon: Heart },
];

function badgeVariant(status) {
    const s = status?.toLowerCase();
    if (s === 'shipped') return 'default';
    if (s === 'pending') return 'secondary';
    return 'outline';
}
</script>

<template>
    <Head title="Dashboard" />
    <div class="space-y-10">
        <!-- Header: Welcome + summary cards -->
        <header>
            <h1
                class="mb-6 font-serif text-2xl font-bold tracking-tight text-foreground sm:text-3xl"
            >
                Hello, {{ userName }}
            </h1>
            <div class="grid gap-4 sm:grid-cols-2">
                <Card class="border-border bg-card shadow-sm">
                    <CardHeader class="pb-2">
                        <CardDescription class="text-muted-foreground">
                            Loyalty points
                        </CardDescription>
                        <CardTitle class="text-2xl font-semibold text-primary">
                            {{ loyaltyPoints.toLocaleString() }}
                        </CardTitle>
                    </CardHeader>
                </Card>
                <Card class="border-border bg-card shadow-sm">
                    <CardHeader class="pb-2">
                        <CardDescription class="text-muted-foreground">
                            Active orders
                        </CardDescription>
                        <CardTitle class="text-2xl font-semibold text-foreground">
                            {{ activeOrders }}
                        </CardTitle>
                    </CardHeader>
                </Card>
            </div>
        </header>

        <!-- Recent Orders -->
        <section>
            <div class="mb-4 flex items-center justify-between">
                <h2
                    class="font-serif text-lg font-semibold tracking-tight text-foreground"
                >
                    Recent orders
                </h2>
                <Link
                    href="#"
                    class="text-sm font-medium text-primary hover:text-primary/90 hover:underline focus-visible:rounded focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none"
                >
                    View all
                </Link>
            </div>
            <Card
                class="overflow-hidden border-border bg-card shadow-sm"
            >
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr
                                    class="border-b border-border bg-muted/50"
                                >
                                    <th
                                        class="px-4 py-3 font-medium text-muted-foreground sm:px-6"
                                    >
                                        Order ID
                                    </th>
                                    <th
                                        class="px-4 py-3 font-medium text-muted-foreground sm:px-6"
                                    >
                                        Date
                                    </th>
                                    <th
                                        class="px-4 py-3 font-medium text-muted-foreground sm:px-6"
                                    >
                                        Amount
                                    </th>
                                    <th
                                        class="px-4 py-3 font-medium text-muted-foreground sm:px-6"
                                    >
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="order in recentOrders"
                                    :key="order.id"
                                    class="border-b border-border transition-colors last:border-0 hover:bg-muted/50"
                                >
                                    <td
                                        class="px-4 py-3 font-medium text-foreground sm:px-6"
                                    >
                                        {{ order.id }}
                                    </td>
                                    <td
                                        class="px-4 py-3 text-muted-foreground sm:px-6"
                                    >
                                        {{ order.date }}
                                    </td>
                                    <td
                                        class="px-4 py-3 text-foreground sm:px-6"
                                    >
                                        ${{ order.amount.toFixed(2) }}
                                    </td>
                                    <td class="px-4 py-3 sm:px-6">
                                        <Badge
                                            :variant="
                                                badgeVariant(order.status)
                                            "
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
        </section>

        <!-- Quick Links -->
        <section>
            <h2
                class="mb-4 font-serif text-lg font-semibold tracking-tight text-foreground"
            >
                Quick links
            </h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <Link
                    v-for="item in quickLinks"
                    :key="item.title"
                    :href="item.href"
                    class="group flex items-center gap-4 rounded-xl border border-border bg-card p-5 shadow-sm transition-all hover:border-primary/40 hover:shadow-md focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none"
                >
                    <span
                        class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-primary/15 text-primary"
                    >
                        <component
                            :is="item.icon"
                            class="size-6"
                            aria-hidden="true"
                        />
                    </span>
                    <span
                        class="font-medium text-foreground group-hover:text-primary"
                    >
                        {{ item.title }}
                    </span>
                    <ChevronRight
                        class="ml-auto size-5 shrink-0 text-muted-foreground transition-transform group-hover:translate-x-0.5 group-hover:text-primary"
                        aria-hidden="true"
                    />
                </Link>
            </div>
        </section>
    </div>
</template>
