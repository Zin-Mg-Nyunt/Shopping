<script setup>
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Label } from '@/components/ui/label';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowLeft,
    ChevronDown,
    FileText,
    Mail,
    MapPin,
    Phone,
    Printer,
    User,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

// Backend: pass order (id, order_number, customer_name, customer_email, customer_phone, created_at, total, payment_method, status, shipping_address, bank_slip_url, transaction_id, items[], activity_log[], admin_notes)
const props = defineProps({
    order: Object,
});

const order = computed(() => props.order ?? {});
const items = computed(() => order.value.items ?? []);
// Backend: activity_log[] with { user_name, action, created_at }
const activityLog = computed(() => order.value.activity_log ?? []);
const adminNotes = ref(props.order?.admin_notes ?? '');
const transactionId = ref(props.order?.transaction_id ?? '');
const bankSlipOpen = ref(false);

const steps = [
    { key: 'pending', label: 'Pending' },
    { key: 'paid', label: 'Paid' },
    { key: 'shipped', label: 'Shipped' },
    { key: 'delivered', label: 'Delivered' },
];

function stepIndex(status) {
    const i = steps.findIndex((s) => s.key === (status || '').toLowerCase());
    return i >= 0 ? i : 0;
}

const currentStep = computed(() => stepIndex(order.value.status));

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

// Backend: persist admin_notes and transaction_id on submit; handle Update Status and Print Invoice
function onUpdateStatus(value) {
    // TODO: POST/PATCH to update order status
}

function onPrintInvoice() {
    // TODO: open print view or download PDF
}

function onSaveNotes() {
    // TODO: POST/PATCH to save admin_notes
}

// Backend: order.bank_slip_url or bank_slip_path for thumbnail and full image
const bankSlipUrl = computed(
    () => order.value.bank_slip_url ?? order.value.bank_slip_path ?? null,
);

defineOptions({
    layout: AdminLayout,
});
</script>

<template>
    <Head :title="`Order ${order.order_number ?? order.id} - Admin`" />
    <div class="min-h-[60vh] bg-gray-50 dark:bg-gray-950">
        <div class="space-y-4 p-4 sm:space-y-6 sm:p-6 lg:p-8">
            <!-- Back link -->
            <Link
                :href="route('admin.orders')"
                class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
            >
                <ArrowLeft class="h-4 w-4" />
                Back to Orders
            </Link>

            <!-- Top Navigation Bar: Order ID + Status Stepper + Update Status + Print -->
            <header
                class="flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:rounded-2xl sm:p-6 xl:flex-row xl:items-center xl:justify-between xl:gap-6 dark:border-gray-800 dark:bg-gray-900/50"
            >
                <div
                    class="min-w-0 space-y-3 sm:space-y-4 xl:flex-1 xl:overflow-hidden"
                >
                    <h1
                        class="truncate text-lg font-semibold tracking-tight text-gray-900 sm:text-xl md:text-2xl dark:text-gray-100"
                    >
                        Order #{{ order.order_number ?? order.id }}
                    </h1>
                    <!-- Status Stepper: scroll when space is tight (mobile/tablet/small laptop) -->
                    <nav
                        class="-mx-1 overflow-x-auto px-1 pb-1 xl:mx-0 xl:px-0"
                        aria-label="Order status progress"
                    >
                        <div
                            class="flex min-w-max items-center gap-1 sm:gap-2 md:gap-4"
                        >
                            <template
                                v-for="(step, i) in steps"
                                :key="step.key"
                            >
                                <div class="flex shrink-0 items-center">
                                    <span
                                        :class="[
                                            'flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-medium transition-colors sm:h-9 sm:w-9',
                                            i < currentStep
                                                ? 'bg-primary text-primary-foreground'
                                                : i === currentStep
                                                  ? 'bg-primary text-primary-foreground ring-4 ring-primary/20'
                                                  : 'bg-muted text-muted-foreground',
                                        ]"
                                    >
                                        <span v-if="i < currentStep">✓</span>
                                        <span v-else>{{ i + 1 }}</span>
                                    </span>
                                    <span
                                        :class="[
                                            'ml-1.5 hidden text-sm font-medium md:ml-2 md:inline',
                                            i <= currentStep
                                                ? 'text-foreground'
                                                : 'text-muted-foreground',
                                        ]"
                                    >
                                        {{ step.label }}
                                    </span>
                                    <span
                                        v-if="i < steps.length - 1"
                                        class="mx-1 h-0.5 w-4 shrink-0 rounded sm:mx-2 sm:w-8"
                                        :class="
                                            i < currentStep
                                                ? 'bg-primary'
                                                : 'bg-muted'
                                        "
                                        aria-hidden="true"
                                    />
                                </div>
                            </template>
                        </div>
                    </nav>
                </div>
                <div class="flex shrink-0 flex-col gap-2 sm:flex-row sm:gap-3">
                    <!-- Backend: submit selected status to update order -->
                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button
                                variant="outline"
                                class="w-full gap-2 border-gray-200 bg-white shadow-sm sm:w-auto dark:border-gray-800 dark:bg-gray-900/50 dark:hover:bg-gray-800/50"
                            >
                                Update Status
                                <ChevronDown class="h-4 w-4" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent
                            align="end"
                            class="w-48 dark:bg-gray-900/50"
                        >
                            <DropdownMenuItem
                                v-for="s in steps"
                                :key="s.key"
                                @click="onUpdateStatus(s.key)"
                                class="dark:hover:bg-gray-800/50"
                            >
                                {{ s.label }}
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                    <Button
                        class="w-full gap-2 bg-primary text-primary-foreground shadow-sm hover:opacity-90 sm:w-auto dark:bg-primary dark:text-primary-foreground"
                        @click="onPrintInvoice"
                    >
                        <Printer class="h-4 w-4 shrink-0" />
                        Print Invoice
                    </Button>
                </div>
            </header>

            <!-- Two columns: Left = Ordered Items, Right = Sidebar -->
            <div class="grid gap-4 sm:gap-6 lg:grid-cols-3">
                <!-- Left column (wide): Ordered Items -->
                <div class="min-w-0 lg:col-span-2">
                    <section
                        class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm sm:rounded-2xl dark:border-gray-800 dark:bg-gray-900/50"
                        aria-label="Ordered items"
                    >
                        <div
                            class="border-b border-gray-200 px-4 py-3 sm:px-6 sm:py-4 dark:border-gray-800"
                        >
                            <h2
                                class="font-semibold text-gray-900 dark:text-gray-100"
                            >
                                Ordered Items
                            </h2>
                        </div>

                        <!-- Mobile: card list -->
                        <div class="block sm:hidden">
                            <div
                                v-for="(item, index) in items"
                                :key="index"
                                class="flex gap-3 border-b border-gray-200 px-4 py-3 last:border-b-0 dark:border-gray-800"
                            >
                                <div
                                    class="h-14 w-14 shrink-0 overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-800"
                                >
                                    <img
                                        v-if="item.thumbnail_url"
                                        :src="item.thumbnail_url"
                                        :alt="item.product_name"
                                        class="h-full w-full object-cover"
                                    />
                                    <div
                                        v-else
                                        class="flex h-full w-full items-center justify-center text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        —
                                    </div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p
                                        class="truncate font-medium text-gray-900 dark:text-gray-100"
                                    >
                                        {{ item.product_name ?? '—' }}
                                    </p>
                                    <p
                                        v-if="item.variant"
                                        class="text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        {{ item.variant }}
                                    </p>
                                    <p
                                        class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                                    >
                                        {{ item.quantity ?? 0 }} ×
                                        {{ formatMoney(item.unit_price) }}
                                    </p>
                                </div>
                                <p
                                    class="shrink-0 text-right font-medium text-gray-900 dark:text-gray-100"
                                >
                                    {{ formatMoney(item.line_total) }}
                                </p>
                            </div>
                            <div
                                v-if="items.length === 0"
                                class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400"
                            >
                                No items in this order.
                            </div>
                            <div
                                v-if="items.length > 0"
                                class="flex justify-end border-t border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-800 dark:bg-gray-900/50"
                            >
                                <div class="text-right">
                                    <p class="text-xs text-muted-foreground">
                                        Grand Total
                                    </p>
                                    <p
                                        class="text-lg font-semibold text-foreground"
                                    >
                                        {{ formatMoney(order.total) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Desktop: table -->
                        <div class="hidden overflow-x-auto sm:block">
                            <table
                                class="w-full min-w-[500px] border-collapse text-left text-sm"
                                role="table"
                            >
                                <thead>
                                    <tr
                                        class="border-b border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-900/50"
                                    >
                                        <th
                                            class="w-16 px-6 py-3 font-medium text-gray-500 dark:text-gray-400"
                                            scope="col"
                                        >
                                            —
                                        </th>
                                        <th
                                            class="px-6 py-3 font-medium text-gray-500 dark:text-gray-400"
                                            scope="col"
                                        >
                                            Product
                                        </th>
                                        <th
                                            class="w-24 px-6 py-3 text-right font-medium text-gray-500 dark:text-gray-400"
                                            scope="col"
                                        >
                                            Qty
                                        </th>
                                        <th
                                            class="w-28 px-6 py-3 text-right font-medium text-gray-500 dark:text-gray-400"
                                            scope="col"
                                        >
                                            Unit price
                                        </th>
                                        <th
                                            class="w-28 px-6 py-3 text-right font-medium text-gray-500 dark:text-gray-400"
                                            scope="col"
                                        >
                                            Subtotal
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(item, index) in items"
                                        :key="index"
                                        class="border-b border-gray-200 last:border-b-0 dark:border-gray-800"
                                    >
                                        <td class="px-6 py-4">
                                            <div
                                                class="h-14 w-14 overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-800"
                                            >
                                                <img
                                                    v-if="item.thumbnail_url"
                                                    :src="item.thumbnail_url"
                                                    :alt="item.product_name"
                                                    class="h-full w-full object-cover"
                                                />
                                                <div
                                                    v-else
                                                    class="flex h-full w-full items-center justify-center text-xs text-gray-500 dark:text-gray-400"
                                                >
                                                    —
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p
                                                class="font-medium text-gray-900 dark:text-gray-100"
                                            >
                                                {{ item.product_name ?? '—' }}
                                            </p>
                                            <p
                                                v-if="item.variant"
                                                class="mt-0.5 text-xs text-gray-500 dark:text-gray-400"
                                            >
                                                {{ item.variant }}
                                            </p>
                                        </td>
                                        <td
                                            class="px-6 py-4 text-right text-gray-500 dark:text-gray-400"
                                        >
                                            {{ item.quantity ?? 0 }}
                                        </td>
                                        <td
                                            class="px-6 py-4 text-right text-gray-500 dark:text-gray-400"
                                        >
                                            {{ formatMoney(item.unit_price) }}
                                        </td>
                                        <td
                                            class="px-6 py-4 text-right font-medium text-gray-900 dark:text-gray-100"
                                        >
                                            {{ formatMoney(item.line_total) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div
                            v-if="items.length === 0"
                            class="hidden px-6 py-12 text-center text-gray-500 sm:block dark:text-gray-400"
                        >
                            No items in this order.
                        </div>
                        <!-- Grand Total (desktop table) -->
                        <div
                            v-if="items.length > 0"
                            class="hidden justify-end border-t border-gray-200 bg-gray-50 px-6 py-4 sm:flex dark:border-gray-800 dark:bg-gray-900/50"
                        >
                            <div class="text-right">
                                <p
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                >
                                    Grand Total
                                </p>
                                <p
                                    class="text-xl font-semibold text-gray-900 dark:text-gray-100"
                                >
                                    {{ formatMoney(order.total) }}
                                </p>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Right column: Customer, Shipping, Payment Verification -->
                <div class="space-y-4 sm:space-y-6">
                    <!-- Customer Info Card -->
                    <section
                        class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:rounded-2xl sm:p-5 dark:border-gray-800 dark:bg-gray-900/50"
                        aria-label="Customer information"
                    >
                        <h2
                            class="mb-4 font-semibold text-gray-900 dark:text-gray-100"
                        >
                            Customer
                        </h2>
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400"
                                >
                                    <User class="h-4 w-4" />
                                </div>
                                <div>
                                    <p
                                        class="font-medium text-gray-900 dark:text-gray-100"
                                    >
                                        {{ order.customer_name ?? '—' }}
                                    </p>
                                    <p
                                        class="text-sm text-gray-500 dark:text-gray-400"
                                    >
                                        Customer name
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400"
                                >
                                    <Mail class="h-4 w-4" />
                                </div>
                                <div>
                                    <p
                                        class="font-medium text-gray-900 dark:text-gray-100"
                                    >
                                        {{ order.customer_email ?? '—' }}
                                    </p>
                                    <p
                                        class="text-sm text-gray-500 dark:text-gray-400"
                                    >
                                        Email
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400"
                                >
                                    <Phone class="h-4 w-4" />
                                </div>
                                <div>
                                    <p
                                        class="font-medium text-gray-900 dark:text-gray-100"
                                    >
                                        {{ order.customer_phone ?? '—' }}
                                    </p>
                                    <p
                                        class="text-sm text-gray-500 dark:text-gray-400"
                                    >
                                        Phone
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Shipping Address Card + static map preview -->
                    <section
                        class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:rounded-2xl sm:p-5 dark:border-gray-800 dark:bg-gray-900/50"
                        aria-label="Shipping address"
                    >
                        <h2
                            class="mb-4 font-semibold text-gray-900 dark:text-gray-100"
                        >
                            Shipping Address
                        </h2>
                        <div class="flex items-start gap-3">
                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400"
                            >
                                <MapPin class="h-4 w-4" />
                            </div>
                            <p class="text-sm text-gray-900 dark:text-gray-100">
                                {{ order.shipping_address ?? '—' }}
                            </p>
                        </div>
                        <!-- Backend: provide embed URL or lat/lng for Google Maps iframe -->
                        <div
                            class="mt-3 overflow-hidden rounded-lg border border-gray-200 bg-gray-50 sm:mt-4 sm:rounded-xl dark:border-gray-800 dark:bg-gray-900/50"
                        >
                            <div
                                class="flex h-24 items-center justify-center text-gray-500 sm:h-32 dark:text-gray-400"
                            >
                                <!-- TODO: Replace with Google Maps embed or static map image from backend -->
                                <span class="text-xs">Map preview</span>
                            </div>
                        </div>
                    </section>

                    <!-- Payment Verification Card: Bank slip thumbnail + Transaction ID -->
                    <section
                        class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:rounded-2xl sm:p-5 dark:border-gray-800 dark:bg-gray-900/50"
                        aria-label="Payment verification"
                    >
                        <h2
                            class="mb-4 font-semibold text-gray-900 dark:text-gray-100"
                        >
                            Payment Verification
                        </h2>
                        <p
                            class="mb-3 text-sm text-gray-500 dark:text-gray-400"
                        >
                            Bank slip (screenshot)
                        </p>
                        <button
                            v-if="bankSlipUrl"
                            type="button"
                            class="block w-full overflow-hidden rounded-xl border border-gray-200 bg-gray-50 transition-shadow hover:shadow-md focus:ring-2 focus:ring-ring focus:outline-none dark:border-gray-800 dark:bg-gray-900/50"
                            @click="bankSlipOpen = true"
                        >
                            <img
                                :src="bankSlipUrl"
                                alt="Bank slip"
                                class="h-32 w-full object-contain sm:h-40"
                            />
                        </button>
                        <div
                            v-else
                            class="flex h-24 items-center justify-center rounded-lg border border-dashed border-gray-200 bg-gray-50 text-sm text-gray-500 sm:h-32 sm:rounded-xl dark:border-gray-800 dark:bg-gray-900/50 dark:text-gray-400"
                        >
                            No slip uploaded
                        </div>
                        <div class="mt-4">
                            <Label
                                for="transaction-id"
                                class="text-sm font-medium text-gray-900 dark:text-gray-100"
                            >
                                Transaction ID
                            </Label>
                            <input
                                id="transaction-id"
                                type="text"
                                v-model="transactionId"
                                placeholder="Enter transaction ID"
                                class="mt-1.5 w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-500 focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none dark:border-gray-800 dark:bg-gray-900/50 dark:text-gray-100 dark:placeholder:text-gray-400"
                            />
                            <!-- Backend: bind to order.transaction_id and save on blur or submit -->
                        </div>
                    </section>

                    <Link :href="route('admin.orders')">
                        <Button
                            variant="outline"
                            class="w-full border-gray-200 bg-white shadow-sm transition-all dark:border-gray-800 dark:bg-gray-900/50"
                        >
                            <ArrowLeft class="mr-2 h-4 w-4" />
                            Back to Orders
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- Footer: Internal Admin Notes + Activity Log -->
            <footer class="grid gap-4 sm:gap-6 lg:grid-cols-2">
                <section
                    class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:rounded-2xl sm:p-6 dark:border-gray-800 dark:bg-gray-900/50"
                    aria-label="Internal admin notes"
                >
                    <h2
                        class="mb-3 font-semibold text-gray-900 dark:text-gray-100"
                    >
                        Internal Admin Notes
                    </h2>
                    <textarea
                        v-model="adminNotes"
                        rows="4"
                        placeholder="Add notes visible only to admins..."
                        class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 placeholder:text-gray-500 focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none dark:border-gray-800 dark:bg-gray-900/50 dark:text-gray-100 dark:placeholder:text-gray-400"
                    />
                    <Button
                        size="sm"
                        class="mt-3 bg-primary text-primary-foreground hover:opacity-90 dark:bg-primary dark:text-primary-foreground"
                        @click="onSaveNotes"
                    >
                        Save notes
                    </Button>
                    <!-- Backend: persist admin_notes on Save -->
                </section>

                <section
                    class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:rounded-2xl sm:p-6 dark:border-gray-800 dark:bg-gray-900/50"
                    aria-label="Activity log"
                >
                    <h2
                        class="mb-4 font-semibold text-gray-900 dark:text-gray-100"
                    >
                        Activity Log
                    </h2>
                    <ul
                        v-if="activityLog.length > 0"
                        class="space-y-3"
                    >
                        <li
                            v-for="(entry, i) in activityLog"
                            :key="i"
                            class="flex items-start gap-2 border-b border-gray-200 pb-3 last:border-0 last:pb-0 sm:gap-3 dark:border-gray-800"
                        >
                            <div
                                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-gray-100 sm:h-8 sm:w-8 dark:bg-gray-800"
                            >
                                <FileText
                                    class="h-4 w-4 text-gray-500 dark:text-gray-400"
                                />
                            </div>
                            <div>
                                <p
                                    class="text-xs font-medium wrap-break-word text-gray-900 sm:text-sm dark:text-gray-100"
                                >
                                    {{ entry.user_name ?? 'System' }} —
                                    {{ entry.action ?? 'Updated status' }}
                                </p>
                                <p
                                    class="mt-0.5 text-xs text-gray-500 dark:text-gray-400"
                                >
                                    {{ formatDate(entry.created_at) }}
                                </p>
                            </div>
                        </li>
                    </ul>
                    <p
                        v-else
                        class="text-sm text-gray-500 dark:text-gray-400"
                    >
                        No activity yet.
                    </p>
                    <!-- Backend: pass activity_log[] with user_name, action, created_at -->
                </section>
            </footer>
        </div>
    </div>

    <!-- Bank slip expand on click -->
    <Dialog v-model:open="bankSlipOpen">
        <DialogContent
            class="w-[calc(100vw-2rem)] max-w-2xl overflow-hidden p-0 sm:w-full"
        >
            <DialogHeader class="p-4 pb-0 sm:p-6">
                <DialogTitle>Bank slip</DialogTitle>
            </DialogHeader>
            <div
                v-if="bankSlipUrl"
                class="p-4 sm:p-6"
            >
                <img
                    :src="bankSlipUrl"
                    alt="Bank slip (full size)"
                    class="max-h-[70vh] w-full rounded-lg object-contain"
                />
            </div>
        </DialogContent>
    </Dialog>
</template>
