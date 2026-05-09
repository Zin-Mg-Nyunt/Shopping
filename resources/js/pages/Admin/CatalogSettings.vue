<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import {
    BadgePercent,
    Calendar,
    FolderTree,
    Layers,
    Pencil,
    Plus,
    Tag,
    Trash2,
} from 'lucide-vue-next';
import CatalogDeleteConfirmDialog from '@/components/admin/CatalogDeleteConfirmDialog.vue';
import { ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import { route } from 'ziggy-js';
import AdminDashboardLayout from '@/layouts/AdminDashboardLayout.vue';

defineOptions({ layout: AdminDashboardLayout });

const props = defineProps({
    categories: { type: Array, default: () => [] },
    brands: { type: Array, default: () => [] },
    promos: { type: Array, default: () => [] },
});

function formatDate(value) {
    if (!value) {
        return '-';
    }

    return new Date(value).toLocaleDateString('en-GB');
}

function slugify(text) {
    return String(text ?? '')
        .trim()
        .toLowerCase()
        .replace(/\s+/g, '-')
        .replace(/[^a-z0-9-]/g, '')
        .replace(/-+/g, '-')
        .replace(/^-|-$/g, '');
}

function toastFirstFormError(form) {
    const messages = Object.values(form.errors);

    if (messages.length > 0) {
        toast.error(messages[0]);
    }
}

const deleteOpen = ref(false);
const deleteKind = ref('category');
const deleteTarget = ref(null);
const deleteProcessing = ref(false);

watch(deleteOpen, (isOpen) => {
    if (!isOpen) {
        deleteTarget.value = null;
        deleteProcessing.value = false;
    }
});

function openDeleteCategory(category) {
    deleteKind.value = 'category';
    deleteTarget.value = category;
    deleteOpen.value = true;
}

function openDeleteBrand(brand) {
    deleteKind.value = 'brand';
    deleteTarget.value = brand;
    deleteOpen.value = true;
}

function openDeletePromo(promo) {
    deleteKind.value = 'promo';
    deleteTarget.value = promo;
    deleteOpen.value = true;
}

function confirmDeleteCatalogItem() {
    const target = deleteTarget.value;
    const kind = deleteKind.value;

    if (!target) {
        return;
    }

    deleteProcessing.value = true;

    let deleteUrl;

    if (kind === 'category') {
        deleteUrl = route('admin.categories.destroy', target.id);
    } else if (kind === 'brand') {
        deleteUrl = route('admin.brands.destroy', target.id);
    } else {
        deleteUrl = route('admin.promos.destroy', target.id);
    }

    router.delete(deleteUrl, {
        preserveScroll: true,
        onFinish: () => {
            deleteProcessing.value = false;
        },
        onSuccess: () => {
            deleteOpen.value = false;

            if (kind === 'category' && editingCategoryId.value === target.id) {
                resetCategoryForm();
            }

            if (kind === 'brand' && editingBrandId.value === target.id) {
                resetBrandForm();
            }

            if (kind === 'promo' && editingPromoId.value === target.id) {
                resetPromoForm();
            }

            toast.success(
                `${kind} "${target.name || target.code}" deleted successfully.`,
            );
        },
        onError: () =>
            toast.error('Delete failed. Check validation or server logs.'),
    });
}

// ——— Categories ———
const categoryForm = useForm({
    name: '',
    slug: '',
});
const editingCategoryId = ref(null);
const manuallyUpdatedSlug = ref(false);

function handleCategoryNameInput(event) {
    if (manuallyUpdatedSlug.value) return;
    categoryForm.slug = slugify(event.target.value);
}

function handleCategorySlugInput(event) {
    if (event.target.value === '') {
        manuallyUpdatedSlug.value = false;
        return;
    }
    manuallyUpdatedSlug.value = true;
}

function resetCategoryForm() {
    categoryForm.reset();
    categoryForm.clearErrors();
    editingCategoryId.value = null;
}

function startEditCategory(category) {
    editingCategoryId.value = category.id;
    categoryForm.name = category.name ?? '';
    categoryForm.slug = category.slug ?? '';
    categoryForm.clearErrors();
}

function categorySlugDuplicate(slug, excludeId) {
    return props.categories.some((c) => c.slug === slug && c.id !== excludeId);
}

function saveCategory() {
    const name = categoryForm.name.trim();
    let slug = categoryForm.slug.trim();

    if (!name) {
        toast.error('Category name is required.');

        return;
    }

    if (!slug) {
        toast.error(
            'Category slug is required (or use a name that slugifies).',
        );

        return;
    }

    if (categorySlugDuplicate(slug, editingCategoryId.value)) {
        toast.error('Another category already uses that slug.');

        return;
    }

    categoryForm.name = name;
    categoryForm.slug = slug;

    const options = {
        preserveScroll: true,
        onSuccess: () => {
            resetCategoryForm();
            toast.success(`Category "${name}" saved successfully.`);
        },
        onError: () => toastFirstFormError(categoryForm),
    };

    if (editingCategoryId.value != null) {
        categoryForm.put(
            route('admin.categories.update', editingCategoryId.value),
            options,
        );

        return;
    }

    categoryForm.post(route('admin.categories.store'), options);
}

// ——— Brands ———
const brandForm = useForm({
    name: '',
    slug: '',
});
const editingBrandId = ref(null);

function handleBrandNameInput(event) {
    if (manuallyUpdatedSlug.value) return;
    brandForm.slug = slugify(event.target.value);
}

function handleBrandSlugInput(event) {
    if (event.target.value === '') {
        manuallyUpdatedSlug.value = false;
        return;
    }
    manuallyUpdatedSlug.value = true;
}

function resetBrandForm() {
    brandForm.reset();
    brandForm.clearErrors();
    editingBrandId.value = null;
}

function startEditBrand(brand) {
    editingBrandId.value = brand.id;
    brandForm.name = brand.name ?? '';
    brandForm.slug = brand.slug ?? '';
    brandForm.clearErrors();
}

function brandSlugDuplicate(slug, excludeId) {
    return props.brands.some((b) => b.slug === slug && b.id !== excludeId);
}

function saveBrand() {
    const name = brandForm.name.trim();
    let slug = brandForm.slug.trim();

    if (!name) {
        toast.error('Brand name is required.');

        return;
    }

    if (!slug) {
        toast.error('Brand slug is required (or use a name that slugifies).');

        return;
    }

    if (brandSlugDuplicate(slug, editingBrandId.value)) {
        toast.error('Another brand already uses that slug.');

        return;
    }

    brandForm.name = name;
    brandForm.slug = slug;

    const options = {
        preserveScroll: true,
        onSuccess: () => {
            resetBrandForm();
            toast.success(`Brand "${name}" saved successfully.`);
        },
        onError: () => toastFirstFormError(brandForm),
    };

    if (editingBrandId.value != null) {
        brandForm.put(
            route('admin.brands.update', editingBrandId.value),
            options,
        );

        return;
    }

    brandForm.post(route('admin.brands.store'), options);
}

// ——— Promos ———
const promoForm = useForm({
    code: '',
    discount: '',
    discount_type: 'percentage',
    expires_at: '',
    user_limit: '',
});
const editingPromoId = ref(null);

function normalizePromoDiscountRaw(raw, maxFractionDigits = 2) {
    let s = String(raw).replace(/[^0-9.]/g, '');
    const firstDot = s.indexOf('.');

    if (firstDot !== -1) {
        s = s.slice(0, firstDot + 1) + s.slice(firstDot + 1).replace(/\./g, '');
    }

    if (firstDot !== -1) {
        const [intPart, frac = ''] = s.split('.');
        s = intPart + '.' + frac.slice(0, maxFractionDigits);
    }

    return s;
}

function clampPercentageDiscountString(s) {
    if (s === '' || s === '.') {
        return s;
    }

    const n = Number.parseFloat(s);

    if (Number.isNaN(n)) {
        return s;
    }

    if (n > 100) {
        return '100';
    }

    return s;
}

function handlePromoDiscountInput(event) {
    let cleaned = normalizePromoDiscountRaw(event.target.value);

    if (promoForm.discount_type === 'percentage') {
        cleaned = clampPercentageDiscountString(cleaned);
    }

    if (cleaned !== '' && cleaned !== '.') {
        const n = Number.parseFloat(cleaned);

        if (!Number.isNaN(n) && n < 0) {
            cleaned = '0';
        }
    }

    promoForm.discount = cleaned;
    event.target.value = cleaned;
}

watch(
    () => promoForm.discount_type,
    (type) => {
        if (type !== 'percentage') {
            return;
        }

        const raw = String(promoForm.discount ?? '').trim();

        if (raw === '' || raw === '.') {
            return;
        }

        const n = Number.parseFloat(raw);

        if (!Number.isNaN(n) && n > 100) {
            promoForm.discount = '100';
        }
    },
);

function parsePromoDiscountForForm(promo) {
    const str = String(promo?.discount ?? '').trim();
    const type = promo?.type === 'fixed' ? 'fixed' : 'percentage';

    if (type === 'percentage') {
        const num = Number.parseFloat(str.replace(/%/g, ''));

        return Number.isNaN(num) ? '' : String(num);
    }

    const num = Number.parseFloat(str.replace(/USD/gi, '').trim());

    return Number.isNaN(num) ? '' : String(num);
}

function dateInputFromPromo(validUntil) {
    if (!validUntil) {
        return '';
    }
    const d = new Date(validUntil);

    if (Number.isNaN(d.getTime())) {
        return '';
    }

    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
}

function resetPromoForm() {
    promoForm.reset();
    promoForm.clearErrors();
    editingPromoId.value = null;
}

function startEditPromo(promo) {
    editingPromoId.value = promo.id;
    promoForm.code = promo.code ?? '';
    promoForm.discount = parsePromoDiscountForForm(promo);
    promoForm.discount_type = promo.type === 'fixed' ? 'fixed' : 'percentage';
    promoForm.expires_at = dateInputFromPromo(promo.valid_until);
    promoForm.user_limit =
        promo.usage_limit != null && promo.usage_limit !== ''
            ? String(promo.usage_limit)
            : '';
    promoForm.clearErrors();
}

function promoCodeDuplicate(code, excludeId) {
    const normalized = code.trim().toUpperCase();

    return props.promos.some(
        (p) =>
            String(p.code).toUpperCase() === normalized && p.id !== excludeId,
    );
}

function savePromo() {
    const code = promoForm.code.trim().toUpperCase();

    if (!code) {
        toast.error('Promo code is required.');

        return;
    }

    const discountNum = Number.parseFloat(String(promoForm.discount));

    if (Number.isNaN(discountNum) || discountNum < 0) {
        toast.error('Enter a valid discount amount.');

        return;
    }

    if (promoForm.discount_type === 'percentage' && discountNum > 100) {
        toast.error('Percentage discount cannot exceed 100.');

        return;
    }

    if (promoCodeDuplicate(code, editingPromoId.value)) {
        toast.error('Another promo already uses that code.');

        return;
    }

    const usageRaw = String(promoForm.user_limit ?? '').trim();
    const usageLimitParsed =
        usageRaw === '' ? null : Number.parseInt(usageRaw, 10);

    if (usageRaw !== '' && Number.isNaN(usageLimitParsed)) {
        toast.error('Usage limit must be a whole number or empty.');

        return;
    }

    const type = promoForm.discount_type === 'fixed' ? 'fixed' : 'percentage';
    const expiresRaw = String(promoForm.expires_at ?? '').trim();

    promoForm.code = code;
    promoForm.discount = discountNum;
    promoForm.discount_type = type;
    promoForm.expires_at = expiresRaw === '' ? null : expiresRaw;
    promoForm.user_limit = usageLimitParsed;

    const options = {
        preserveScroll: true,
        onSuccess: () => {
            resetPromoForm();
            toast.success(`Promo "${code}" saved successfully.`);
        },
        onError: () => toastFirstFormError(promoForm),
    };

    if (editingPromoId.value != null) {
        promoForm.put(
            route('admin.promos.update', editingPromoId.value),
            options,
        );

        return;
    }

    promoForm.post(route('admin.promos.store'), options);
}
</script>

<template>
    <Head title="Admin · Catalog settings" />

    <div class="space-y-6">
        <div
            class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between"
        >
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-foreground">
                    Catalog settings
                </h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Manage categories, brands, and promo codes from one place.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
            <div class="rounded-xl border border-border bg-card p-4 shadow-sm">
                <p class="text-xs text-muted-foreground">Categories</p>
                <p class="mt-1 text-2xl font-bold text-foreground">
                    {{ categories.length }}
                </p>
                <div
                    class="mt-2 flex items-center gap-2 text-xs text-muted-foreground"
                >
                    <FolderTree class="h-3.5 w-3.5" />
                    Product grouping
                </div>
            </div>

            <div class="rounded-xl border border-border bg-card p-4 shadow-sm">
                <p class="text-xs text-muted-foreground">Brands</p>
                <p class="mt-1 text-2xl font-bold text-foreground">
                    {{ brands.length }}
                </p>
                <div
                    class="mt-2 flex items-center gap-2 text-xs text-muted-foreground"
                >
                    <Layers class="h-3.5 w-3.5" />
                    Brand management
                </div>
            </div>

            <div class="rounded-xl border border-border bg-card p-4 shadow-sm">
                <p class="text-xs text-muted-foreground">Promo codes</p>
                <p class="mt-1 text-2xl font-bold text-foreground">
                    {{ promos.length }}
                </p>
                <div
                    class="mt-2 flex items-center gap-2 text-xs text-muted-foreground"
                >
                    <BadgePercent class="h-3.5 w-3.5" />
                    Discount campaigns
                </div>
            </div>
        </div>

        <section class="space-y-5">
            <article class="rounded-2xl border border-border bg-card shadow-sm">
                <div class="border-b border-border px-5 py-4">
                    <div
                        class="flex flex-wrap items-center justify-between gap-3"
                    >
                        <div>
                            <h2 class="text-base font-semibold text-foreground">
                                Category CRUD
                            </h2>
                            <p class="text-xs text-muted-foreground">
                                Create, update, and remove product categories.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="border-b border-border p-5">
                    <div
                        class="flex flex-col gap-3 md:flex-row md:flex-wrap md:items-center"
                    >
                        <div
                            class="grid flex-1 grid-cols-1 gap-3 sm:grid-cols-2"
                        >
                            <input
                                v-model="categoryForm.name"
                                type="text"
                                placeholder="Category name"
                                class="h-10 rounded-lg border border-border bg-background px-3 text-sm text-foreground ring-0 outline-none placeholder:text-muted-foreground focus:border-primary/50"
                                @input="handleCategoryNameInput"
                            />
                            <input
                                v-model="categoryForm.slug"
                                type="text"
                                placeholder="Slug (optional)"
                                class="h-10 rounded-lg border border-border bg-background px-3 text-sm text-foreground ring-0 outline-none placeholder:text-muted-foreground focus:border-primary/50"
                                @input="handleCategorySlugInput"
                            />
                        </div>
                        <div class="flex shrink-0 flex-wrap gap-2">
                            <button
                                type="button"
                                class="inline-flex h-10 min-w-32 items-center gap-2 rounded-lg bg-primary px-3 text-xs font-semibold text-primary-foreground transition hover:bg-primary/90 disabled:cursor-not-allowed disabled:opacity-50 xl:flex-none"
                                :disabled="categoryForm.processing"
                                @click="saveCategory"
                            >
                                {{
                                    editingCategoryId != null
                                        ? 'Update category'
                                        : '+ Add category'
                                }}
                            </button>
                            <button
                                v-if="editingCategoryId != null"
                                type="button"
                                class="h-10 rounded-lg border border-border px-3 text-sm font-medium text-muted-foreground transition hover:border-primary/40 hover:text-foreground"
                                :disabled="categoryForm.processing"
                                @click="resetCategoryForm"
                            >
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px] text-left text-sm">
                        <thead>
                            <tr
                                class="border-b border-border bg-muted/40 text-muted-foreground"
                            >
                                <th class="px-4 py-3 font-semibold">Name</th>
                                <th class="px-4 py-3 font-semibold">Slug</th>
                                <th class="px-4 py-3 font-semibold">
                                    Products
                                </th>
                                <th class="px-4 py-3 font-semibold">Updated</th>
                                <th class="px-4 py-3 text-right font-semibold">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <tr
                                v-for="category in categories"
                                :key="category.id"
                                class="transition hover:bg-muted/30"
                            >
                                <td
                                    class="px-4 py-3 font-medium text-foreground"
                                >
                                    {{ category.name }}
                                </td>
                                <td class="px-4 py-3 text-muted-foreground">
                                    {{ category.slug }}
                                </td>
                                <td class="px-4 py-3 text-muted-foreground">
                                    {{ category.products_count }}
                                </td>
                                <td class="px-4 py-3 text-muted-foreground">
                                    {{ formatDate(category.updated_at) }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <button
                                            type="button"
                                            class="inline-flex items-center gap-1 rounded-md border border-border px-2.5 py-1.5 text-xs font-medium text-foreground transition hover:border-primary/40 hover:text-primary"
                                            @click="startEditCategory(category)"
                                        >
                                            <Pencil class="h-3.5 w-3.5" />
                                            Edit
                                        </button>
                                        <button
                                            type="button"
                                            class="inline-flex items-center gap-1 rounded-md border border-destructive/40 px-2.5 py-1.5 text-xs font-medium text-destructive transition hover:bg-destructive/10"
                                            @click="
                                                openDeleteCategory(category)
                                            "
                                        >
                                            <Trash2 class="h-3.5 w-3.5" />
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="categories.length === 0">
                                <td
                                    colspan="5"
                                    class="px-4 py-10 text-center text-sm text-muted-foreground"
                                >
                                    No categories yet. Add one above.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </article>

            <article class="rounded-2xl border border-border bg-card shadow-sm">
                <div class="border-b border-border px-5 py-4">
                    <div
                        class="flex flex-wrap items-center justify-between gap-3"
                    >
                        <div>
                            <h2 class="text-base font-semibold text-foreground">
                                Brand CRUD
                            </h2>
                            <p class="text-xs text-muted-foreground">
                                Maintain available product brands and
                                visibility.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="border-b border-border p-5">
                    <div
                        class="flex flex-col gap-3 md:flex-row md:flex-wrap md:items-center"
                    >
                        <div
                            class="grid flex-1 grid-cols-1 gap-3 sm:grid-cols-2"
                        >
                            <input
                                v-model="brandForm.name"
                                type="text"
                                placeholder="Brand name"
                                class="h-10 rounded-lg border border-border bg-background px-3 text-sm text-foreground ring-0 outline-none placeholder:text-muted-foreground focus:border-primary/50"
                                @input="handleBrandNameInput"
                            />
                            <input
                                v-model="brandForm.slug"
                                type="text"
                                placeholder="Slug (optional)"
                                class="h-10 rounded-lg border border-border bg-background px-3 text-sm text-foreground ring-0 outline-none placeholder:text-muted-foreground focus:border-primary/50"
                                @input="handleBrandSlugInput"
                            />
                        </div>
                        <div class="flex shrink-0 flex-wrap gap-2">
                            <button
                                type="button"
                                class="inline-flex h-10 min-w-32 items-center gap-2 rounded-lg bg-primary px-3 text-xs font-semibold text-primary-foreground transition hover:bg-primary/90 disabled:cursor-not-allowed disabled:opacity-50 xl:flex-none"
                                :disabled="brandForm.processing"
                                @click="saveBrand"
                            >
                                {{
                                    editingBrandId != null
                                        ? 'Update brand'
                                        : '+ Add brand'
                                }}
                            </button>
                            <button
                                v-if="editingBrandId != null"
                                type="button"
                                class="h-10 rounded-lg border border-border px-3 text-sm font-medium text-muted-foreground transition hover:border-primary/40 hover:text-foreground"
                                :disabled="brandForm.processing"
                                @click="resetBrandForm"
                            >
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px] text-left text-sm">
                        <thead>
                            <tr
                                class="border-b border-border bg-muted/40 text-muted-foreground"
                            >
                                <th class="px-4 py-3 font-semibold">Name</th>
                                <th class="px-4 py-3 font-semibold">Slug</th>
                                <th class="px-4 py-3 font-semibold">
                                    Products
                                </th>
                                <th class="px-4 py-3 font-semibold">Updated</th>
                                <th class="px-4 py-3 text-right font-semibold">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <tr
                                v-for="brand in brands"
                                :key="brand.id"
                                class="transition hover:bg-muted/30"
                            >
                                <td
                                    class="px-4 py-3 font-medium text-foreground"
                                >
                                    {{ brand.name }}
                                </td>
                                <td class="px-4 py-3 text-muted-foreground">
                                    {{ brand.slug }}
                                </td>
                                <td class="px-4 py-3 text-muted-foreground">
                                    {{ brand.products_count }}
                                </td>
                                <td class="px-4 py-3 text-muted-foreground">
                                    {{ formatDate(brand.updated_at) }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <button
                                            type="button"
                                            class="inline-flex items-center gap-1 rounded-md border border-border px-2.5 py-1.5 text-xs font-medium text-foreground transition hover:border-primary/40 hover:text-primary"
                                            @click="startEditBrand(brand)"
                                        >
                                            <Pencil class="h-3.5 w-3.5" />
                                            Edit
                                        </button>
                                        <button
                                            type="button"
                                            class="inline-flex items-center gap-1 rounded-md border border-destructive/40 px-2.5 py-1.5 text-xs font-medium text-destructive transition hover:bg-destructive/10"
                                            @click="openDeleteBrand(brand)"
                                        >
                                            <Trash2 class="h-3.5 w-3.5" />
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="brands.length === 0">
                                <td
                                    colspan="5"
                                    class="px-4 py-10 text-center text-sm text-muted-foreground"
                                >
                                    No brands yet. Add one above.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </article>

            <article class="rounded-2xl border border-border bg-card shadow-sm">
                <div class="border-b border-border px-5 py-4">
                    <div
                        class="flex flex-wrap items-center justify-between gap-3"
                    >
                        <div>
                            <h2 class="text-base font-semibold text-foreground">
                                Promo code CRUD
                            </h2>
                            <p class="text-xs text-muted-foreground">
                                Prepare discount campaigns and set active
                                windows.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="border-b border-border p-5">
                    <div
                        class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6"
                    >
                        <input
                            v-model="promoForm.code"
                            type="text"
                            placeholder="Code (e.g. SUMMER10)"
                            class="h-10 rounded-lg border border-border bg-background px-3 text-sm text-foreground uppercase ring-0 outline-none placeholder:text-muted-foreground focus:border-primary/50"
                        />
                        <input
                            v-model="promoForm.discount"
                            inputmode="decimal"
                            type="text"
                            placeholder="Discount amount"
                            class="h-10 rounded-lg border border-border bg-background px-3 text-sm text-foreground ring-0 outline-none placeholder:text-muted-foreground focus:border-primary/50"
                            @input="handlePromoDiscountInput($event)"
                        />
                        <select
                            v-model="promoForm.discount_type"
                            class="h-10 rounded-lg border border-border bg-background px-3 text-sm text-foreground ring-0 outline-none focus:border-primary/50"
                        >
                            <option value="percentage">Percentage</option>
                            <option value="fixed">Fixed (USD)</option>
                        </select>
                        <input
                            v-model="promoForm.expires_at"
                            type="date"
                            class="h-10 rounded-lg border border-border bg-background px-3 text-sm text-foreground ring-0 outline-none focus:border-primary/50"
                        />
                        <input
                            v-model="promoForm.user_limit"
                            type="text"
                            inputmode="numeric"
                            placeholder="Usage limit (optional)"
                            class="h-10 rounded-lg border border-border bg-background px-3 text-sm text-foreground ring-0 outline-none placeholder:text-muted-foreground focus:border-primary/50"
                        />
                        <div class="flex flex-wrap gap-2">
                            <button
                                type="button"
                                class="inline-flex h-10 min-w-32 items-center gap-2 rounded-lg bg-primary px-3 text-xs font-semibold text-primary-foreground transition hover:bg-primary/90 disabled:cursor-not-allowed disabled:opacity-50 xl:flex-none"
                                :disabled="promoForm.processing"
                                @click="savePromo"
                            >
                                {{
                                    editingPromoId != null
                                        ? 'Update promo'
                                        : '+ Add promo'
                                }}
                            </button>
                            <button
                                v-if="editingPromoId != null"
                                type="button"
                                class="h-10 rounded-lg border border-border px-3 text-sm font-medium text-muted-foreground transition hover:border-primary/40 hover:text-foreground"
                                :disabled="promoForm.processing"
                                @click="resetPromoForm"
                            >
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[860px] text-left text-sm">
                        <thead>
                            <tr
                                class="border-b border-border bg-muted/40 text-muted-foreground"
                            >
                                <th class="px-4 py-3 font-semibold">Code</th>
                                <th class="px-4 py-3 font-semibold">
                                    Discount
                                </th>
                                <th class="px-4 py-3 font-semibold">Type</th>
                                <th class="px-4 py-3 font-semibold">
                                    Usage limit
                                </th>
                                <th class="px-4 py-3 font-semibold">
                                    Valid until
                                </th>
                                <th class="px-4 py-3 font-semibold">Status</th>
                                <th class="px-4 py-3 text-right font-semibold">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <tr
                                v-for="promo in promos"
                                :key="promo.id"
                                class="transition hover:bg-muted/30"
                            >
                                <td
                                    class="px-4 py-3 font-semibold text-foreground"
                                >
                                    <div
                                        class="inline-flex items-center gap-1.5 rounded-md bg-muted px-2 py-1 text-xs"
                                    >
                                        <Tag class="h-3 w-3" />
                                        {{ promo.code }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-muted-foreground">
                                    {{ promo.discount }}
                                </td>
                                <td class="px-4 py-3 text-muted-foreground">
                                    {{ promo.type }}
                                </td>
                                <td class="px-4 py-3 text-muted-foreground">
                                    {{ promo.usage_limit || 'Unlimited' }}
                                </td>
                                <td class="px-4 py-3 text-muted-foreground">
                                    <span
                                        class="inline-flex items-center gap-1.5"
                                    >
                                        <Calendar class="h-3.5 w-3.5" />
                                        {{
                                            promo.valid_until
                                                ? formatDate(promo.valid_until)
                                                : 'N/A'
                                        }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold"
                                        :class="
                                            promo.status === 'Active'
                                                ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300'
                                                : 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300'
                                        "
                                    >
                                        {{ promo.status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <button
                                            type="button"
                                            class="inline-flex items-center gap-1 rounded-md border border-border px-2.5 py-1.5 text-xs font-medium text-foreground transition hover:border-primary/40 hover:text-primary"
                                            @click="startEditPromo(promo)"
                                        >
                                            <Pencil class="h-3.5 w-3.5" />
                                            Edit
                                        </button>
                                        <button
                                            type="button"
                                            class="inline-flex items-center gap-1 rounded-md border border-destructive/40 px-2.5 py-1.5 text-xs font-medium text-destructive transition hover:bg-destructive/10"
                                            @click="openDeletePromo(promo)"
                                        >
                                            <Trash2 class="h-3.5 w-3.5" />
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="promos.length === 0">
                                <td
                                    colspan="7"
                                    class="px-4 py-10 text-center text-sm text-muted-foreground"
                                >
                                    No promo codes yet. Add one above.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </article>
        </section>
    </div>

    <CatalogDeleteConfirmDialog
        v-model="deleteOpen"
        :kind="deleteKind"
        :target="deleteTarget"
        :processing="deleteProcessing"
        @confirm="confirmDeleteCatalogItem"
    />
</template>
