<script setup>
import DeleteDialog from '@/components/main/DeleteDialog.vue';
import Pagination from '@/components/main/Pagination.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { useFilter } from '@/composables/useFilter';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Activity,
    MoreHorizontal,
    Pencil,
    Plus,
    Search,
    Trash2,
    Trophy,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

const props = defineProps({
    products: Object,
    categories: Array,
    brands: Array,
    filter: Object,
});

const adminProductsUrl = route('admin.products');
const { filterBy, search, sortBy, brand } = useFilter();

const sortOptions = [
    { value: 'default', label: 'Latest' },
    { value: 'oldest', label: 'Oldest' },
    { value: 'price_asc', label: 'Price: Low to High' },
    { value: 'price_desc', label: 'Price: High to Low' },
];

function stockStatus(product) {
    const stock = product.stock ?? 0;
    if (stock <= 0) return { label: 'Out of stock', variant: 'destructive' };
    if (stock <= 5) return { label: 'Low stock', variant: 'secondary' };
    return { label: 'In stock', variant: 'default' };
}

function displayPrice(product) {
    const p = product.discount_price ?? product.price;
    return p != null ? `$${Number(p).toFixed(2)}` : '—';
}

function handleSearch() {
    filterBy(adminProductsUrl, 'search', search.value);
}

function handleEdit(product) {
    router.get(route('admin.products.edit', product.slug));
}

const deleteDialogOpen = ref(false);
const productToDelete = ref(null);

function openDeleteDialog(product) {
    productToDelete.value = product;
    deleteDialogOpen.value = true;
}
const closeDeleteDialog = () => {
    productToDelete.value = null;
    deleteDialogOpen.value = false;
};
const productList = computed(() => props.products?.data ?? []);

// Mock activity log entries (UI only — no backend)
const activityLogEntries = [
    {
        id: 1,
        type: 'created',
        icon: Plus,
        message: 'Product "Wireless Earbuds Pro" was added',
        user: 'Admin',
        time: '2 min ago',
        dotClass: 'bg-emerald-500',
    },
    {
        id: 2,
        type: 'updated',
        icon: Pencil,
        message: 'Product "Classic Leather Wallet" was updated',
        user: 'Staff',
        time: '15 min ago',
        dotClass: 'bg-amber-500',
    },
    {
        id: 3,
        type: 'deleted',
        icon: Trash2,
        message: 'Product "Old Stock Item" was removed',
        user: 'Admin',
        time: '1 hour ago',
        dotClass: 'bg-red-500',
    },
    {
        id: 4,
        type: 'updated',
        icon: Pencil,
        message: 'Stock level updated for "Running Shoes"',
        user: 'Staff',
        time: '2 hours ago',
        dotClass: 'bg-amber-500',
    },
    {
        id: 5,
        type: 'created',
        icon: Plus,
        message: 'Product "USB-C Hub 7-in-1" was added',
        user: 'Admin',
        time: 'Yesterday',
        dotClass: 'bg-emerald-500',
    },
];

// Mock best sellers (UI only — no backend)
const bestSellers = [
    {
        rank: 1,
        name: 'Wireless Earbuds Pro',
        sales: 152,
        image: 'https://placehold.co/56x56/1f2937/9ca3af?text=1',
        badgeClass: 'bg-amber-400 text-black',
        label: '1st',
    },
    {
        rank: 2,
        name: 'Classic Leather Wallet',
        sales: 128,
        image: 'https://placehold.co/56x56/1f2937/9ca3af?text=2',
        badgeClass: 'bg-slate-300 text-black',
        label: '2nd',
    },
    {
        rank: 3,
        name: 'USB-C Hub 7-in-1',
        sales: 94,
        image: 'https://placehold.co/56x56/1f2937/9ca3af?text=3',
        badgeClass: 'bg-orange-400 text-black',
        label: '3rd',
    },
];
defineOptions({
    layout: AdminLayout,
});
</script>

<template>
    <Head title="Products - Admin" />
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between gap-4">
            <h1
                class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white"
            >
                Products
            </h1>
            <Button
                as-child
                class="w-fit shrink-0"
            >
                <Link :href="route('admin.products.create')">
                    <Plus class="mr-2 h-4 w-4" />
                    Add Product
                </Link>
            </Button>
        </div>

        <!-- Activity Log + Best Sellers: responsive side-by-side -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Best Sellers (Monthly) card -->
            <div
                class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900"
            >
                <div
                    class="flex items-center justify-between border-b border-gray-200 px-4 py-3 dark:border-gray-700"
                >
                    <div class="flex items-center gap-2">
                        <Trophy
                            class="h-5 w-5 text-amber-400"
                            aria-hidden="true"
                        />
                        <h2
                            class="text-base font-semibold text-gray-900 dark:text-white"
                        >
                            Best Sellers
                        </h2>
                    </div>
                    <span
                        class="rounded-md bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300"
                    >
                        This Month
                    </span>
                </div>
                <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                    <li
                        v-for="item in bestSellers"
                        :key="item.rank"
                        class="flex items-center gap-4 px-4 py-3 transition-colors duration-200 hover:bg-gray-50 dark:hover:bg-gray-800/50"
                    >
                        <div
                            class="relative h-14 w-14 shrink-0 overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-800"
                        >
                            <img
                                :src="item.image"
                                :alt="item.name"
                                class="h-full w-full object-cover"
                            />
                            <span
                                :class="[
                                    'absolute top-0.5 right-0.5 flex h-5 min-w-5 items-center justify-center rounded px-1 text-[10px] font-bold',
                                    item.badgeClass,
                                ]"
                            >
                                {{ item.label }}
                            </span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p
                                class="truncate text-sm font-medium text-gray-900 dark:text-white"
                            >
                                {{ item.name }}
                            </p>
                            <p
                                class="mt-0.5 text-xs text-gray-500 dark:text-gray-400"
                            >
                                {{ item.sales }} Sales
                            </p>
                        </div>
                    </li>
                </ul>
            </div>
            <!-- Activity Log card -->
            <div
                class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900"
            >
                <div
                    class="flex items-center gap-2 border-b border-gray-200 px-4 py-3 dark:border-gray-700"
                >
                    <Activity
                        class="h-5 w-5 text-gray-500 dark:text-gray-400"
                        aria-hidden="true"
                    />
                    <h2
                        class="text-base font-semibold text-gray-900 dark:text-white"
                    >
                        Activity Log
                    </h2>
                </div>
                <div class="max-h-[280px] overflow-y-auto">
                    <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                        <li
                            v-for="(entry, index) in activityLogEntries"
                            :key="entry.id"
                            class="flex gap-4 px-4 py-3 transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/50"
                        >
                            <div
                                class="relative flex shrink-0 items-center justify-center"
                            >
                                <span
                                    :class="[
                                        'flex h-8 w-8 items-center justify-center rounded-full',
                                        entry.dotClass,
                                        'text-white',
                                    ]"
                                >
                                    <component
                                        :is="entry.icon"
                                        class="h-4 w-4"
                                        aria-hidden="true"
                                    />
                                </span>
                                <template
                                    v-if="index < activityLogEntries.length - 1"
                                >
                                    <span
                                        class="absolute top-10 left-1/2 h-[calc(100%+0.5rem)] w-px -translate-x-1/2 bg-gray-200 dark:bg-gray-700"
                                        aria-hidden="true"
                                    />
                                </template>
                            </div>
                            <div class="min-w-0 flex-1 pt-0.5">
                                <p
                                    class="text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    {{ entry.message }}
                                </p>
                                <p
                                    class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-xs text-gray-500 dark:text-gray-400"
                                >
                                    <span>{{ entry.user }}</span>
                                    <span
                                        class="text-gray-300 dark:text-gray-600"
                                        aria-hidden="true"
                                    >
                                        ·
                                    </span>
                                    <span>{{ entry.time }}</span>
                                </p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Search & filters -->
        <div
            class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900"
        >
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:gap-4"
            >
                <div class="relative flex-1">
                    <Search
                        class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400"
                    />
                    <Input
                        v-model="search"
                        @input="handleSearch"
                        type="search"
                        placeholder="Search products..."
                        class="h-10 w-full rounded-md border border-input bg-background pr-4 pl-10 text-sm focus-visible:border-transparent focus-visible:ring-1 focus-visible:ring-primary focus-visible:outline-none"
                        aria-label="Search products"
                    />
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <label
                        for="admin-category"
                        class="sr-only"
                    >
                        Category
                    </label>
                    <select
                        id="admin-category"
                        :value="filter?.category ?? ''"
                        class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
                        @change="
                            filterBy(
                                adminProductsUrl,
                                'category',
                                $event.target.value,
                            )
                        "
                    >
                        <option value="">All categories</option>
                        <option
                            v-for="cat in categories"
                            :key="cat.id"
                            :value="cat.slug"
                        >
                            {{ cat.name }}
                        </option>
                    </select>
                    <label
                        for="admin-sort"
                        class="sr-only"
                    >
                        Sort
                    </label>
                    <select
                        id="admin-sort"
                        v-model="sortBy"
                        class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
                        @change="filterBy(adminProductsUrl, 'sortBy', sortBy)"
                    >
                        <option
                            v-for="opt in sortOptions"
                            :key="opt.value"
                            :value="opt.value"
                        >
                            {{ opt.label }}
                        </option>
                    </select>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400"
                >{{ products.total }} products</span
            >
        </div>
        <!-- Table card -->
        <div
            class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900"
        >
            <!-- Mobile: card list -->
            <div class="block sm:hidden">
                <div
                    v-for="product in productList"
                    :key="product.id"
                    class="flex items-center gap-4 border-b border-gray-100 px-4 py-3 last:border-b-0 dark:border-gray-800"
                >
                    <div
                        class="h-14 w-14 shrink-0 overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-800"
                    >
                        <img
                            v-if="product.thumbnail"
                            :src="product.thumbnail"
                            :alt="product.name"
                            class="h-full w-full object-cover"
                        />
                        <div
                            v-else
                            class="flex h-full w-full items-center justify-center text-xs text-gray-400"
                        >
                            —
                        </div>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p
                            class="truncate font-medium text-gray-900 dark:text-white"
                        >
                            {{ product.name }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ product.slug || product.id }} ·
                            {{ displayPrice(product) }}
                        </p>
                        <Badge
                            :variant="stockStatus(product).variant"
                            class="mt-1 rounded-md"
                        >
                            {{ stockStatus(product).label }}
                        </Badge>
                    </div>
                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button
                                variant="ghost"
                                size="icon"
                                class="h-8 w-8 shrink-0"
                                aria-label="Open menu"
                            >
                                <MoreHorizontal class="h-4 w-4" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent
                            align="end"
                            class="w-44"
                        >
                            <DropdownMenuItem @click="handleEdit(product)">
                                <Pencil class="mr-2 h-4 w-4" />
                                Edit
                            </DropdownMenuItem>
                            <DropdownMenuItem
                                variant="destructive"
                                @click="openDeleteDialog(product)"
                            >
                                <Trash2 class="mr-2 h-4 w-4" />
                                Delete
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </div>

            <!-- Desktop: table -->
            <div class="hidden overflow-x-auto sm:block">
                <table
                    class="w-full min-w-[640px] border-collapse text-left text-sm"
                    role="table"
                >
                    <thead>
                        <tr
                            class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800/50"
                        >
                            <th
                                class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300"
                            >
                                Image
                            </th>
                            <th
                                class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300"
                            >
                                Name
                            </th>
                            <th
                                class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300"
                            >
                                SKU
                            </th>
                            <th
                                class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300"
                            >
                                Price
                            </th>
                            <th
                                class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300"
                            >
                                Stock
                            </th>
                            <th
                                class="w-12 px-4 py-3 text-right font-medium text-gray-700 dark:text-gray-300"
                                aria-label="Actions"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="product in productList"
                            :key="product.id"
                            class="border-b border-gray-100 transition-colors hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-800/50"
                        >
                            <td class="px-4 py-3">
                                <div
                                    class="h-12 w-12 overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-800"
                                >
                                    <img
                                        v-if="product.thumbnail"
                                        :src="product.thumbnail"
                                        :alt="product.name"
                                        class="h-full w-full object-cover"
                                    />
                                    <div
                                        v-else
                                        class="flex h-full w-full items-center justify-center text-xs text-gray-400"
                                    >
                                        No image
                                    </div>
                                </div>
                            </td>
                            <td
                                class="px-4 py-3 font-medium text-gray-900 dark:text-white"
                            >
                                {{ product.name }}
                            </td>
                            <td
                                class="px-4 py-3 text-gray-600 dark:text-gray-400"
                            >
                                {{ product.slug || product.id }}
                            </td>
                            <td
                                class="px-4 py-3 text-gray-700 dark:text-gray-300"
                            >
                                {{ displayPrice(product) }}
                            </td>
                            <td class="px-4 py-3">
                                <Badge
                                    :variant="stockStatus(product).variant"
                                    class="rounded-md"
                                >
                                    {{ stockStatus(product).label }}
                                </Badge>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            class="h-8 w-8 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                                            aria-label="Open menu"
                                        >
                                            <MoreHorizontal class="h-4 w-4" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent
                                        align="end"
                                        class="w-44"
                                    >
                                        <DropdownMenuItem
                                            @click="handleEdit(product)"
                                        >
                                            <Pencil class="mr-2 h-4 w-4" />
                                            Edit
                                        </DropdownMenuItem>
                                        <DropdownMenuItem
                                            variant="destructive"
                                            @click="openDeleteDialog(product)"
                                        >
                                            <Trash2 class="mr-2 h-4 w-4" />
                                            Delete
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Empty state -->
            <div
                v-if="productList.length === 0"
                class="px-4 py-12 text-center text-gray-500 dark:text-gray-400"
            >
                No products found. Try adjusting search or filters.
            </div>

            <!-- Pagination -->
            <div
                v-if="products && productList.length > 0"
                class="border-t border-gray-200 px-4 py-3 dark:border-gray-700"
            >
                <Pagination :meta="products" />
            </div>
        </div>

        <!-- Delete product confirmation dialog -->
        <DeleteDialog
            v-if="deleteDialogOpen"
            :deleteItem="productToDelete"
            @closeDeleteDialog="closeDeleteDialog"
        />
    </div>
</template>
