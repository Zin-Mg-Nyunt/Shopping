<script setup>
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Sheet, SheetContent, SheetDescription } from '@/components/ui/sheet';
import {
    DollarSign,
    Edit3,
    KeyRound,
    Mail,
    MapPin,
    MessageSquare,
    NotepadText,
    Phone,
    ShoppingCart,
    User,
} from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    open: {
        type: Boolean,
        default: false,
    },
    customer: {
        type: Object,
        default: null,
    },
    initialTab: {
        type: String,
        default: 'orders',
    },
});

const emit = defineEmits(['update:open']);

const isOpen = computed({
    get: () => props.open,
    set: (val) => emit('update:open', val),
});

const activeTab = ref('orders');
const adminNotes = ref('');

// =============================================================================
// MOCK DATA (UI ONLY) — Replace with backend data when API is ready
// =============================================================================

const mockOrders = [
    { id: 'ORD-2847', date: 'Feb 10, 2025', total: 89.99, status: 'Completed' },
    { id: 'ORD-2801', date: 'Feb 5, 2025', total: 156.5, status: 'Completed' },
    { id: 'ORD-2755', date: 'Jan 28, 2025', total: 42.0, status: 'Pending' },
    { id: 'ORD-2692', date: 'Jan 15, 2025', total: 234.75, status: 'Completed' },
];

const mockFeedbacks = [
    {
        id: 1,
        productName: 'Wireless Earbuds Pro',
        rating: 4,
        comment: 'Great sound quality but the battery life could be better. Overall satisfied.',
        date: 'Feb 8, 2025',
        replied: false,
    },
    {
        id: 2,
        productName: 'Classic Leather Wallet',
        rating: 5,
        comment: 'Excellent craftsmanship. Very happy with this purchase!',
        date: 'Jan 20, 2025',
        replied: true,
    },
];

const mockAddresses = [
    {
        type: 'Shipping',
        street: '123 Oak Street, Apt 4B',
        city: 'San Francisco',
        state: 'CA',
        zip: '94102',
        country: 'United States',
    },
    {
        type: 'Billing',
        street: '456 Pine Avenue',
        city: 'San Francisco',
        state: 'CA',
        zip: '94103',
        country: 'United States',
    },
];

const orders = computed(() => mockOrders);
const addresses = computed(() => mockAddresses);
const feedbacks = computed(() => mockFeedbacks);

const customerData = computed(() => {
    if (!props.customer) return null;
    return {
        name: props.customer.name,
        email: props.customer.email,
        phone: props.customer.phone ?? '+1 (555) 123-4567',
        avatar: props.customer.avatar,
        status: props.customer.status ?? 'active',
        ordersCount: props.customer.ordersCount ?? 0,
        totalSpent: props.customer.totalSpent ?? 0,
        joined: props.customer.joined ?? 'Oct 24, 2023',
    };
});

watch(
    () => props.customer,
    (c) => {
        if (c) {
            activeTab.value = props.initialTab;
            adminNotes.value = '';
        }
    },
);

watch(
    () => [props.open, props.initialTab],
    ([open, tab]) => {
        if (open && props.customer) {
            activeTab.value = tab;
        }
    },
);

function getStatusBadgeClass(status) {
    switch (status) {
        case 'active':
            return 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30';
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

function getOrderStatusClass(status) {
    switch (status?.toLowerCase()) {
        case 'completed':
            return 'bg-emerald-500/15 text-emerald-400';
        case 'pending':
            return 'bg-amber-500/15 text-amber-400';
        default:
            return 'bg-gray-500/15 text-gray-400';
    }
}

function getInitials(name) {
    if (!name) return '??';
    return name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
}

// =============================================================================
// HANDLERS (UI ONLY) — Wire up to backend when ready
// =============================================================================

function handleBlockUnblock() {
    // TODO: Toggle block status via backend
}

function handleResetPassword() {
    // TODO: Trigger password reset flow
}

function handleEditProfile() {
    // TODO: Open edit profile form/modal
}

function handleViewOrder(order) {
    // TODO: Navigate to order detail or open order slide-over
}

function handleSaveNote() {
    // TODO: Persist admin notes to backend
}

function handleReplyToFeedback(feedback) {
    // TODO: Open reply form / submit reply via backend
}

const tabs = [
    { id: 'orders', label: 'Order History', icon: ShoppingCart },
    { id: 'addresses', label: 'Address Book', icon: MapPin },
    { id: 'feedbacks', label: 'Feedbacks', icon: MessageSquare },
    { id: 'notes', label: 'Admin Notes', icon: NotepadText },
];
</script>

<template>
    <Sheet v-model:open="isOpen">
        <SheetContent
            side="right"
            class="flex w-full max-w-full flex-col gap-0 border-gray-800 bg-gray-900 p-0 text-gray-300 sm:max-w-xl dark:border-gray-800 dark:bg-gray-900"
        >
            <SheetDescription class="sr-only">
                Customer detail panel for {{ customerData?.name ?? 'customer' }}
            </SheetDescription>

            <template v-if="customerData">
                <!-- Header -->
                <div
                    class="flex shrink-0 items-start justify-between gap-4 border-b border-gray-800 px-4 py-4 pr-12 dark:border-gray-800"
                >
                    <div class="min-w-0 flex-1">
                        <h2
                            class="text-xl font-semibold tracking-tight text-white"
                        >
                            {{ customerData.name }}
                        </h2>
                        <p class="mt-0.5 text-sm text-gray-400">
                            Joined {{ customerData.joined }}
                        </p>
                    </div>
                </div>

                <!-- Scrollable Body -->
                <div
                    class="flex min-h-0 flex-1 flex-col overflow-y-auto"
                >
                    <!-- Profile Section -->
                    <div class="border-b border-gray-800 p-4 dark:border-gray-800">
                        <div class="flex flex-col items-center gap-4 sm:flex-row sm:items-start">
                            <Avatar
                                class="h-20 w-20 shrink-0 rounded-full border-2 border-gray-700"
                            >
                                <AvatarImage
                                    v-if="customerData.avatar"
                                    :src="customerData.avatar"
                                    :alt="customerData.name"
                                    class="object-cover"
                                />
                                <AvatarFallback
                                    class="rounded-full bg-gray-700 text-xl font-medium text-gray-300"
                                >
                                    {{ getInitials(customerData.name) }}
                                </AvatarFallback>
                            </Avatar>
                            <div class="flex-1 space-y-2 text-center sm:text-left">
                                <div class="flex flex-wrap items-center gap-2 justify-center sm:justify-start">
                                    <span
                                        :class="[
                                            'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium capitalize',
                                            getStatusBadgeClass(customerData.status),
                                        ]"
                                    >
                                        {{ customerData.status }}
                                    </span>
                                </div>
                                <div class="flex flex-col gap-1 text-sm text-gray-400">
                                    <div class="flex items-center gap-2">
                                        <Mail class="h-4 w-4 shrink-0 text-gray-500" />
                                        <a
                                            :href="`mailto:${customerData.email}`"
                                            class="hover:text-white"
                                        >
                                            {{ customerData.email }}
                                        </a>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <Phone class="h-4 w-4 shrink-0 text-gray-500" />
                                        <span>{{ customerData.phone }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stats Cards -->
                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <div
                                class="rounded-lg border border-gray-800 bg-gray-800/50 p-3 dark:border-gray-800"
                            >
                                <div class="flex items-center gap-2">
                                    <ShoppingCart class="h-4 w-4 text-gray-500" />
                                    <span class="text-xs font-medium text-gray-400">Total Orders</span>
                                </div>
                                <p class="mt-1 text-lg font-semibold text-white">
                                    {{ customerData.ordersCount }}
                                </p>
                            </div>
                            <div
                                class="rounded-lg border border-gray-800 bg-gray-800/50 p-3 dark:border-gray-800"
                            >
                                <div class="flex items-center gap-2">
                                    <DollarSign class="h-4 w-4 text-gray-500" />
                                    <span class="text-xs font-medium text-gray-400">Total Spent</span>
                                </div>
                                <p class="mt-1 text-lg font-semibold text-white">
                                    {{ formatCurrency(customerData.totalSpent) }}
                                </p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-4 flex flex-wrap gap-2">
                            <Button
                                variant="outline"
                                size="sm"
                                class="h-9 gap-1.5 border-gray-700 text-gray-300 hover:bg-gray-800 hover:text-white dark:border-gray-700"
                                @click="handleBlockUnblock"
                            >
                                {{ customerData.status === 'blocked' ? 'Unblock' : 'Block' }} Customer
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                class="h-9 gap-1.5 border-gray-700 text-gray-300 hover:bg-gray-800 hover:text-white dark:border-gray-700"
                                @click="handleResetPassword"
                            >
                                <KeyRound class="h-3.5 w-3.5" />
                                Reset Password
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                class="h-9 gap-1.5 border-gray-700 text-gray-300 hover:bg-gray-800 hover:text-white dark:border-gray-700"
                                @click="handleEditProfile"
                            >
                                <Edit3 class="h-3.5 w-3.5" />
                                Edit Profile
                            </Button>
                        </div>
                    </div>

                    <!-- Tabs -->
                    <div class="flex-1">
                        <div
                            class="sticky top-0 z-10 flex border-b border-gray-800 bg-gray-900 dark:border-gray-800 dark:bg-gray-900"
                        >
                            <button
                                v-for="tab in tabs"
                                :key="tab.id"
                                type="button"
                                class="flex flex-1 items-center justify-center gap-2 px-4 py-3 text-sm font-medium transition-colors"
                                :class="
                                    activeTab === tab.id
                                        ? 'border-b-2 border-primary text-white'
                                        : 'text-gray-400 hover:text-gray-300'
                                "
                                @click="activeTab = tab.id"
                            >
                                <component :is="tab.icon" class="h-4 w-4 shrink-0" />
                                {{ tab.label }}
                            </button>
                        </div>

                        <div class="p-4">
                            <!-- Tab 1: Order History -->
                            <div v-show="activeTab === 'orders'" class="space-y-3">
                                <div
                                    v-for="order in orders"
                                    :key="order.id"
                                    class="rounded-lg border border-gray-800 bg-gray-800/30 p-3 dark:border-gray-800"
                                >
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0 flex-1">
                                            <p class="font-medium text-white">
                                                {{ order.id }}
                                            </p>
                                            <p class="mt-0.5 text-xs text-gray-400">
                                                {{ order.date }} · {{ formatCurrency(order.total) }}
                                            </p>
                                            <span
                                                :class="[
                                                    'mt-2 inline-block rounded-full px-2 py-0.5 text-xs font-medium',
                                                    getOrderStatusClass(order.status),
                                                ]"
                                            >
                                                {{ order.status }}
                                            </span>
                                        </div>
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            class="h-8 shrink-0 text-gray-400 hover:text-white"
                                            @click="handleViewOrder(order)"
                                        >
                                            View Order
                                        </Button>
                                    </div>
                                </div>
                                <p
                                    v-if="orders.length === 0"
                                    class="py-8 text-center text-sm text-gray-500"
                                >
                                    No orders yet
                                </p>
                            </div>

                            <!-- Tab 2: Feedbacks -->
                            <div v-show="activeTab === 'feedbacks'" class="space-y-4">
                                <div
                                    v-for="feedback in feedbacks"
                                    :key="feedback.id"
                                    class="rounded-lg border p-4"
                                    :class="
                                        feedback.replied
                                            ? 'border-gray-800 bg-gray-800/30 dark:border-gray-800'
                                            : 'border-amber-500/30 bg-amber-500/5 dark:border-amber-500/30'
                                    "
                                >
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0 flex-1">
                                            <p class="font-medium text-white">
                                                {{ feedback.productName }}
                                            </p>
                                            <div class="mt-1 flex items-center gap-2">
                                                <span class="text-xs text-amber-400">
                                                    {{ feedback.rating }}/5
                                                </span>
                                                <span class="text-xs text-gray-500">
                                                    {{ feedback.date }}
                                                </span>
                                                <span
                                                    v-if="!feedback.replied"
                                                    class="rounded-full bg-red-500/20 px-2 py-0.5 text-xs font-medium text-red-400"
                                                >
                                                    Needs Reply
                                                </span>
                                            </div>
                                            <p class="mt-2 text-sm text-gray-400">
                                                {{ feedback.comment }}
                                            </p>
                                        </div>
                                        <Button
                                            v-if="!feedback.replied"
                                            size="sm"
                                            class="h-8 shrink-0"
                                            @click="handleReplyToFeedback(feedback)"
                                        >
                                            Reply
                                        </Button>
                                    </div>
                                </div>
                                <p
                                    v-if="feedbacks.length === 0"
                                    class="py-8 text-center text-sm text-gray-500"
                                >
                                    No product reviews yet
                                </p>
                            </div>

                            <!-- Tab 3: Address Book -->
                            <div v-show="activeTab === 'addresses'" class="space-y-4">
                                <div
                                    v-for="(addr, idx) in addresses"
                                    :key="idx"
                                    class="rounded-lg border border-gray-800 bg-gray-800/30 p-4 dark:border-gray-800"
                                >
                                    <div class="flex items-center gap-2">
                                        <MapPin class="h-4 w-4 text-gray-500" />
                                        <span class="text-sm font-medium text-gray-300">
                                            {{ addr.type }} Address
                                        </span>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-400">
                                        {{ addr.street }}
                                    </p>
                                    <p class="text-sm text-gray-400">
                                        {{ addr.city }}, {{ addr.state }} {{ addr.zip }}
                                    </p>
                                    <p class="text-sm text-gray-400">
                                        {{ addr.country }}
                                    </p>
                                </div>
                                <p
                                    v-if="addresses.length === 0"
                                    class="py-8 text-center text-sm text-gray-500"
                                >
                                    No saved addresses
                                </p>
                            </div>

                            <!-- Tab 4: Admin Notes -->
                            <div v-show="activeTab === 'notes'" class="space-y-4">
                                <textarea
                                    v-model="adminNotes"
                                    rows="6"
                                    placeholder="Add internal notes about this customer (e.g., 'Frequent returner', 'Always calls before buying')..."
                                    class="w-full rounded-lg border border-gray-700 bg-gray-800/50 px-3 py-2 text-sm text-white placeholder-gray-500 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none"
                                />
                                <Button
                                    size="sm"
                                    class="w-full sm:w-auto"
                                    @click="handleSaveNote"
                                >
                                    Save Note
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <div
                v-else
                class="flex flex-1 flex-col items-center justify-center p-8 text-gray-500"
            >
                <User class="mb-4 h-12 w-12" />
                <p class="text-sm">Select a customer to view details</p>
            </div>
        </SheetContent>
    </Sheet>
</template>
