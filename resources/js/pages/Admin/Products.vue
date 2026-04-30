<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    ChevronLeft,
    ChevronRight,
    Eye,
    Filter,
    Package,
    Pencil,
    Plus,
    Search,
    Trash2,
} from 'lucide-vue-next';
import { computed, nextTick, ref, watch } from 'vue';
import ProductFormDialog from '@/components/admin/ProductFormDialog.vue';
import ProductPreviewDialog from '@/components/admin/ProductPreviewDialog.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import useFilterBy from '@/composables/useFilterBy';
import AdminDashboardLayout from '@/layouts/AdminDashboardLayout.vue';

defineOptions({ layout: AdminDashboardLayout });

const props = defineProps({
    products: { type: Object, required: true },
});

const page = usePage();
const rows = computed(() => props.products?.data);
const search = ref('');
const stock = ref('');
const price = ref('');
const { filterBy } = useFilterBy();

const selectedIds = ref(new Set());
const selectAllRef = ref(null);

const deleteOpen = ref(false);
const deleteTargetIds = ref([]);

const viewOpen = ref(false);
const viewProduct = ref(null);

const editOpen = ref(false);
const editMode = ref('add');
const editProduct = ref(null);
const saveLoading = ref(false);

const currency = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
});
const paginationLinks = computed(() => props.products?.links ?? []);
const pageIds = computed(() => rows.value.map((row) => row.id));

const allPageSelected = computed(
    () =>
        pageIds.value.length > 0 &&
        pageIds.value.every((id) => selectedIds.value.has(id)),
);

const somePageSelected = computed(() => {
    if (pageIds.value.length === 0) {
        return false;
    }

    const selectedCount = pageIds.value.filter((id) =>
        selectedIds.value.has(id),
    ).length;

    return selectedCount > 0 && selectedCount < pageIds.value.length;
});

watch(
    [allPageSelected, somePageSelected, rows],
    async () => {
        await nextTick();
        const checkbox = selectAllRef.value;

        if (checkbox && 'indeterminate' in checkbox) {
            checkbox.indeterminate =
                somePageSelected.value && !allPageSelected.value;
        }
    },
    { flush: 'post' },
);

function formatPrice(amount) {
    return currency.format(Number(amount));
}

function stockPercent(row) {
    return Math.min(
        100,
        Math.round((row.stock / Math.max(1, row.stock)) * 100),
    );
}

function stockBarClass(row) {
    if (row.stock === 0) {
        return 'bg-destructive';
    }

    if (row.stock < 10) {
        return 'bg-[#FF6600]';
    }

    return 'bg-emerald-500';
}

function toggleSelectAllPage(event) {
    const checked = event.target.checked;
    const next = new Set(selectedIds.value);

    for (const id of pageIds.value) {
        if (checked) {
            next.add(id);
        } else {
            next.delete(id);
        }
    }

    selectedIds.value = next;
}

function toggleRowSelected(id, checked) {
    const next = new Set(selectedIds.value);

    if (checked) {
        next.add(id);
    } else {
        next.delete(id);
    }

    selectedIds.value = next;
}

function openDeleteForRow(id) {
    deleteTargetIds.value = [id];
    deleteOpen.value = true;
}

function openBulkDelete() {
    deleteTargetIds.value = [...selectedIds.value];
    deleteOpen.value = true;
}

function confirmDelete() {
    const ids = new Set(deleteTargetIds.value);
    rows.value = rows.value.filter((row) => !ids.has(row.id));
    selectedIds.value = new Set(
        [...selectedIds.value].filter((id) => !ids.has(id)),
    );
    deleteTargetIds.value = [];
    deleteOpen.value = false;
}

function openView(row) {
    viewProduct.value = row;
    viewOpen.value = true;
}

function openAdd() {
    editMode.value = 'add';
    editProduct.value = null;
    editOpen.value = true;
}

function openEdit(row) {
    editMode.value = 'edit';
    editProduct.value = row;
    editOpen.value = true;
}

async function saveEdit(payload) {
    saveLoading.value = true;

    try {
        await new Promise((resolve) => setTimeout(resolve, 900));

        if (editMode.value === 'edit' && payload.id != null) {
            const index = rows.value.findIndex((row) => row.id === payload.id);

            if (index !== -1) {
                rows.value[index] = {
                    ...rows.value[index],
                    title: payload.title.trim(),
                    slug: payload.slug || rows.value[index].slug,
                    description: payload.description.trim(),
                    category_name: payload.category,
                    price: payload.price,
                    discount_price: payload.discount_price,
                    stock: payload.stock,
                };
            }
        } else {
            const maxId = rows.value.reduce(
                (max, row) => Math.max(max, row.id),
                0,
            );
            rows.value.push({
                id: maxId + 1,
                title: payload.title.trim(),
                slug: `new-product-${maxId + 1}`,
                description: payload.description.trim(),
                category_name: payload.category,
                price: payload.price,
                discount_price: payload.discount_price,
                stock: payload.stock,
                thumbnail: null,
            });
        }

        editOpen.value = false;
    } finally {
        saveLoading.value = false;
    }
}
</script>

<template>
    <Head title="Admin · Product inventory" />

    <div
        class="-mx-4 -mt-4 min-h-[calc(100vh-4rem)] w-[calc(100%+2rem)] max-w-none px-4 pt-4 pb-8 md:-mx-6 md:mt-0 md:w-[calc(100%+3rem)] md:px-6 md:pt-6 lg:-mx-8 lg:w-[calc(100%+4rem)] lg:px-8 lg:pt-8"
    >
        <!-- Header -->
        <div
            class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between"
        >
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-foreground">
                    Product inventory
                </h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Manage catalog, stock levels, and publishing status.
                </p>
            </div>
            <Button
                class="h-11 shrink-0 bg-[#FF6600] font-semibold text-white shadow-sm hover:bg-[#FF6600]/90"
                @click="openAdd"
            >
                <Plus class="mr-2 h-5 w-5" stroke-width="2" />
                Add new product
            </Button>
        </div>

        <!-- Toolbar -->
        <div
            class="mb-4 flex flex-col gap-3 rounded-2xl border border-border bg-card p-4 shadow-sm sm:flex-row sm:flex-wrap sm:items-center"
        >
            <div class="relative min-w-[200px] flex-1">
                <Search
                    class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                    stroke-width="2"
                />
                <Input
                    v-model="search"
                    type="search"
                    placeholder="Search products by title or slug..."
                    class="h-10 border-border bg-background pl-9 focus-visible:border-[#FF6600] focus-visible:ring-[#FF6600]/25"
                    @keyup.enter="filterBy('search', search, 'admin.products')"
                    @blur="
                        search === ''
                            ? filterBy('search', search, 'admin.products')
                            : null
                    "
                />
            </div>
            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <div
                    class="flex items-center gap-2 text-sm text-muted-foreground"
                >
                    <Filter class="h-4 w-4 text-[#FF6600]" stroke-width="2" />
                    <span class="hidden sm:inline">Filters</span>
                </div>
                <select
                    :value="route().params.category ?? ''"
                    class="h-10 min-w-34 rounded-lg border border-border bg-background px-3 text-sm text-foreground focus:border-[#FF6600] focus:ring-2 focus:ring-[#FF6600]/20 focus:outline-none"
                    @change="
                        filterBy(
                            'category',
                            $event.target.value,
                            'admin.products',
                        )
                    "
                >
                    <option value="">All categories</option>
                    <option
                        v-for="category in page.props.categories"
                        :key="category.id"
                        :value="category.slug"
                    >
                        {{ category.name }}
                    </option>
                </select>
                <select
                    v-model="stock"
                    class="h-10 min-w-38 rounded-lg border border-border bg-background px-3 text-sm text-foreground focus:border-[#FF6600] focus:ring-2 focus:ring-[#FF6600]/20 focus:outline-none"
                    @change="filterBy('stock', stock, 'admin.products')"
                >
                    <option value="">In stock</option>
                    <option value="low">Low (&lt;10)</option>
                    <option value="out">Out of stock</option>
                </select>
                <select
                    v-model="price"
                    class="h-10 min-w-36 rounded-lg border border-border bg-background px-3 text-sm text-foreground focus:border-[#FF6600] focus:ring-2 focus:ring-[#FF6600]/20 focus:outline-none"
                    @change="filterBy('price', price, 'admin.products')"
                >
                    <option value="">Price: Any</option>
                    <option value="under100">Under $100</option>
                    <option value="100to500">$100 – $500</option>
                    <option value="over500">Over $500</option>
                </select>
            </div>
        </div>

        <!-- Bulk bar -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 -translate-y-1"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="selectedIds.size > 0"
                class="mb-4 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-[#FF6600]/30 bg-[#FF6600]/8 px-4 py-3 dark:bg-[#FF6600]/10"
            >
                <p class="text-sm font-medium text-foreground">
                    <span class="font-semibold text-[#FF6600]">{{
                        selectedIds.size
                    }}</span>
                    product(s) selected
                </p>
                <Button
                    variant="outline"
                    class="border-destructive/40 text-destructive hover:bg-destructive/10"
                    @click="openBulkDelete"
                >
                    <Trash2 class="mr-2 h-4 w-4" />
                    Delete selected
                </Button>
            </div>
        </Transition>

        <!-- Table card -->
        <div
            class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm"
        >
            <div class="overflow-x-auto">
                <table class="w-full min-w-[920px] text-left text-sm">
                    <thead>
                        <tr
                            class="border-b border-border bg-muted/40 text-muted-foreground"
                        >
                            <th class="w-12 px-4 py-3">
                                <input
                                    ref="selectAllRef"
                                    type="checkbox"
                                    class="size-4 rounded border-input accent-[#FF6600]"
                                    :checked="allPageSelected"
                                    aria-label="Select all on this page"
                                    @change="toggleSelectAllPage"
                                />
                            </th>
                            <th class="px-4 py-3 font-semibold">Image</th>
                            <th class="px-4 py-3 font-semibold">Product</th>
                            <th class="px-4 py-3 font-semibold">Slug</th>
                            <th class="px-4 py-3 font-semibold">Category</th>
                            <th class="px-4 py-3 font-semibold">Price</th>
                            <th class="px-4 py-3 font-semibold">
                                Discount Price
                            </th>
                            <th class="min-w-[140px] px-4 py-3 font-semibold">
                                Stock
                            </th>
                            <th class="w-36 px-4 py-3 text-right font-semibold">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <tr
                            v-for="row in rows"
                            :key="row.id"
                            class="transition hover:bg-muted/30"
                        >
                            <td class="px-4 py-3 align-middle">
                                <Checkbox
                                    :model-value="selectedIds.has(row.id)"
                                    class="size-4 border-input data-[state=checked]:border-[#FF6600] data-[state=checked]:bg-[#FF6600] data-[state=checked]:text-white"
                                    @update:model-value="
                                        (v) => toggleRowSelected(row.id, v)
                                    "
                                />
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <div
                                    class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-lg bg-muted"
                                >
                                    <img
                                        v-if="row.thumbnail"
                                        :src="row.thumbnail"
                                        :alt="row.title"
                                        class="h-full w-full object-cover"
                                    />
                                    <Package
                                        v-else
                                        class="h-6 w-6 text-muted-foreground/50"
                                        stroke-width="1.5"
                                    />
                                </div>
                            </td>
                            <td
                                class="max-w-[200px] px-4 py-3 align-middle font-medium text-foreground"
                            >
                                <span class="line-clamp-2">{{
                                    row.title
                                }}</span>
                            </td>
                            <td
                                class="px-4 py-3 align-middle font-mono text-xs text-muted-foreground"
                            >
                                {{ row.slug }}
                            </td>
                            <td class="max-w-[200px] px-2 py-3">
                                <div class="flex flex-wrap items-start gap-2">
                                    <div
                                        v-for="category in row.categories"
                                        :key="category.id"
                                    >
                                        <span
                                            class="rounded-full border border-primary/50 px-2 py-1 text-xs transition-colors duration-200"
                                            :class="
                                                category.slug ===
                                                route().params.category
                                                    ? 'bg-primary/80 text-white'
                                                    : 'text-primary'
                                            "
                                            >{{ category.name }}</span
                                        >
                                    </div>
                                </div>
                            </td>
                            <td
                                class="px-4 py-3 align-middle font-medium text-foreground tabular-nums"
                            >
                                {{ formatPrice(row.price) }}
                            </td>
                            <td
                                class="px-4 py-3 align-middle font-medium text-primary tabular-nums"
                            >
                                {{
                                    row.discount_price
                                        ? formatPrice(row.discount_price)
                                        : '-'
                                }}
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="min-w-8 text-right font-semibold tabular-nums"
                                        :class="
                                            row.stock === 0
                                                ? 'text-destructive'
                                                : row.stock < 10
                                                  ? 'text-[#FF6600]'
                                                  : 'text-foreground'
                                        "
                                    >
                                        {{ row.stock }}
                                    </span>
                                    <div
                                        class="h-2 min-w-[72px] flex-1 overflow-hidden rounded-full bg-muted"
                                    >
                                        <div
                                            class="h-full rounded-full transition-all"
                                            :class="stockBarClass(row)"
                                            :style="{
                                                width: `${stockPercent(row)}%`,
                                            }"
                                        />
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <div
                                    class="flex items-center justify-end gap-1"
                                >
                                    <button
                                        type="button"
                                        class="flex h-9 w-9 items-center justify-center rounded-lg text-muted-foreground transition hover:bg-muted hover:text-[#FF6600]"
                                        title="View"
                                        @click="openView(row)"
                                    >
                                        <Eye class="h-4 w-4" stroke-width="2" />
                                    </button>
                                    <button
                                        type="button"
                                        class="flex h-9 w-9 items-center justify-center rounded-lg text-muted-foreground transition hover:bg-muted hover:text-[#FF6600]"
                                        title="Edit"
                                        @click="openEdit(row)"
                                    >
                                        <Pencil
                                            class="h-4 w-4"
                                            stroke-width="2"
                                        />
                                    </button>
                                    <button
                                        type="button"
                                        class="flex h-9 w-9 items-center justify-center rounded-lg text-muted-foreground transition hover:bg-destructive/10 hover:text-destructive"
                                        title="Delete"
                                        @click="openDeleteForRow(row.id)"
                                    >
                                        <Trash2
                                            class="h-4 w-4"
                                            stroke-width="2"
                                        />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                class="flex flex-wrap items-center justify-between gap-3 border-t border-border px-4 py-3"
            >
                <p class="text-xs text-muted-foreground">
                    Showing
                    {{ props.products.from ?? 0 }}–{{
                        props.products.to ?? 0
                    }}
                    of {{ props.products.total ?? 0 }}
                </p>
                <nav class="flex items-center gap-1" aria-label="Pagination">
                    <component
                        :is="link.url ? Link : 'span'"
                        v-for="link in paginationLinks"
                        :key="link.label"
                        :href="link.url || undefined"
                        preserve-scroll
                        preserve-state
                        class="flex h-9 min-w-9 items-center justify-center rounded-lg border border-border px-2 text-sm font-medium transition"
                        :class="
                            link.active
                                ? 'border-[#FF6600] bg-[#FF6600] text-white shadow-sm'
                                : link.url
                                  ? 'text-muted-foreground hover:border-[#FF6600]/50 hover:text-[#FF6600]'
                                  : 'cursor-not-allowed text-muted-foreground opacity-40'
                        "
                    >
                        <ChevronLeft
                            v-if="link.label.includes('Previous')"
                            class="h-4 w-4"
                        />
                        <ChevronRight
                            v-else-if="link.label.includes('Next')"
                            class="h-4 w-4"
                        />
                        <span v-else v-html="link.label" />
                    </component>
                </nav>
            </div>
        </div>

        <!-- Delete confirmation -->
        <Dialog v-model:open="deleteOpen">
            <DialogContent class="border-destructive/20 sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Delete product(s)?</DialogTitle>
                    <DialogDescription>
                        This will remove
                        {{ deleteTargetIds.length }} product(s) from the
                        inventory list. This demo only updates the page — wire
                        to your API for real deletes.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter class="gap-2 sm:gap-0">
                    <Button variant="outline" @click="deleteOpen = false">
                        Cancel
                    </Button>
                    <Button
                        class="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                        @click="confirmDelete"
                    >
                        Confirm delete
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <ProductPreviewDialog v-model:open="viewOpen" :product="viewProduct" />

        <ProductFormDialog
            v-model:open="editOpen"
            :mode="editMode"
            :product="editProduct"
            :saving="saveLoading"
            :category-options="categoryOptions"
            @submit="saveEdit"
        />
    </div>
</template>
