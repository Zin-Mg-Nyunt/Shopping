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
import { MoreHorizontal, Pencil, Plus, Search, Trash2 } from 'lucide-vue-next';
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
    router.visit(route('admin.products'));
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
defineOptions({
    layout: AdminLayout,
});
</script>

<template>
    <Head title="Products - Admin" />
    <div class="space-y-6">
        <!-- Header -->
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <h1
                class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white"
            >
                Products
            </h1>
            <Button
                as-child
                class="w-fit shrink-0"
            >
                <Link :href="route('admin.products')">
                    <Plus class="mr-2 h-4 w-4" />
                    Add Product
                </Link>
            </Button>
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
