<script setup>
import CustomerDetailPanel from '@/components/admin/CustomerDetailPanel.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import {
    Check,
    Download,
    ExternalLink,
    Filter,
    MoreHorizontal,
    Search,
    UserPlus,
    Users,
} from 'lucide-vue-next';
import { ref } from 'vue';

// =============================================================================
// MOCK DATA (UI ONLY) — Replace with backend props when API is ready
// =============================================================================

const stats = [
    {
        title: 'Total Customers',
        value: '2,847',
        trend: '+12%',
        trendUp: true,
        icon: Users,
        iconBg: 'bg-blue-500/10 text-blue-400',
    },
    {
        title: 'Active This Month',
        value: '1,923',
        trend: '+8%',
        trendUp: true,
        icon: UserPlus,
        iconBg: 'bg-emerald-500/10 text-emerald-400',
    },
    {
        title: 'New Registrations',
        value: '156',
        trend: '+24%',
        trendUp: true,
        icon: UserPlus,
        iconBg: 'bg-violet-500/10 text-violet-400',
    },
];

const statusFilterOptions = [
    { value: 'all', label: 'All' },
    { value: 'active', label: 'Active' },
    { value: 'inactive', label: 'Inactive' },
    { value: 'blocked', label: 'Blocked' },
];

const customers = [
    {
        id: 1,
        name: 'Sarah Johnson',
        email: 'sarah.johnson@example.com',
        avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Sarah',
        status: 'active',
        ordersCount: 12,
        totalSpent: 1240.0,
        joined: 'Oct 24, 2023',
        hasUnrepliedReview: true,
    },
    {
        id: 2,
        name: 'Michael Chen',
        email: 'michael.chen@example.com',
        avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Michael',
        status: 'active',
        ordersCount: 8,
        totalSpent: 890.5,
        joined: 'Nov 15, 2023',
        hasUnrepliedReview: false,
    },
    {
        id: 3,
        name: 'Emily Davis',
        email: 'emily.davis@example.com',
        avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Emily',
        status: 'inactive',
        ordersCount: 3,
        totalSpent: 245.0,
        joined: 'Jan 8, 2024',
        hasUnrepliedReview: true,
    },
    {
        id: 4,
        name: 'James Wilson',
        email: 'james.wilson@example.com',
        avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=James',
        status: 'blocked',
        ordersCount: 0,
        totalSpent: 0,
        joined: 'Sep 12, 2022',
        hasUnrepliedReview: false,
    },
    {
        id: 5,
        name: 'Olivia Martinez',
        email: 'olivia.martinez@example.com',
        avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Olivia',
        status: 'active',
        ordersCount: 24,
        totalSpent: 2890.75,
        joined: 'Mar 3, 2023',
        hasUnrepliedReview: false,
    },
];

const searchQuery = ref('');
const statusFilter = ref('all');
const detailPanelOpen = ref(false);
const selectedCustomer = ref(null);
const detailPanelInitialTab = ref('orders');

function getStatusBadgeClass(status) {
    switch (status) {
        case 'active':
            return 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30';
        case 'inactive':
            return 'bg-gray-500/15 text-gray-400 border-gray-500/30';
        case 'blocked':
            return 'bg-red-500/15 text-red-400 border-red-500/30';
        default:
            return 'bg-gray-500/15 text-gray-400 border-gray-500/30';
    }
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 2,
    }).format(amount);
}

// =============================================================================
// HANDLERS (UI ONLY) — Wire up to backend when ready
// =============================================================================

function handleSearch() {
    // TODO: Debounce and call backend search API
}

function handleStatusFilterChange(value) {
    statusFilter.value = value;
    // TODO: Apply filter via backend or client-side filter
}

function handleExport() {
    // TODO: Trigger CSV/Excel export from backend
}

function handleViewCustomer(customer, initialTab = 'orders') {
    selectedCustomer.value = customer;
    detailPanelInitialTab.value = initialTab;
    detailPanelOpen.value = true;
}

function handleOpenFeedbacks(customer) {
    handleViewCustomer(customer, 'feedbacks');
}

function getInitials(name) {
    return name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
}

defineOptions({
    layout: AdminLayout,
});
</script>

<template>
    <Head title="Customers - Admin" />
    <div class="space-y-6 font-[Inter,sans-serif]">
        <!-- Page Header -->
        <div>
            <h1
                class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white"
            >
                Customers
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Manage your customer base and view their activity
            </p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="stat in stats"
                :key="stat.title"
                class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900/50"
            >
                <div class="flex items-start justify-between">
                    <div class="min-w-0 flex-1">
                        <p
                            class="text-sm font-medium text-gray-500 dark:text-gray-400"
                        >
                            {{ stat.title }}
                        </p>
                        <p
                            class="mt-2 text-2xl font-semibold text-gray-900 tabular-nums dark:text-white"
                        >
                            {{ stat.value }}
                        </p>
                        <p
                            :class="[
                                'mt-1 flex items-center gap-1 text-xs font-medium',
                                stat.trendUp
                                    ? 'text-emerald-500'
                                    : 'text-red-500',
                            ]"
                        >
                            {{ stat.trend }} from last month
                        </p>
                    </div>
                    <div
                        :class="[
                            'flex h-11 w-11 shrink-0 items-center justify-center rounded-lg',
                            stat.iconBg,
                        ]"
                    >
                        <component
                            :is="stat.icon"
                            class="h-5 w-5"
                            aria-hidden="true"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Controls -->
        <div
            class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900/50"
        >
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="relative max-w-md flex-1">
                    <Search
                        class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400"
                        aria-hidden="true"
                    />
                    <Input
                        v-model="searchQuery"
                        type="search"
                        placeholder="Search by name or email..."
                        class="h-10 w-full rounded-lg border-gray-300 bg-white pr-4 pl-10 text-sm focus:border-primary focus:ring-1 focus:ring-primary dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
                        aria-label="Search customers"
                        @input="handleSearch"
                    />
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Status Filter -->
                    <div class="relative">
                        <select
                            :value="statusFilter"
                            class="h-10 appearance-none rounded-lg border border-gray-300 bg-white pr-10 pl-4 text-sm text-gray-700 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
                            aria-label="Filter by status"
                            @change="
                                handleStatusFilterChange($event.target.value)
                            "
                        >
                            <option
                                v-for="opt in statusFilterOptions"
                                :key="opt.value"
                                :value="opt.value"
                            >
                                {{ opt.label }}
                            </option>
                        </select>
                        <Filter
                            class="pointer-events-none absolute top-1/2 right-3 h-4 w-4 -translate-y-1/2 text-gray-400"
                            aria-hidden="true"
                        />
                    </div>
                    <!-- Export Button -->
                    <Button
                        variant="outline"
                        size="sm"
                        class="h-10 gap-2"
                        @click="handleExport"
                    >
                        <Download class="h-4 w-4" />
                        Export
                    </Button>
                </div>
            </div>
        </div>

        <!-- Customer Table -->
        <div
            class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900/50"
        >
            <div class="overflow-x-auto">
                <table
                    class="w-full min-w-[900px] border-collapse text-left text-sm"
                    role="table"
                >
                    <thead>
                        <tr
                            class="border-b border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-800/50"
                        >
                            <th
                                class="px-4 py-3.5 font-medium text-gray-700 dark:text-gray-300"
                            >
                                Customer
                            </th>
                            <th
                                class="px-4 py-3.5 font-medium text-gray-700 dark:text-gray-300"
                            >
                                Status
                            </th>
                            <th
                                class="px-4 py-3.5 font-medium text-gray-700 dark:text-gray-300"
                            >
                                Orders
                            </th>
                            <th
                                class="px-4 py-3.5 font-medium text-gray-700 dark:text-gray-300"
                            >
                                Total Spent
                            </th>
                            <th
                                class="px-4 py-3.5 font-medium text-gray-700 dark:text-gray-300"
                            >
                                Joined
                            </th>
                            <th
                                class="px-4 py-3.5 font-medium text-gray-700 dark:text-gray-300"
                            >
                                Support Needs
                            </th>
                            <th
                                class="w-32 px-4 py-3.5 text-right font-medium text-gray-700 dark:text-gray-300"
                                aria-label="Actions"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="customer in customers"
                            :key="customer.id"
                            class="border-b border-gray-100 transition-colors duration-150 last:border-b-0 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-800/50"
                        >
                            <!-- Customer -->
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-3">
                                    <Avatar
                                        class="h-10 w-10 shrink-0 rounded-full border-2 border-gray-200 dark:border-gray-700"
                                    >
                                        <AvatarImage
                                            :src="customer.avatar"
                                            :alt="customer.name"
                                            class="object-cover"
                                        />
                                        <AvatarFallback
                                            class="rounded-full bg-gray-200 text-sm font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-200"
                                        >
                                            {{ getInitials(customer.name) }}
                                        </AvatarFallback>
                                    </Avatar>
                                    <div class="min-w-0">
                                        <p
                                            class="font-medium text-gray-900 dark:text-white"
                                        >
                                            {{ customer.name }}
                                        </p>
                                        <p
                                            class="truncate text-xs text-gray-500 dark:text-gray-400"
                                        >
                                            {{ customer.email }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <!-- Status -->
                            <td class="px-4 py-3.5">
                                <span
                                    :class="[
                                        'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium capitalize',
                                        getStatusBadgeClass(customer.status),
                                    ]"
                                >
                                    {{ customer.status }}
                                </span>
                            </td>
                            <!-- Orders -->
                            <td
                                class="px-4 py-3.5 text-gray-700 dark:text-gray-300"
                            >
                                {{ customer.ordersCount }} Orders
                            </td>
                            <!-- Total Spent -->
                            <td
                                class="px-4 py-3.5 font-medium text-gray-700 dark:text-gray-300"
                            >
                                {{ formatCurrency(customer.totalSpent) }}
                            </td>
                            <!-- Joined -->
                            <td
                                class="px-4 py-3.5 text-gray-600 dark:text-gray-400"
                            >
                                {{ customer.joined }}
                            </td>
                            <!-- Support Needs -->
                            <td class="px-4 py-3.5">
                                <button
                                    v-if="customer.hasUnrepliedReview"
                                    type="button"
                                    class="inline-flex cursor-pointer items-center gap-1.5 rounded-full border border-red-500/40 bg-red-500/15 px-2.5 py-1 text-xs font-medium text-red-400 transition-colors hover:bg-red-500/25"
                                    @click="handleOpenFeedbacks(customer)"
                                >
                                    <span
                                        class="relative flex h-2 w-2 shrink-0"
                                        aria-hidden="true"
                                    >
                                        <span
                                            class="absolute inline-flex h-full w-full animate-ping rounded-full bg-red-400 opacity-75"
                                        />
                                        <span
                                            class="relative inline-flex h-2 w-2 rounded-full bg-red-500"
                                        />
                                    </span>
                                    Needs Reply
                                </button>
                                <span
                                    v-else
                                    class="inline-flex items-center justify-center text-emerald-500"
                                    title="All reviews replied"
                                >
                                    <Check class="h-5 w-5" />
                                </span>
                            </td>
                            <!-- Actions -->
                            <td class="px-4 py-3.5">
                                <div
                                    class="flex items-center justify-end gap-1"
                                >
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="h-8 gap-1.5 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
                                        @click="handleViewCustomer(customer)"
                                    >
                                        <ExternalLink class="h-3.5 w-3.5" />
                                        View
                                    </Button>
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                class="h-8 w-8 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                                                aria-label="More options"
                                            >
                                                <MoreHorizontal
                                                    class="h-4 w-4"
                                                    aria-hidden="true"
                                                />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent
                                            align="end"
                                            class="w-44"
                                        >
                                            <DropdownMenuItem
                                                @click="
                                                    handleViewCustomer(customer)
                                                "
                                            >
                                                <ExternalLink
                                                    class="mr-2 h-4 w-4"
                                                />
                                                View Details
                                            </DropdownMenuItem>
                                            <DropdownMenuItem>
                                                Send Email
                                            </DropdownMenuItem>
                                            <DropdownMenuItem
                                                variant="destructive"
                                            >
                                                Block Customer
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Empty state (shown when no customers) -->
            <div
                v-if="customers.length === 0"
                class="px-4 py-16 text-center"
            >
                <Users
                    class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500"
                    aria-hidden="true"
                />
                <p
                    class="mt-4 text-sm font-medium text-gray-900 dark:text-white"
                >
                    No customers found
                </p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Try adjusting your search or filter criteria
                </p>
            </div>
        </div>

        <!-- Customer Detail Slide-over Panel -->
        <CustomerDetailPanel
            v-model:open="detailPanelOpen"
            :customer="selectedCustomer"
            :initial-tab="detailPanelInitialTab"
        />
    </div>
</template>
