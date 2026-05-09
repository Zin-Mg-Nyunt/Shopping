<script setup>
import { Head } from '@inertiajs/vue3';
import { Search, ShieldCheck, UserCheck, Users } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import AdminDashboardLayout from '@/layouts/AdminDashboardLayout.vue';
import { Input } from '@/components/ui/input';

defineOptions({ layout: AdminDashboardLayout });

const props = defineProps({
    customers: { type: Array, default: () => [] },
});

const search = ref('');

const filteredCustomers = computed(() => {
    const keyword = search.value.trim().toLowerCase();

    if (!keyword) {
        return props.customers;
    }

    return props.customers.filter((customer) => {
        const name = (customer.name ?? '').toLowerCase();
        const email = (customer.email ?? '').toLowerCase();

        return name.includes(keyword) || email.includes(keyword);
    });
});

function isLoyalCustomer(customer) {
    if (!customer?.created_at) {
        return false;
    }

    const joinedAt = new Date(customer.created_at).getTime();
    const now = Date.now();
    const daysSinceJoin = (now - joinedAt) / (1000 * 60 * 60 * 24);

    return daysSinceJoin >= 90;
}

const loyalCount = computed(
    () =>
        props.customers.filter((customer) => isLoyalCustomer(customer)).length,
);

function formatDate(value) {
    if (!value) {
        return '-';
    }

    return new Date(value).toLocaleDateString();
}
</script>

<template>
    <Head title="Admin · Customers" />

    <div class="space-y-6">
        <div
            class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between"
        >
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-foreground">
                    Customer list
                </h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Review customer accounts and membership activity.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
            <div class="rounded-xl border border-border bg-card p-4 shadow-sm">
                <p class="text-xs text-muted-foreground">Total customers</p>
                <p class="mt-1 text-2xl font-bold text-foreground">
                    {{ props.customers.length }}
                </p>
                <div
                    class="mt-2 flex items-center gap-2 text-xs text-muted-foreground"
                >
                    <Users class="h-3.5 w-3.5" />
                    Registered accounts
                </div>
            </div>

            <div class="rounded-xl border border-border bg-card p-4 shadow-sm">
                <p class="text-xs text-muted-foreground">Loyal customers</p>
                <p class="mt-1 text-2xl font-bold text-foreground">
                    {{ loyalCount }}
                </p>
                <div
                    class="mt-2 flex items-center gap-2 text-xs text-muted-foreground"
                >
                    <UserCheck class="h-3.5 w-3.5" />
                    Joined over 90 days
                </div>
            </div>

            <div class="rounded-xl border border-border bg-card p-4 shadow-sm">
                <p class="text-xs text-muted-foreground">Normal customers</p>
                <p class="mt-1 text-2xl font-bold text-foreground">
                    {{ props.customers.length - loyalCount }}
                </p>
                <div
                    class="mt-2 flex items-center gap-2 text-xs text-muted-foreground"
                >
                    <Users class="h-3.5 w-3.5" />
                    Recently joined customers
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-border bg-card p-4 shadow-sm">
            <div class="relative max-w-md">
                <Search
                    class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                />
                <Input
                    v-model="search"
                    type="search"
                    placeholder="Search by name or email..."
                    class="h-10 border-border bg-background pl-9 focus-visible:border-[#FF6600] focus-visible:ring-[#FF6600]/25"
                />
            </div>
        </div>

        <div
            class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm"
        >
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left text-sm">
                    <thead>
                        <tr
                            class="border-b border-border bg-muted/40 text-muted-foreground"
                        >
                            <th class="px-4 py-3 font-semibold">Name</th>
                            <th class="px-4 py-3 font-semibold">Email</th>
                            <th class="px-4 py-3 font-semibold">Role</th>
                            <th class="px-4 py-3 font-semibold">Joined</th>
                            <th class="px-4 py-3 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <tr
                            v-for="customer in filteredCustomers"
                            :key="customer.id"
                            class="transition hover:bg-muted/30"
                        >
                            <td class="px-4 py-3 font-medium text-foreground">
                                {{ customer.name }}
                            </td>
                            <td class="px-4 py-3 text-muted-foreground">
                                {{ customer.email }}
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex rounded-full border border-border bg-muted/60 px-2.5 py-1 text-xs font-medium text-foreground capitalize"
                                >
                                    {{ customer.role || 'customer' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-muted-foreground">
                                {{ formatDate(customer.created_at) }}
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold"
                                    :class="
                                        isLoyalCustomer(customer)
                                            ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300'
                                            : 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300'
                                    "
                                >
                                    <ShieldCheck class="h-3.5 w-3.5" />
                                    {{
                                        isLoyalCustomer(customer)
                                            ? 'Loyal customer'
                                            : 'Normal customer'
                                    }}
                                </span>
                            </td>
                        </tr>

                        <tr v-if="filteredCustomers.length === 0">
                            <td
                                colspan="5"
                                class="px-4 py-10 text-center text-sm text-muted-foreground"
                            >
                                No customers found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
