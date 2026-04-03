<script setup>
import { Head } from '@inertiajs/vue3';
import {
    ArrowLeft,
    ChevronLeft,
    ChevronRight,
    Eye,
    Filter,
    Loader2,
    Minus,
    Package,
    Pencil,
    Plus,
    Search,
    Trash2,
    Upload,
} from 'lucide-vue-next';
import { computed, nextTick, onUnmounted, ref, watch } from 'vue';
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
import { Label } from '@/components/ui/label';
import AdminDashboardLayout from '@/layouts/AdminDashboardLayout.vue';

defineOptions({
    layout: AdminDashboardLayout,
});

const props = defineProps({
    products: {
        type: Array,
        required: true,
    },
});

const rows = ref(props.products.map((p) => ({ ...p })));

const searchQuery = ref('');
const filterCategory = ref('all');
const filterStock = ref('all');
const filterPrice = ref('all');

const selectedIds = ref(new Set());
const perPage = 7;
const currentPage = ref(1);

const selectAllRef = ref(null);

const deleteOpen = ref(false);
const deleteTargetIds = ref([]);

const viewOpen = ref(false);
const viewProduct = ref(null);

const editOpen = ref(false);
const editMode = ref('add');
const editForm = ref({
    id: null,
    name: '',
    description: '',
    category: '',
    price: '',
    stock: 0,
    imageFile: null,
});
const editFileInputRef = ref(null);
const imagePreviewUrl = ref(null);
const existingImageUrl = ref(null);
const categoryQuery = ref('');
const categoryDropdownOpen = ref(false);
const dropZoneDragging = ref(false);
const editErrors = ref({});
const saveLoading = ref(false);

const productCategories = computed(() => {
    const s = new Set(rows.value.map((r) => r.category));

    s.add('Uncategorized');
    s.add('Footwear');
    s.add('Bags');
    s.add('Electronics');
    s.add('Apparel');
    s.add('Home');
    s.add('Fitness');

    return [...s].sort((a, b) => a.localeCompare(b));
});

const filteredCategoryOptions = computed(() => {
    const q = categoryQuery.value.trim().toLowerCase();

    if (!q) {
        return productCategories.value;
    }

    return productCategories.value.filter((c) =>
        c.toLowerCase().includes(q),
    );
});

const currency = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
});

function formatPrice(n) {
    return currency.format(Number(n));
}

const categories = computed(() => {
    const s = new Set(rows.value.map((r) => r.category));

    return ['all', ...[...s].sort()];
});

const filteredRows = computed(() => {
    let list = rows.value;

    const q = searchQuery.value.trim().toLowerCase();

    if (q) {
        list = list.filter(
            (r) =>
                r.name.toLowerCase().includes(q) ||
                r.sku.toLowerCase().includes(q) ||
                r.category.toLowerCase().includes(q),
        );
    }

    if (filterCategory.value !== 'all') {
        list = list.filter((r) => r.category === filterCategory.value);
    }

    if (filterStock.value === 'low') {
        list = list.filter((r) => r.stock > 0 && r.stock < 10);
    } else if (filterStock.value === 'out') {
        list = list.filter((r) => r.stock === 0);
    } else if (filterStock.value === 'in_stock') {
        list = list.filter((r) => r.stock > 0);
    }

    if (filterPrice.value === 'under50') {
        list = list.filter((r) => r.price < 50);
    } else if (filterPrice.value === '50to150') {
        list = list.filter((r) => r.price >= 50 && r.price <= 150);
    } else if (filterPrice.value === 'over150') {
        list = list.filter((r) => r.price > 150);
    }

    return list;
});

const totalPages = computed(() =>
    Math.max(1, Math.ceil(filteredRows.value.length / perPage)),
);

const paginatedRows = computed(() => {
    const start = (currentPage.value - 1) * perPage;

    return filteredRows.value.slice(start, start + perPage);
});

const pageIds = computed(() => paginatedRows.value.map((r) => r.id));

const allPageSelected = computed(() => {
    if (pageIds.value.length === 0) {
        return false;
    }

    return pageIds.value.every((id) => selectedIds.value.has(id));
});

const somePageSelected = computed(() => {
    if (pageIds.value.length === 0) {
        return false;
    }

    const n = pageIds.value.filter((id) => selectedIds.value.has(id)).length;

    return n > 0 && n < pageIds.value.length;
});

watch([filteredRows, currentPage], () => {
    if (currentPage.value > totalPages.value) {
        currentPage.value = totalPages.value;
    }
});

watch(
    [allPageSelected, somePageSelected, paginatedRows],
    async () => {
        await nextTick();
        const el = selectAllRef.value;

        if (el && 'indeterminate' in el) {
            el.indeterminate = somePageSelected.value && !allPageSelected.value;
        }
    },
    { flush: 'post' },
);

function stockPercent(row) {
    const max = Math.max(1, row.stock_max || 1);

    return Math.min(100, Math.round((row.stock / max) * 100));
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

function statusLabel(status) {
    if (status === 'published') {
        return 'Published';
    }

    if (status === 'draft') {
        return 'Draft';
    }

    return 'Out of stock';
}

function statusBadgeClass(status) {
    if (status === 'published') {
        return 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-400';
    }

    if (status === 'draft') {
        return 'bg-muted text-muted-foreground';
    }

    return 'bg-destructive/15 text-destructive';
}

function toggleSelectAllPage(e) {
    const on = e.target.checked;
    const next = new Set(selectedIds.value);

    if (on) {
        for (const id of pageIds.value) {
            next.add(id);
        }
    } else {
        for (const id of pageIds.value) {
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
    rows.value = rows.value.filter((r) => !ids.has(r.id));
    selectedIds.value = new Set(
        [...selectedIds.value].filter((id) => !ids.has(id)),
    );
    deleteOpen.value = false;
    deleteTargetIds.value = [];
}

function openView(row) {
    viewProduct.value = row;
    viewOpen.value = true;
}

function revokeBlobPreview() {
    if (imagePreviewUrl.value && imagePreviewUrl.value.startsWith('blob:')) {
        URL.revokeObjectURL(imagePreviewUrl.value);
    }

    imagePreviewUrl.value = null;
}

const previewImageSrc = computed(
    () => imagePreviewUrl.value || existingImageUrl.value || null,
);

function openAdd() {
    editMode.value = 'add';
    editErrors.value = {};
    revokeBlobPreview();
    existingImageUrl.value = null;
    categoryQuery.value = '';
    categoryDropdownOpen.value = false;
    editForm.value = {
        id: null,
        name: '',
        description: '',
        category: '',
        price: '',
        stock: 0,
        imageFile: null,
    };
    editOpen.value = true;
}

function openEdit(row) {
    editMode.value = 'edit';
    editErrors.value = {};
    revokeBlobPreview();
    existingImageUrl.value = row.image_url || null;
    categoryQuery.value = row.category || '';
    categoryDropdownOpen.value = false;
    editForm.value = {
        id: row.id,
        name: row.name,
        description: row.description ?? '',
        category: row.category || '',
        price: String(row.price),
        stock: Number(row.stock) || 0,
        imageFile: null,
    };
    editOpen.value = true;
}

function onEditFileChange(e) {
    const f = e.target.files?.[0];
    revokeBlobPreview();
    editForm.value.imageFile = f || null;

    if (f && f.type.startsWith('image/')) {
        imagePreviewUrl.value = URL.createObjectURL(f);
    }
}

function triggerEditFileInput() {
    editFileInputRef.value?.click();
}

function onDropZoneDragOver(e) {
    e.preventDefault();
    dropZoneDragging.value = true;
}

function onDropZoneDragLeave() {
    dropZoneDragging.value = false;
}

function onDropZoneDrop(e) {
    e.preventDefault();
    dropZoneDragging.value = false;
    const f = e.dataTransfer?.files?.[0];

    if (f && f.type.startsWith('image/')) {
        editForm.value.imageFile = f;
        revokeBlobPreview();
        imagePreviewUrl.value = URL.createObjectURL(f);

        if (editFileInputRef.value) {
            editFileInputRef.value.value = '';
        }
    }
}

function selectCategory(c) {
    editForm.value.category = c;
    categoryQuery.value = c;
    categoryDropdownOpen.value = false;
    editErrors.value.category = '';
}

function onCategoryBlur() {
    setTimeout(() => {
        categoryDropdownOpen.value = false;
    }, 180);
}

function clearProductImage() {
    revokeBlobPreview();
    editForm.value.imageFile = null;
    existingImageUrl.value = null;

    if (editFileInputRef.value) {
        editFileInputRef.value.value = '';
    }
}

function closeEditForm() {
    editOpen.value = false;
}

function validateEditForm() {
    const err = {};
    const name = editForm.value.name.trim();

    if (!name) {
        err.name = 'Product name is required.';
    }

    const category =
        editForm.value.category.trim() || categoryQuery.value.trim();

    if (!category) {
        err.category = 'Please select or enter a category.';
    }

    const price = Number.parseFloat(editForm.value.price);

    if (Number.isNaN(price) || price < 0) {
        err.price = 'Enter a valid price (0 or greater).';
    }

    const stock = Number(editForm.value.stock);

    if (Number.isNaN(stock) || stock < 0 || !Number.isInteger(stock)) {
        err.stock = 'Stock must be a whole number of 0 or more.';
    }

    editErrors.value = err;

    return Object.keys(err).length === 0;
}

async function saveEdit() {
    if (!validateEditForm()) {
        return;
    }

    saveLoading.value = true;

    try {
        await new Promise((r) => setTimeout(r, 900));

        const name = editForm.value.name.trim();
        const price = Number.parseFloat(editForm.value.price);
        const stock = Number(editForm.value.stock);
        const category =
            editForm.value.category.trim() || categoryQuery.value.trim();
        const description = editForm.value.description.trim();

        if (editMode.value === 'edit' && editForm.value.id != null) {
            const i = rows.value.findIndex((r) => r.id === editForm.value.id);

            if (i !== -1) {
                rows.value[i] = {
                    ...rows.value[i],
                    name,
                    description,
                    category,
                    price,
                    stock,
                    stock_max: Math.max(rows.value[i].stock_max || 100, stock),
                };
            }
        } else {
            const maxId = rows.value.reduce((m, r) => Math.max(m, r.id), 0);
            rows.value.push({
                id: maxId + 1,
                name,
                description,
                sku: `NEW-${maxId + 1}`,
                category,
                price,
                stock,
                stock_max: Math.max(100, stock),
                status: 'draft',
                image_url: null,
            });
        }

        revokeBlobPreview();
        editOpen.value = false;
    } finally {
        saveLoading.value = false;
    }
}

function adjustStock(delta) {
    const next = Math.max(0, Number(editForm.value.stock) + delta);
    editForm.value.stock = next;
    editErrors.value.stock = '';
}

watch(editOpen, (open) => {
    if (!open) {
        categoryDropdownOpen.value = false;
        dropZoneDragging.value = false;
    }
});

onUnmounted(() => {
    revokeBlobPreview();
});

const pageNumbers = computed(() => {
    const total = totalPages.value;
    const cur = currentPage.value;
    const windowSize = 3;
    const start = Math.max(1, cur - 1);
    const end = Math.min(total, start + windowSize - 1);
    let s = start;

    if (end - start + 1 < windowSize) {
        s = Math.max(1, end - windowSize + 1);
    }

    const nums = [];

    for (let i = s; i <= end; i++) {
        nums.push(i);
    }

    return nums;
});

function goPage(p) {
    currentPage.value = Math.min(totalPages.value, Math.max(1, p));
}

watch([searchQuery, filterCategory, filterStock, filterPrice], () => {
    currentPage.value = 1;
});
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
                    v-model="searchQuery"
                    type="search"
                    placeholder="Search name, SKU, category…"
                    class="h-10 border-border bg-background pl-9 focus-visible:border-[#FF6600] focus-visible:ring-[#FF6600]/25"
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
                    v-model="filterCategory"
                    class="h-10 min-w-34 rounded-lg border border-border bg-background px-3 text-sm text-foreground focus:border-[#FF6600] focus:ring-2 focus:ring-[#FF6600]/20 focus:outline-none"
                >
                    <option value="all">All categories</option>
                    <option
                        v-for="c in categories.filter((x) => x !== 'all')"
                        :key="c"
                        :value="c"
                    >
                        {{ c }}
                    </option>
                </select>
                <select
                    v-model="filterStock"
                    class="h-10 min-w-38 rounded-lg border border-border bg-background px-3 text-sm text-foreground focus:border-[#FF6600] focus:ring-2 focus:ring-[#FF6600]/20 focus:outline-none"
                >
                    <option value="all">Stock: Any</option>
                    <option value="in_stock">In stock</option>
                    <option value="low">Low (&lt;10)</option>
                    <option value="out">Out of stock</option>
                </select>
                <select
                    v-model="filterPrice"
                    class="h-10 min-w-36 rounded-lg border border-border bg-background px-3 text-sm text-foreground focus:border-[#FF6600] focus:ring-2 focus:ring-[#FF6600]/20 focus:outline-none"
                >
                    <option value="all">Price: Any</option>
                    <option value="under50">Under $50</option>
                    <option value="50to150">$50 – $150</option>
                    <option value="over150">Over $150</option>
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
                            <th class="px-4 py-3 font-semibold">SKU</th>
                            <th class="px-4 py-3 font-semibold">Category</th>
                            <th class="px-4 py-3 font-semibold">Price</th>
                            <th class="min-w-[140px] px-4 py-3 font-semibold">
                                Stock
                            </th>
                            <th class="px-4 py-3 font-semibold">Status</th>
                            <th class="w-36 px-4 py-3 text-right font-semibold">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <tr
                            v-for="row in paginatedRows"
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
                                        v-if="row.image_url"
                                        :src="row.image_url"
                                        :alt="row.name"
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
                                <span class="line-clamp-2">{{ row.name }}</span>
                            </td>
                            <td
                                class="px-4 py-3 align-middle font-mono text-xs text-muted-foreground"
                            >
                                {{ row.sku }}
                            </td>
                            <td class="px-4 py-3 align-middle text-foreground">
                                {{ row.category }}
                            </td>
                            <td
                                class="px-4 py-3 align-middle font-medium text-[#FF6600] tabular-nums"
                            >
                                {{ formatPrice(row.price) }}
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
                                <span
                                    class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                    :class="statusBadgeClass(row.status)"
                                >
                                    {{ statusLabel(row.status) }}
                                </span>
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
                    {{ (currentPage - 1) * perPage + 1 }}–{{
                        Math.min(currentPage * perPage, filteredRows.length)
                    }}
                    of {{ filteredRows.length }}
                </p>
                <nav class="flex items-center gap-1" aria-label="Pagination">
                    <button
                        type="button"
                        class="flex h-9 min-w-9 items-center justify-center rounded-lg border border-border text-sm font-medium text-muted-foreground transition hover:border-[#FF6600]/50 hover:text-[#FF6600] disabled:cursor-not-allowed disabled:opacity-40"
                        :disabled="currentPage <= 1"
                        @click="goPage(currentPage - 1)"
                    >
                        <ChevronLeft class="h-4 w-4" />
                        <span class="sr-only">Previous</span>
                    </button>
                    <button
                        v-for="n in pageNumbers"
                        :key="n"
                        type="button"
                        class="h-9 min-w-9 rounded-lg text-sm font-semibold transition"
                        :class="
                            n === currentPage
                                ? 'bg-[#FF6600] text-white shadow-sm'
                                : 'text-muted-foreground hover:bg-muted hover:text-[#FF6600]'
                        "
                        @click="goPage(n)"
                    >
                        {{ n }}
                    </button>
                    <button
                        type="button"
                        class="flex h-9 min-w-9 items-center justify-center rounded-lg border border-border text-sm font-medium text-muted-foreground transition hover:border-[#FF6600]/50 hover:text-[#FF6600] disabled:cursor-not-allowed disabled:opacity-40"
                        :disabled="currentPage >= totalPages"
                        @click="goPage(currentPage + 1)"
                    >
                        <ChevronRight class="h-4 w-4" />
                        <span class="sr-only">Next</span>
                    </button>
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

        <!-- View product -->
        <Dialog v-model:open="viewOpen">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Product details</DialogTitle>
                    <DialogDescription> Read-only preview </DialogDescription>
                </DialogHeader>
                <dl v-if="viewProduct" class="grid gap-3 text-sm">
                    <div>
                        <dt class="text-muted-foreground">Name</dt>
                        <dd class="font-medium">{{ viewProduct.name }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">SKU</dt>
                        <dd class="font-mono">{{ viewProduct.sku }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Category</dt>
                        <dd>{{ viewProduct.category }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Price</dt>
                        <dd class="font-semibold text-[#FF6600]">
                            {{ formatPrice(viewProduct.price) }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Stock</dt>
                        <dd>{{ viewProduct.stock }}</dd>
                    </div>
                </dl>
                <DialogFooter>
                    <Button
                        class="bg-[#FF6600] text-white hover:bg-[#FF6600]/90"
                        @click="viewOpen = false"
                    >
                        Close
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Add / Edit product form -->
        <Dialog v-model:open="editOpen">
            <DialogContent
                :show-close-button="false"
                class="flex max-h-[min(92vh,880px)] flex-col gap-0 overflow-hidden border-border p-0 sm:max-w-4xl"
            >
                <div
                    class="shrink-0 border-b border-border bg-card px-5 py-4 sm:px-6"
                >
                    <div
                        class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <Button
                            variant="ghost"
                            class="w-fit justify-start gap-2 text-muted-foreground hover:text-[#FF6600]"
                            @click="closeEditForm"
                        >
                            <ArrowLeft class="h-4 w-4" stroke-width="2" />
                            Back to list
                        </Button>
                        <div class="min-w-0">
                            <DialogTitle
                                class="text-left text-xl font-bold tracking-tight"
                            >
                                {{
                                    editMode === 'add'
                                        ? 'Add new product'
                                        : 'Edit product'
                                }}
                            </DialogTitle>
                            <DialogDescription
                                class="text-left text-sm text-muted-foreground"
                            >
                                Fill in product details. Changes apply locally
                                until you connect an API.
                            </DialogDescription>
                        </div>
                    </div>
                </div>

                <div
                    class="flex-1 overflow-y-auto bg-[#F9FAFB] px-4 py-5 sm:px-6 dark:bg-muted/30"
                >
                    <div
                        class="grid gap-6 lg:grid-cols-2 lg:items-start"
                    >
                        <!-- General -->
                        <div
                            class="space-y-5 rounded-2xl border border-border bg-card p-5 shadow-sm"
                        >
                            <h3
                                class="text-sm font-semibold tracking-wide text-foreground uppercase"
                            >
                                General information
                            </h3>

                            <div class="grid gap-2">
                                <Label for="edit-name">Product name</Label>
                                <Input
                                    id="edit-name"
                                    v-model="editForm.name"
                                    placeholder="e.g. Aero Running Shoes"
                                    class="border-border bg-background focus-visible:border-[#FF6600] focus-visible:ring-[#FF6600]/25"
                                    :class="
                                        editErrors.name
                                            ? 'border-destructive ring-destructive/20'
                                            : ''
                                    "
                                    :aria-invalid="Boolean(editErrors.name)"
                                />
                                <p
                                    v-if="editErrors.name"
                                    class="text-xs font-medium text-destructive"
                                >
                                    {{ editErrors.name }}
                                </p>
                            </div>

                            <div class="grid gap-2">
                                <Label for="edit-description"
                                    >Product description</Label
                                >
                                <textarea
                                    id="edit-description"
                                    v-model="editForm.description"
                                    rows="8"
                                    placeholder="Describe materials, sizing, care instructions, and what makes this product stand out…"
                                    class="min-h-[180px] w-full resize-y rounded-lg border border-border bg-background px-3 py-2.5 text-sm text-foreground placeholder:text-muted-foreground focus:border-[#FF6600] focus:ring-2 focus:ring-[#FF6600]/20 focus:outline-none"
                                />
                                <p class="text-xs text-muted-foreground">
                                    Shown on the product page. Rich text
                                    formatting can be added when you integrate
                                    an editor.
                                </p>
                            </div>

                            <div class="grid gap-2">
                                <Label>Product photo</Label>
                                <input
                                    ref="editFileInputRef"
                                    type="file"
                                    accept="image/*"
                                    class="sr-only"
                                    @change="onEditFileChange"
                                />
                                <div
                                    class="relative rounded-xl border-2 border-dashed bg-muted/20 transition"
                                    :class="
                                        dropZoneDragging
                                            ? 'border-[#FF6600] bg-[#FF6600]/8'
                                            : 'border-[#FF6600]/40 hover:border-[#FF6600]/70'
                                    "
                                    @dragover="onDropZoneDragOver"
                                    @dragleave="onDropZoneDragLeave"
                                    @drop="onDropZoneDrop"
                                >
                                    <button
                                        type="button"
                                        class="flex min-h-[160px] w-full flex-col items-center justify-center gap-2 px-4 py-6 text-center sm:min-h-[200px]"
                                        @click="triggerEditFileInput"
                                    >
                                        <template v-if="!previewImageSrc">
                                            <Upload
                                                class="h-10 w-10 text-[#FF6600]/85"
                                                stroke-width="1.5"
                                            />
                                            <span
                                                class="text-sm font-medium text-foreground"
                                            >
                                                Drag & drop an image here, or
                                                click to browse
                                            </span>
                                            <span
                                                class="text-xs text-muted-foreground"
                                            >
                                                PNG, JPG or WebP · max 10MB
                                            </span>
                                        </template>
                                    </button>
                                    <div
                                        v-if="previewImageSrc"
                                        class="relative p-3"
                                    >
                                        <img
                                            :src="previewImageSrc"
                                            alt="Product preview"
                                            class="mx-auto max-h-52 w-auto max-w-full rounded-lg object-contain"
                                        />
                                        <p
                                            v-if="editForm.imageFile"
                                            class="mt-2 truncate text-center text-xs text-[#FF6600]"
                                        >
                                            {{ editForm.imageFile.name }}
                                        </p>
                                        <Button
                                            type="button"
                                            variant="outline"
                                            size="sm"
                                            class="mt-3 w-full border-border"
                                            @click.stop="clearProductImage"
                                        >
                                            Remove image
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Specifications -->
                        <div
                            class="space-y-5 rounded-2xl border border-border bg-card p-5 shadow-sm"
                        >
                            <h3
                                class="text-sm font-semibold tracking-wide text-foreground uppercase"
                            >
                                Specifications
                            </h3>

                            <div class="relative grid gap-2">
                                <Label for="edit-category">Category</Label>
                                <div class="relative">
                                    <Search
                                        class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                                        stroke-width="2"
                                    />
                                    <Input
                                        id="edit-category"
                                        v-model="categoryQuery"
                                        type="text"
                                        autocomplete="off"
                                        placeholder="Search or type a category…"
                                        class="border-border bg-background pl-9 focus-visible:border-[#FF6600] focus-visible:ring-[#FF6600]/25"
                                        :class="
                                            editErrors.category
                                                ? 'border-destructive'
                                                : ''
                                        "
                                        @focus="categoryDropdownOpen = true"
                                        @blur="onCategoryBlur"
                                        @input="
                                            editErrors.category = '';
                                            categoryDropdownOpen = true
                                        "
                                    />
                                    <div
                                        v-if="
                                            categoryDropdownOpen &&
                                            filteredCategoryOptions.length > 0
                                        "
                                        class="absolute top-full right-0 left-0 z-10 mt-1 max-h-48 overflow-auto rounded-lg border border-border bg-popover py-1 shadow-lg"
                                    >
                                        <button
                                            v-for="c in filteredCategoryOptions"
                                            :key="c"
                                            type="button"
                                            class="flex w-full px-3 py-2 text-left text-sm text-foreground hover:bg-[#FF6600]/10 hover:text-[#FF6600]"
                                            @mousedown.prevent="selectCategory(c)"
                                        >
                                            {{ c }}
                                        </button>
                                    </div>
                                </div>
                                <p
                                    v-if="editErrors.category"
                                    class="text-xs font-medium text-destructive"
                                >
                                    {{ editErrors.category }}
                                </p>
                            </div>

                            <div class="grid gap-2">
                                <Label for="edit-price">Price</Label>
                                <div class="relative">
                                    <span
                                        class="pointer-events-none absolute top-1/2 left-3 -translate-y-1/2 text-sm font-semibold text-muted-foreground"
                                        >$</span
                                    >
                                    <Input
                                        id="edit-price"
                                        v-model="editForm.price"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        placeholder="0.00"
                                        class="border-border bg-background pl-8 focus-visible:border-[#FF6600] focus-visible:ring-[#FF6600]/25"
                                        :class="
                                            editErrors.price
                                                ? 'border-destructive'
                                                : ''
                                        "
                                        @input="editErrors.price = ''"
                                    />
                                </div>
                                <p
                                    v-if="editErrors.price"
                                    class="text-xs font-medium text-destructive"
                                >
                                    {{ editErrors.price }}
                                </p>
                            </div>

                            <div class="grid gap-2">
                                <Label for="edit-stock-qty"
                                    >Quantity / stock</Label
                                >
                                <div class="flex items-center gap-2">
                                    <Button
                                        type="button"
                                        variant="outline"
                                        size="icon"
                                        class="h-11 w-11 shrink-0 border-border hover:border-[#FF6600]/50 hover:text-[#FF6600]"
                                        :disabled="editForm.stock <= 0"
                                        aria-label="Decrease stock"
                                        @click="adjustStock(-1)"
                                    >
                                        <Minus class="h-4 w-4" />
                                    </Button>
                                    <Input
                                        id="edit-stock-qty"
                                        v-model.number="editForm.stock"
                                        type="number"
                                        min="0"
                                        step="1"
                                        class="h-11 border-border bg-background text-center font-semibold tabular-nums focus-visible:border-[#FF6600] focus-visible:ring-[#FF6600]/25"
                                        :class="
                                            editErrors.stock
                                                ? 'border-destructive'
                                                : ''
                                        "
                                        @input="editErrors.stock = ''"
                                    />
                                    <Button
                                        type="button"
                                        variant="outline"
                                        size="icon"
                                        class="h-11 w-11 shrink-0 border-border hover:border-[#FF6600]/50 hover:text-[#FF6600]"
                                        aria-label="Increase stock"
                                        @click="adjustStock(1)"
                                    >
                                        <Plus class="h-4 w-4" />
                                    </Button>
                                </div>
                                <p
                                    v-if="editErrors.stock"
                                    class="text-xs font-medium text-destructive"
                                >
                                    {{ editErrors.stock }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="flex shrink-0 flex-col-reverse gap-3 border-t border-border bg-card px-4 py-4 sm:flex-row sm:justify-end sm:px-6"
                >
                    <Button
                        variant="ghost"
                        class="text-muted-foreground hover:text-foreground"
                        :disabled="saveLoading"
                        @click="closeEditForm"
                    >
                        Cancel
                    </Button>
                    <Button
                        class="min-w-[160px] bg-[#FF6600] font-semibold text-white shadow-sm hover:bg-[#FF6600]/90 disabled:opacity-70"
                        :disabled="saveLoading"
                        @click="saveEdit"
                    >
                        <Loader2
                            v-if="saveLoading"
                            class="mr-2 h-4 w-4 animate-spin"
                        />
                        {{
                            saveLoading ? 'Saving…' : 'Save product'
                        }}
                    </Button>
                </div>
            </DialogContent>
        </Dialog>
    </div>
</template>
