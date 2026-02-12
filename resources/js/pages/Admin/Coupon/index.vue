<script setup>
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetFooter,
    SheetHeader,
    SheetTitle,
} from '@/components/ui/sheet';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import {
    Calendar,
    Copy,
    MoreHorizontal,
    Plus,
    Shuffle,
    Ticket,
    Trash2,
} from 'lucide-vue-next';
import { ref } from 'vue';

defineOptions({
    layout: AdminLayout,
});

// =============================================================================
// MOCK DATA (UI ONLY) — Replace with backend props when API is ready
// =============================================================================

const coupons = [
    {
        id: 1,
        code: 'SAVE20',
        discountType: 'percentage',
        value: 20,
        usedCount: 45,
        usageLimit: 100,
        expiryDate: '2025-12-31',
        status: 'active',
    },
    {
        id: 2,
        code: 'FLAT10',
        discountType: 'fixed',
        value: 10,
        usedCount: 100,
        usageLimit: 100,
        expiryDate: '2025-06-15',
        status: 'expired',
    },
    {
        id: 3,
        code: 'WELCOME15',
        discountType: 'percentage',
        value: 15,
        usedCount: 12,
        usageLimit: 500,
        expiryDate: '2026-01-01',
        status: 'scheduled',
    },
    {
        id: 4,
        code: 'SUMMER50',
        discountType: 'fixed',
        value: 50,
        usedCount: 23,
        usageLimit: 50,
        expiryDate: '2025-08-31',
        status: 'active',
    },
];

const createPanelOpen = ref(false);
const formCode = ref('');
const formDiscountType = ref('percentage');
const formDiscountValue = ref('');
const formMinPurchase = ref('');
const formUsageLimit = ref('');
const formStartDate = ref('');
const formEndDate = ref('');

const editPanelOpen = ref(false);
const editingCoupon = ref(null);
const editFormCode = ref('');
const editFormDiscountType = ref('percentage');
const editFormDiscountValue = ref('');
const editFormMinPurchase = ref('');
const editFormUsageLimit = ref('');
const editFormStartDate = ref('');
const editFormEndDate = ref('');

const selectInputClass =
    'h-10 w-full rounded-lg border border-gray-300 bg-white pl-4 pr-10 text-sm text-gray-700 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200';
const dateInputClass =
    'h-10 w-full rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-700 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 [color-scheme:dark]';

function getStatusBadgeClass(status) {
    switch (status) {
        case 'active':
            return 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30';
        case 'expired':
            return 'bg-red-500/15 text-red-400 border-red-500/30';
        case 'scheduled':
            return 'bg-amber-500/15 text-amber-400 border-amber-500/30';
        default:
            return 'bg-gray-500/15 text-gray-400 border-gray-500/30';
    }
}

function formatDiscountType(type) {
    return type === 'percentage' ? 'Percentage' : 'Fixed';
}

function formatValue(coupon) {
    return coupon.discountType === 'percentage'
        ? `${coupon.value}%`
        : `$${coupon.value}`;
}

function formatDate(dateStr) {
    if (!dateStr) return '—';
    return new Date(dateStr + 'T00:00:00').toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}

function openCreatePanel() {
    formCode.value = '';
    formDiscountType.value = 'percentage';
    formDiscountValue.value = '';
    formMinPurchase.value = '';
    formUsageLimit.value = '';
    formStartDate.value = '';
    formEndDate.value = '';
    createPanelOpen.value = true;
}

function closeCreatePanel() {
    createPanelOpen.value = false;
}

function generateRandomCode() {
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    let code = '';
    for (let i = 0; i < 8; i++) {
        code += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    formCode.value = code;
}

function copyCode(code) {
    navigator.clipboard?.writeText(code).catch(() => {});
    // Optional: toast on copy
}

function handleSubmitCreate() {
    // UI only — no backend
    closeCreatePanel();
}

function openEditPanel(coupon) {
    editingCoupon.value = coupon;
    editFormCode.value = coupon.code;
    editFormDiscountType.value = coupon.discountType;
    editFormDiscountValue.value = String(coupon.value);
    editFormMinPurchase.value =
        coupon.minPurchase != null ? String(coupon.minPurchase) : '';
    editFormUsageLimit.value = String(coupon.usageLimit);
    editFormStartDate.value = coupon.startDate || '';
    editFormEndDate.value = coupon.expiryDate || '';
    editPanelOpen.value = true;
}

function closeEditPanel() {
    editPanelOpen.value = false;
    editingCoupon.value = null;
}

function handleSubmitEdit() {
    // UI only — no backend
    closeEditPanel();
}
</script>

<template>
    <Head title="Coupons - Admin" />
    <div class="space-y-6 font-[Inter,sans-serif]">
        <!-- Top Header -->
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h1
                    class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white"
                >
                    Coupons
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Create and manage discount coupons
                </p>
            </div>
            <Button
                class="h-10 shrink-0 gap-2"
                @click="openCreatePanel"
            >
                <Plus class="h-4 w-4" />
                Create Coupon
            </Button>
        </div>

        <!-- Coupons Table -->
        <div
            class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900/80"
        >
            <div class="overflow-x-auto">
                <table
                    class="w-full min-w-[800px] border-collapse text-left text-sm"
                    role="table"
                >
                    <thead>
                        <tr
                            class="border-b border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-800/50"
                        >
                            <th
                                class="px-4 py-3.5 font-medium text-gray-700 dark:text-gray-300"
                            >
                                Code
                            </th>
                            <th
                                class="px-4 py-3.5 font-medium text-gray-700 dark:text-gray-300"
                            >
                                Discount Type
                            </th>
                            <th
                                class="px-4 py-3.5 font-medium text-gray-700 dark:text-gray-300"
                            >
                                Value
                            </th>
                            <th
                                class="px-4 py-3.5 font-medium text-gray-700 dark:text-gray-300"
                            >
                                Usage
                            </th>
                            <th
                                class="px-4 py-3.5 font-medium text-gray-700 dark:text-gray-300"
                            >
                                Expiry Date
                            </th>
                            <th
                                class="px-4 py-3.5 font-medium text-gray-700 dark:text-gray-300"
                            >
                                Status
                            </th>
                            <th
                                class="w-28 px-4 py-3.5 text-right font-medium text-gray-700 dark:text-gray-300"
                                aria-label="Actions"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="coupon in coupons"
                            :key="coupon.id"
                            class="border-b border-gray-100 transition-colors duration-150 last:border-b-0 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-800/50"
                        >
                            <td class="px-4 py-3.5">
                                <div
                                    class="flex items-center gap-2 font-mono font-medium text-gray-900 dark:text-white"
                                >
                                    <Ticket
                                        class="h-4 w-4 shrink-0 text-gray-400 dark:text-gray-500"
                                        aria-hidden="true"
                                    />
                                    {{ coupon.code }}
                                    <button
                                        type="button"
                                        class="rounded p-1 text-gray-400 transition-colors hover:bg-gray-200 hover:text-gray-700 dark:hover:bg-gray-700 dark:hover:text-gray-200"
                                        aria-label="Copy code"
                                        @click="copyCode(coupon.code)"
                                    >
                                        <Copy class="h-3.5 w-3.5" />
                                    </button>
                                </div>
                            </td>
                            <td
                                class="px-4 py-3.5 text-gray-700 dark:text-gray-300"
                            >
                                {{ formatDiscountType(coupon.discountType) }}
                            </td>
                            <td
                                class="px-4 py-3.5 font-medium text-gray-700 dark:text-gray-300"
                            >
                                {{ formatValue(coupon) }}
                            </td>
                            <td
                                class="px-4 py-3.5 text-gray-700 dark:text-gray-300"
                            >
                                {{ coupon.usedCount }}/{{
                                    coupon.usageLimit
                                }}
                                uses
                            </td>
                            <td
                                class="px-4 py-3.5 text-gray-600 dark:text-gray-400"
                            >
                                <span class="flex items-center gap-1.5">
                                    <Calendar class="h-3.5 w-3.5 shrink-0" />
                                    {{ formatDate(coupon.expiryDate) }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span
                                    :class="[
                                        'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium capitalize',
                                        getStatusBadgeClass(coupon.status),
                                    ]"
                                >
                                    {{ coupon.status }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div
                                    class="flex items-center justify-end gap-1"
                                >
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
                                                @click="openEditPanel(coupon)"
                                            >
                                                Edit Coupon
                                            </DropdownMenuItem>
                                            <DropdownMenuItem
                                                variant="destructive"
                                            >
                                                <Trash2 class="mr-2 h-4 w-4" />
                                                Delete
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Empty state -->
            <div
                v-if="coupons.length === 0"
                class="px-4 py-16 text-center"
            >
                <Ticket
                    class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500"
                    aria-hidden="true"
                />
                <p
                    class="mt-4 text-sm font-medium text-gray-900 dark:text-white"
                >
                    No coupons yet
                </p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Create your first coupon to offer discounts
                </p>
                <Button
                    class="mt-4 gap-2"
                    @click="openCreatePanel"
                >
                    <Plus class="h-4 w-4" />
                    Create Coupon
                </Button>
            </div>
        </div>

        <!-- Create Coupon — Slide-over Panel -->
        <Sheet v-model:open="createPanelOpen">
            <SheetContent
                side="right"
                class="sm:max-w-md"
            >
                <SheetHeader>
                    <SheetTitle>Create Coupon</SheetTitle>
                    <SheetDescription class="sr-only">
                        Form to create a new discount coupon
                    </SheetDescription>
                </SheetHeader>
                <form
                    class="flex flex-col gap-6 overflow-y-auto p-6 pr-2"
                    @submit.prevent="handleSubmitCreate"
                >
                    <div class="space-y-2">
                        <Label for="coupon-code">Coupon Code</Label>
                        <div class="flex gap-2">
                            <Input
                                id="coupon-code"
                                v-model="formCode"
                                type="text"
                                placeholder="e.g. SAVE20"
                                class="flex-1 font-mono uppercase"
                            />
                            <Button
                                type="button"
                                variant="outline"
                                size="icon"
                                class="h-10 w-10 shrink-0"
                                aria-label="Generate random code"
                                @click="generateRandomCode"
                            >
                                <Shuffle class="h-4 w-4" />
                            </Button>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="discount-type">Discount Type</Label>
                        <select
                            id="discount-type"
                            v-model="formDiscountType"
                            :class="selectInputClass"
                            aria-label="Discount type"
                        >
                            <option value="percentage">Percentage</option>
                            <option value="fixed">Fixed</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <Label for="discount-value">Discount Value</Label>
                        <div class="relative">
                            <span
                                v-if="formDiscountType === 'percentage'"
                                class="absolute top-1/2 right-3 -translate-y-1/2 text-sm text-gray-500 dark:text-gray-400"
                            >
                                %
                            </span>
                            <span
                                v-else
                                class="absolute top-1/2 left-3 text-sm text-gray-500 dark:text-gray-400"
                            >
                                $
                            </span>
                            <Input
                                id="discount-value"
                                v-model="formDiscountValue"
                                type="number"
                                min="0"
                                :step="
                                    formDiscountType === 'percentage' ? 1 : 0.01
                                "
                                :placeholder="
                                    formDiscountType === 'percentage'
                                        ? 'e.g. 20'
                                        : 'e.g. 10.00'
                                "
                                :class="[
                                    'w-full',
                                    formDiscountType === 'fixed' && 'pl-7',
                                    formDiscountType === 'percentage' && 'pr-8',
                                ]"
                            />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="min-purchase"
                            >Minimum Purchase Amount</Label
                        >
                        <div class="relative">
                            <span
                                class="absolute top-1/2 left-3 text-sm text-gray-500 dark:text-gray-400"
                            >
                                $
                            </span>
                            <Input
                                id="min-purchase"
                                v-model="formMinPurchase"
                                type="number"
                                min="0"
                                step="0.01"
                                placeholder="0.00"
                                class="w-full pl-7"
                            />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="usage-limit"
                            >Usage Limit (Total uses)</Label
                        >
                        <Input
                            id="usage-limit"
                            v-model="formUsageLimit"
                            type="number"
                            min="1"
                            placeholder="e.g. 100"
                            class="w-full"
                        />
                    </div>

                    <div class="space-y-2">
                        <Label for="start-date">Start Date</Label>
                        <input
                            id="start-date"
                            v-model="formStartDate"
                            type="date"
                            :class="dateInputClass"
                            aria-label="Start date"
                        />
                    </div>

                    <div class="space-y-2">
                        <Label for="end-date">End Date</Label>
                        <input
                            id="end-date"
                            v-model="formEndDate"
                            type="date"
                            :class="dateInputClass"
                            aria-label="End date"
                        />
                    </div>

                    <SheetFooter class="gap-2 sm:gap-0">
                        <Button
                            type="button"
                            variant="outline"
                            @click="closeCreatePanel"
                        >
                            Cancel
                        </Button>
                        <Button type="submit"> Create Coupon </Button>
                    </SheetFooter>
                </form>
            </SheetContent>
        </Sheet>

        <!-- Edit Coupon — Slide-over Panel -->
        <Sheet v-model:open="editPanelOpen">
            <SheetContent
                side="right"
                class="sm:max-w-md"
            >
                <SheetHeader>
                    <SheetTitle>Edit Coupon</SheetTitle>
                    <SheetDescription class="sr-only">
                        Form to update the selected coupon
                    </SheetDescription>
                </SheetHeader>
                <form
                    class="flex flex-col gap-6 overflow-y-auto p-6 pr-2"
                    @submit.prevent="handleSubmitEdit"
                >
                    <div class="space-y-2">
                        <Label for="edit-coupon-code">Coupon Code</Label>
                        <Input
                            id="edit-coupon-code"
                            v-model="editFormCode"
                            type="text"
                            placeholder="e.g. SAVE20"
                            class="w-full font-mono uppercase"
                        />
                    </div>

                    <div class="space-y-2">
                        <Label for="edit-discount-type">Discount Type</Label>
                        <select
                            id="edit-discount-type"
                            v-model="editFormDiscountType"
                            :class="selectInputClass"
                            aria-label="Discount type"
                        >
                            <option value="percentage">Percentage</option>
                            <option value="fixed">Fixed</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <Label for="edit-discount-value">Discount Value</Label>
                        <div class="relative">
                            <span
                                v-if="editFormDiscountType === 'percentage'"
                                class="absolute top-1/2 right-3 -translate-y-1/2 text-sm text-gray-500 dark:text-gray-400"
                            >
                                %
                            </span>
                            <span
                                v-else
                                class="absolute top-1/2 left-3 text-sm text-gray-500 dark:text-gray-400"
                            >
                                $
                            </span>
                            <Input
                                id="edit-discount-value"
                                v-model="editFormDiscountValue"
                                type="number"
                                min="0"
                                :step="
                                    editFormDiscountType === 'percentage'
                                        ? 1
                                        : 0.01
                                "
                                :placeholder="
                                    editFormDiscountType === 'percentage'
                                        ? 'e.g. 20'
                                        : 'e.g. 10.00'
                                "
                                :class="[
                                    'w-full',
                                    editFormDiscountType === 'fixed' && 'pl-7',
                                    editFormDiscountType === 'percentage' &&
                                        'pr-8',
                                ]"
                            />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="edit-min-purchase"
                            >Minimum Purchase Amount</Label
                        >
                        <div class="relative">
                            <span
                                class="absolute top-1/2 left-3 text-sm text-gray-500 dark:text-gray-400"
                            >
                                $
                            </span>
                            <Input
                                id="edit-min-purchase"
                                v-model="editFormMinPurchase"
                                type="number"
                                min="0"
                                step="0.01"
                                placeholder="0.00"
                                class="w-full pl-7"
                            />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="edit-usage-limit"
                            >Usage Limit (Total uses)</Label
                        >
                        <Input
                            id="edit-usage-limit"
                            v-model="editFormUsageLimit"
                            type="number"
                            min="1"
                            placeholder="e.g. 100"
                            class="w-full"
                        />
                    </div>

                    <div class="space-y-2">
                        <Label for="edit-start-date">Start Date</Label>
                        <input
                            id="edit-start-date"
                            v-model="editFormStartDate"
                            type="date"
                            :class="dateInputClass"
                            aria-label="Start date"
                        />
                    </div>

                    <div class="space-y-2">
                        <Label for="edit-end-date">End Date</Label>
                        <input
                            id="edit-end-date"
                            v-model="editFormEndDate"
                            type="date"
                            :class="dateInputClass"
                            aria-label="End date"
                        />
                    </div>

                    <SheetFooter class="gap-2 sm:gap-0">
                        <Button
                            type="button"
                            variant="outline"
                            @click="closeEditPanel"
                        >
                            Cancel
                        </Button>
                        <Button type="submit"> Save Changes </Button>
                    </SheetFooter>
                </form>
            </SheetContent>
        </Sheet>
    </div>
</template>
