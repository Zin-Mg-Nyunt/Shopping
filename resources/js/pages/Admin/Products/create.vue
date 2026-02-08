<script setup>
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useGenerateName } from '@/composables/useGenerateName';
import { useGenerateSlug } from '@/composables/useGenerateSlug';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ImagePlus, Search, X } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const generateName = useGenerateName();
const generateSlug = useGenerateSlug();
const props = defineProps({
    brands: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
});

const form = useForm({
    brand: null,
    categories: [],
    thumbnail: null,
    name: '',
    slug: '',
    description: '',
    price: '',
    discount_price: null,
    stock: '',
    discount_percentage: null,
});

const isEditingSlug = ref(false);
const manualSlugCreate = () => {
    isEditingSlug.value = true;
    if (form.slug == '') {
        isEditingSlug.value = false;
        form.slug = generateSlug(form.name);
    }
};
watch(
    () => form.name,
    (newValue) => {
        if (!isEditingSlug.value) {
            form.slug = generateSlug(newValue);
        }
    },
);

const addBrand = (brandName) => {
    const name = brandName;
    const slug = generateSlug(name);
    form.brand = {
        name,
        slug,
        isNew: true,
    };
};
const manualBrandCreate = (e) => {
    const brandName = generateName(e.target.value);
    const existingBrand = props.brands.find(
        (b) => b.slug == generateSlug(brandName),
    );

    if (existingBrand) {
        form.brand = existingBrand;
    } else {
        addBrand(brandName);
    }
    e.target.value = '';
};

const addCategory = (categoryName) => {
    const name = generateName(categoryName);
    const existingCategory = form.categories.find(
        (c) => c.slug == generateSlug(name),
    );
    const oldCategory = props.categories.find((c) => {
        return c.slug == generateSlug(name);
    });
    if (existingCategory) {
        return;
    } else if (!existingCategory && oldCategory) {
        form.categories.push(oldCategory);
    } else {
        const slug = generateSlug(name);
        form.categories.push({
            name,
            slug,
            isNew: true,
        });
    }
};
const manualCategoryCreate = (e) => {
    const categoryName = generateName(e.target.value);
    addCategory(categoryName);
    e.target.value = '';
};
const removeCategory = (category) => {
    form.categories = form.categories.filter((c) => c.slug !== category.slug);
};

watch(
    () => form.discount_percentage,
    (newValue) => {
        if (newValue) {
            form.discount_price = form.price - form.price * (newValue / 100);
        } else {
            form.discount_price = null;
            form.discount_percentage = null;
        }
    },
    { immediate: true },
);

watch(
    () => form.discount_price,
    (newValue) => {
        if (newValue) {
            form.discount_percentage = 100 - (newValue / form.price) * 100;
        } else {
            form.discount_percentage = null;
            form.discount_price = null;
        }
    },
    { immediate: true },
);

const addProduct = () => {
    alert('addProduct');
};
defineOptions({
    layout: AdminLayout,
});
</script>

<template>
    <Head title="Create Product - Admin" />
    <form
        class="space-y-6"
        @submit.prevent="addProduct"
    >
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1
                    class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white"
                >
                    Create Product
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Add a new product to your catalog
                </p>
            </div>
            <div class="flex items-center gap-2">
                <Button
                    type="button"
                    class="cursor-pointer"
                    variant="outline"
                    @click="router.visit(route('admin.products'))"
                >
                    Cancel
                </Button>
                <Button
                    type="submit"
                    class="cursor-pointer"
                >
                    Save
                </Button>
            </div>
        </div>

        <!-- 2-column grid: main left, sidebar right -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Main column -->
            <div class="space-y-6 lg:col-span-2">
                <!-- Basic Info -->
                <Card class="rounded-xl border-gray-200 dark:border-gray-700">
                    <CardHeader>
                        <CardTitle>Basic Information</CardTitle>
                        <CardDescription>
                            Product name, slug, and description
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-2">
                            <Label for="name">Product Name</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="Product name"
                                class="w-full"
                            />
                            <InputError :message="form.errors.name" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="slug">Slug</Label>
                            <Input
                                id="slug"
                                v-model="form.slug"
                                type="text"
                                placeholder="product-slug"
                                @input="manualSlugCreate"
                                class="w-full"
                            />
                            <InputError :message="form.errors.slug" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="description">Description</Label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="4"
                                placeholder="Product description..."
                                class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:cursor-not-allowed disabled:opacity-50"
                            />
                            <InputError :message="form.errors.description" />
                        </div>
                    </CardContent>
                </Card>

                <!-- Pricing Section (interconnected layout) -->
                <Card class="rounded-xl border-gray-200 dark:border-gray-700">
                    <CardHeader>
                        <CardTitle>Pricing</CardTitle>
                        <CardDescription>
                            Base price, discount, and final price
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div
                            class="flex flex-wrap items-end gap-4 rounded-lg border border-gray-200 bg-gray-50/50 p-4 dark:border-gray-700 dark:bg-gray-800/30"
                        >
                            <div class="grid min-w-[140px] flex-1 gap-2">
                                <Label for="price">Base Price</Label>
                                <Input
                                    id="price"
                                    v-model="form.price"
                                    type="text"
                                    placeholder="0.00"
                                    class="w-full"
                                />
                                <InputError :message="form.errors.price" />
                            </div>
                            <div
                                class="mb-2 flex shrink-0 items-center text-gray-400 dark:text-gray-500"
                                aria-hidden="true"
                            >
                                <span class="text-lg">→</span>
                            </div>
                            <div class="grid w-24 min-w-[96px] gap-2">
                                <Label for="discount_percentage"
                                    >Discount %</Label
                                >
                                <Input
                                    id="discount_percentage"
                                    v-model="form.discount_percentage"
                                    type="text"
                                    placeholder="0"
                                    class="w-full text-center"
                                />
                                <InputError
                                    :message="form.errors.discount_percentage"
                                />
                            </div>
                            <div
                                class="mb-2 flex shrink-0 items-center text-gray-400 dark:text-gray-500"
                                aria-hidden="true"
                            >
                                <span class="text-lg">→</span>
                            </div>
                            <div class="grid min-w-[140px] flex-1 gap-2">
                                <Label for="discount_price"
                                    >Discount Price</Label
                                >
                                <Input
                                    id="discount_price"
                                    v-model="form.discount_price"
                                    type="text"
                                    placeholder="0.00"
                                    class="w-full"
                                />
                                <InputError
                                    :message="form.errors.discount_price"
                                />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Organization: Brand & Category -->
                <Card class="rounded-xl border-gray-200 dark:border-gray-700">
                    <CardHeader>
                        <CardTitle>Organization</CardTitle>
                        <CardDescription> Brand and category </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <!-- Brand -->
                        <div class="space-y-3">
                            <Label>Brand</Label>
                            <div
                                v-if="form.brand"
                                class="flex flex-wrap items-center gap-2"
                            >
                                <Badge
                                    class="relative gap-1.5 overflow-visible rounded-md bg-secondary py-1.5 pr-1 pl-2.5"
                                >
                                    <span
                                        v-if="form.brand.isNew"
                                        class="absolute -top-1 -right-1 rounded-full bg-primary px-1 py-1"
                                    >
                                    </span>
                                    {{ form.brand.name }}
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        size="icon"
                                        class="h-5 w-5 shrink-0 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600"
                                        aria-label="Remove brand"
                                        @click="form.brand = null"
                                    >
                                        <X class="h-3.5 w-3.5" />
                                    </Button>
                                </Badge>
                            </div>
                            <div class="relative">
                                <Search
                                    class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400"
                                />
                                <Input
                                    type="text"
                                    placeholder="Search or add brand..."
                                    class="pl-9"
                                    @keydown.enter.prevent="manualBrandCreate"
                                />
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <Button
                                    type="button"
                                    v-for="b in brands"
                                    :key="b.id"
                                    variant="outline"
                                    size="sm"
                                    class="rounded-full"
                                    @click="form.brand = b"
                                    :disabled="
                                        form.brand && form.brand.slug == b.slug
                                    "
                                >
                                    {{ b.name }}
                                </Button>
                            </div>
                            <InputError :message="form.errors.brand" />
                        </div>

                        <!-- Category -->
                        <div class="space-y-3">
                            <Label>Category</Label>
                            <div
                                v-if="form.categories.length > 0"
                                class="flex flex-wrap items-center gap-2"
                            >
                                <Badge
                                    v-for="c in form.categories"
                                    :key="c.id"
                                    variant="secondary"
                                    class="relative gap-1.5 overflow-visible rounded-md py-1.5 pr-1 pl-2.5"
                                >
                                    <span
                                        v-if="c.isNew"
                                        class="absolute -top-1 -right-1 rounded-full bg-primary px-1 py-1"
                                    >
                                    </span>
                                    {{ c.name }}
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        size="icon"
                                        class="h-5 w-5 shrink-0 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600"
                                        aria-label="Remove category"
                                        @click="removeCategory(c)"
                                    >
                                        <X class="h-3.5 w-3.5" />
                                    </Button>
                                </Badge>
                            </div>
                            <div class="relative">
                                <Search
                                    class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400"
                                />
                                <Input
                                    @keydown.enter.prevent="
                                        manualCategoryCreate
                                    "
                                    type="text"
                                    placeholder="Search or add category..."
                                    class="pl-9"
                                />
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <Button
                                    type="button"
                                    v-for="c in categories"
                                    :key="c.id"
                                    variant="outline"
                                    size="sm"
                                    class="rounded-full"
                                    :disabled="
                                        form.categories.some(
                                            (cat) => cat.slug == c.slug,
                                        )
                                    "
                                    @click="addCategory(c.name)"
                                >
                                    {{ c.name }}
                                </Button>
                            </div>
                            <InputError :message="form.errors.categories" />
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Sidebar column -->
            <div class="space-y-6">
                <!-- Media / Thumbnail -->
                <Card class="rounded-xl border-gray-200 dark:border-gray-700">
                    <CardHeader>
                        <CardTitle>Thumbnail</CardTitle>
                        <CardDescription> Product image </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <label
                            for="thumbnail"
                            class="flex cursor-pointer flex-col items-center justify-center gap-3 rounded-xl border-2 border-dashed border-gray-300 bg-gray-50/50 px-6 py-10 transition-colors hover:border-gray-400 hover:bg-gray-100/50 dark:border-gray-600 dark:bg-gray-800/30 dark:hover:border-gray-500 dark:hover:bg-gray-800/50"
                        >
                            <ImagePlus
                                class="h-12 w-12 text-gray-400 dark:text-gray-500"
                            />
                            <div class="text-center">
                                <p
                                    class="text-sm font-medium text-gray-600 dark:text-gray-300"
                                >
                                    Drop image or click to upload
                                </p>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400"
                                >
                                    PNG, JPG up to 4MB
                                </p>
                            </div>
                            <input
                                id="thumbnail"
                                type="file"
                                accept="image/*"
                                class="sr-only"
                                @change="
                                    form.thumbnail =
                                        $event.target.files?.[0] ?? null
                                "
                            />
                        </label>
                        <InputError :message="form.errors.thumbnail" />
                    </CardContent>
                </Card>

                <!-- Stock -->
                <Card class="rounded-xl border-gray-200 dark:border-gray-700">
                    <CardHeader>
                        <CardTitle>Stock</CardTitle>
                        <CardDescription> Available quantity </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="grid gap-2">
                            <Label for="stock">Quantity</Label>
                            <Input
                                id="stock"
                                v-model="form.stock"
                                type="text"
                                placeholder="0"
                                class="w-full"
                            />
                            <InputError :message="form.errors.stock" />
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </form>
</template>
