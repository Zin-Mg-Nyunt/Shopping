<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { ImagePlus, Loader2, Upload } from 'lucide-vue-next';
import { computed, onUnmounted, ref } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const props = defineProps({
    mode: { type: String, default: 'add' },
    product: { type: Object, default: null },
});

const emit = defineEmits(['update:open']);
const page = usePage();
const customCategories = ref([]);
const customBrands = ref([]);
const newCategoryName = ref('');
const newBrandName = ref('');
const thumbnailInput = ref(null);
const thumbnailPreview = ref(null);
const categoryOptions = computed(() => [
    ...(page.props.categories ?? []),
    ...customCategories.value,
]);
const brandOptions = computed(() => [
    ...(page.props.brands ?? []),
    ...customBrands.value,
]);

const productForm = useForm({
    id: props.product?.id ?? null,
    title: props.product?.title ?? '',
    description: props.product?.description ?? '',
    price: props.product?.price ?? 0,
    percentage: props.product?.percentage ?? 0,
    discount_price: props.product?.discount_price ?? 0,
    stock: props.product?.stock ?? 0,
    thumbnail: props.product?.thumbnail ?? null,
    categories: props.product?.categories?.map((category) => category.id) ?? [],
    new_categories: [],
    brand: props.product?.brand?.id ?? null,
    new_brand: '',
});

const discountPrice = computed({
    get: () => {
        return productForm.discount_price;
    },
    set: (val) => {
        let value = Math.round(Number(val));

        if (value < 0 || value >= productForm.price) {
            value = 0;
        }

        productForm.discount_price = value;

        if (value > 0) {
            productForm.percentage = Math.round(
                100 - 100 * (value / productForm.price),
            );
        } else {
            productForm.percentage = 0;
        }
    },
});

const percentage = computed({
    get: () => {
        return productForm.percentage;
    },
    set: (val) => {
        let value = Math.round(Number(val));

        if (value <= 0 || value > 100) {
            value = 0;
        }

        productForm.percentage = value;

        if (value > 0) {
            productForm.discount_price = Math.max(
                productForm.price - productForm.price * (value / 100),
                0,
            );
        } else {
            productForm.discount_price = 0;
        }
    },
});

function addCategoryOption() {
    const name = newCategoryName.value.trim();
    if (!name) return;

    const duplicate = categoryOptions.value.some(
        (category) => category.name.toLowerCase() === name.toLowerCase(),
    );

    if (duplicate) {
        newCategoryName.value = '';

        return;
    }
    const formattedName = name.charAt(0).toUpperCase() + name.slice(1);
    const newCategory = {
        name: formattedName,
        isNew: true,
        id: new Date().getTime(),
    };

    customCategories.value.push(newCategory);
    productForm.categories.push(newCategory.id);
    productForm.new_categories.push(newCategory.name);
    newCategoryName.value = '';
}

function selectCategory(id) {
    return productForm.categories.includes(id);
}

function toggleCategory(cat) {
    const id = cat.id;
    if (selectCategory(id)) {
        productForm.categories = productForm.categories.filter((c) => c !== id);
        if (cat.isNew) {
            productForm.new_categories = productForm.new_categories.filter(
                (c) => c !== cat.name,
            );
        }
    } else {
        productForm.categories.push(id);
        if (cat.isNew) productForm.new_categories.push(cat.name);
    }
}

function addBrandOption() {
    const name = newBrandName.value.trim();
    if (!name) return;

    const duplicate = brandOptions.value.some(
        (brand) => brand.name.toLowerCase() === name.toLowerCase(),
    );

    if (duplicate) {
        newBrandName.value = '';

        return;
    }

    const formattedName = name.charAt(0).toUpperCase() + name.slice(1);
    const newBrand = {
        name: formattedName,
        isNew: true,
        id: new Date().getTime(),
    };
    customBrands.value.push(newBrand);
    productForm.brand = newBrand.id;
    productForm.new_brand = newBrand.name;
    newBrandName.value = '';
}

function selectBrand(id) {
    return productForm.brand === id;
}

function toggleBrand(brand) {
    const id = brand.id;
    if (selectBrand(id)) {
        productForm.brand = null;
        if (brand.isNew) productForm.new_brand = '';
    } else {
        productForm.brand = id;
        if (brand.isNew) productForm.new_brand = brand.name;
    }
}

function uploadThumbnail(event) {
    const file = event.target.files[0];

    if (!file) {
        return;
    }

    if (thumbnailPreview.value) {
        URL.revokeObjectURL(thumbnailPreview.value);
    }

    thumbnailPreview.value = URL.createObjectURL(file);
    productForm.thumbnail = file;
}

function submitForm() {
    const routeName =
        props.mode === 'add' ? 'products.store' : 'products.update';

    productForm.post(routeName, {
        onSuccess: () => {
            emit('update:open', false);
        },
    });
}

onUnmounted(() => {
    if (thumbnailPreview.value) {
        URL.revokeObjectURL(thumbnailPreview.value);
    }
});
</script>

<template>
    <Dialog open @update:open="emit('update:open', $event)">
        <DialogContent
            :show-close-button="false"
            class="flex max-h-[min(92vh,900px)] flex-col gap-0 overflow-hidden border-border p-0 sm:max-w-4xl"
        >
            <div
                class="shrink-0 border-b border-border bg-card px-5 py-4 sm:px-6"
            >
                <DialogTitle class="text-left text-xl font-bold tracking-tight">
                    {{ mode === 'add' ? 'Add new product' : 'Edit product' }}
                </DialogTitle>
                <DialogDescription
                    class="text-left text-sm text-muted-foreground"
                >
                    Thumbnail, categories (multi), brand (one), and pricing.
                </DialogDescription>
            </div>

            <div
                class="flex-1 overflow-y-auto bg-muted/30 px-4 py-5 sm:px-6 dark:bg-muted/20"
            >
                <div
                    class="grid gap-6 lg:grid-cols-[minmax(0,240px)_1fr] lg:items-start"
                >
                    <!-- Thumbnail -->
                    <div class="space-y-2">
                        <Label class="text-foreground">Thumbnail</Label>
                        <input
                            ref="thumbnailInput"
                            type="file"
                            accept="image/*"
                            class="sr-only"
                            @change="uploadThumbnail"
                        />
                        <button
                            type="button"
                            class="group relative flex aspect-square w-full max-w-[240px] cursor-pointer flex-col items-center justify-center gap-2 overflow-hidden rounded-2xl border-2 border-dashed border-border bg-card text-center transition hover:border-[#FF6600]/50 hover:bg-muted/40"
                            @click="thumbnailInput.click()"
                        >
                            <img
                                v-if="productForm.thumbnail"
                                :src="thumbnailPreview || productForm.thumbnail"
                                alt=""
                                class="absolute h-full w-full object-cover"
                            />
                            <div
                                v-if="productForm.thumbnail"
                                class="absolute bg-linear-to-t from-black/50 to-transparent opacity-0 transition group-hover:opacity-100"
                            >
                                <div
                                    class="relative z-10 flex flex-col items-center gap-2 px-4"
                                >
                                    <span
                                        class="flex h-12 w-12 items-center justify-center rounded-full bg-[#FF6600]/15 text-[#FF6600]"
                                    >
                                        <Upload
                                            class="h-5 w-5"
                                            stroke-width="2"
                                        />
                                    </span>
                                    <span
                                        class="text-xs font-medium text-white"
                                    >
                                        Change image
                                    </span>
                                </div>
                            </div>

                            <template v-if="!productForm.thumbnail">
                                <ImagePlus
                                    class="h-10 w-10 text-muted-foreground/60"
                                    stroke-width="1.25"
                                />
                                <span
                                    class="px-3 text-xs font-medium text-muted-foreground"
                                >
                                    Click to upload
                                </span>
                            </template>
                        </button>
                        <!-- <div
                            v-if="form.imageFile"
                            class="flex max-w-[240px] flex-wrap gap-2"
                        >
                            <Button
                                type="button"
                                variant="outline"
                                size="sm"
                                class="text-xs"
                                @click="clearThumbnailPick"
                            >
                                Remove new file
                            </Button>
                        </div> -->
                    </div>

                    <!-- Fields -->
                    <div
                        class="grid gap-5 rounded-2xl border border-border bg-card p-5 shadow-sm"
                    >
                        <div class="grid gap-2">
                            <Label for="edit-name">Product name</Label>
                            <Input
                                id="edit-name"
                                v-model="productForm.title"
                                required
                            />
                        </div>

                        <div class="grid gap-2">
                            <Label for="edit-description">Description</Label>
                            <textarea
                                id="edit-description"
                                v-model="productForm.description"
                                rows="4"
                                class="w-full resize-y rounded-lg border border-border bg-background px-3 py-2.5 text-sm focus:border-[#FF6600] focus:ring-2 focus:ring-[#FF6600]/20 focus:outline-none"
                            />
                        </div>

                        <div class="grid gap-2">
                            <div
                                class="flex flex-wrap items-baseline justify-between gap-2"
                            >
                                <Label id="edit-categories-label"
                                    >Categories</Label
                                >
                                <span class="text-xs text-muted-foreground"
                                    >Pick one or more</span
                                >
                            </div>
                            <div
                                class="flex flex-wrap gap-2"
                                role="group"
                                aria-labelledby="edit-categories-label"
                            >
                                <button
                                    v-for="cat in categoryOptions"
                                    :key="cat.id"
                                    type="button"
                                    class="relative rounded-full border px-3 py-1.5 text-sm font-medium transition focus-visible:ring-2 focus-visible:ring-[#FF6600] focus-visible:ring-offset-2 focus-visible:outline-none"
                                    :class="
                                        selectCategory(cat.id)
                                            ? 'border-[#FF6600] bg-[#FF6600]/15 text-[#B34700] dark:text-[#FF8533]'
                                            : 'border-border bg-background text-foreground hover:border-[#FF6600]/45'
                                    "
                                    @click="toggleCategory(cat)"
                                >
                                    <span
                                        v-if="cat.isNew"
                                        class="absolute top-1 right-1 h-2 w-2 translate-x-1/2 -translate-y-1/2 rounded-full bg-green-700"
                                    />
                                    {{ cat.name }}
                                </button>
                            </div>
                            <div class="flex flex-col gap-2 sm:flex-row">
                                <Input
                                    v-model="newCategoryName"
                                    placeholder="Add new category"
                                    @keydown.enter.prevent="addCategoryOption"
                                />
                                <Button
                                    type="button"
                                    variant="outline"
                                    class="shrink-0"
                                    @click="addCategoryOption"
                                >
                                    Add category
                                </Button>
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <div
                                class="flex flex-wrap items-baseline justify-between gap-2"
                            >
                                <Label id="edit-brands-label">Brand</Label>
                                <span class="text-xs text-muted-foreground"
                                    >One only</span
                                >
                            </div>
                            <div
                                class="flex flex-wrap gap-2"
                                role="group"
                                aria-labelledby="edit-brands-label"
                            >
                                <button
                                    v-for="b in brandOptions"
                                    :key="b.id"
                                    type="button"
                                    class="relative rounded-full border px-3 py-1.5 text-sm font-medium transition focus-visible:ring-2 focus-visible:ring-[#FF6600] focus-visible:ring-offset-2 focus-visible:outline-none"
                                    :class="
                                        selectBrand(b.id)
                                            ? 'border-[#FF6600] bg-[#FF6600] text-white shadow-sm'
                                            : 'border-border bg-background text-foreground hover:border-[#FF6600]/45'
                                    "
                                    @click="toggleBrand(b)"
                                >
                                    <span
                                        v-if="b.isNew"
                                        class="absolute top-1 right-1 h-2 w-2 translate-x-1/2 -translate-y-1/2 rounded-full bg-green-700"
                                    />
                                    {{ b.name }}
                                </button>
                            </div>
                            <div class="flex flex-col gap-2 sm:flex-row">
                                <Input
                                    v-model="newBrandName"
                                    placeholder="Add new brand"
                                    @keydown.enter.prevent="addBrandOption"
                                />
                                <Button
                                    type="button"
                                    variant="outline"
                                    class="shrink-0"
                                    @click="addBrandOption"
                                >
                                    Add brand
                                </Button>
                            </div>
                        </div>

                        <div
                            class="grid gap-4 sm:grid-cols-2 sm:gap-4 lg:grid-cols-4"
                        >
                            <div class="grid gap-2">
                                <Label for="edit-price">Price</Label>
                                <Input
                                    id="edit-price"
                                    v-model="productForm.price"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                />
                            </div>
                            <div class="grid gap-2">
                                <Label for="edit-percentage">Percentage</Label>
                                <Input
                                    id="edit-percentage"
                                    v-model="percentage"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                />
                            </div>
                            <div class="grid gap-2">
                                <Label for="edit-discount-price"
                                    >Discount price</Label
                                >
                                <Input
                                    id="edit-discount-price"
                                    v-model="discountPrice"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                />
                            </div>
                            <div class="grid gap-2">
                                <Label for="edit-stock-qty">Stock</Label>
                                <Input
                                    id="edit-stock-qty"
                                    v-model.number="productForm.stock"
                                    type="number"
                                    min="0"
                                    step="1"
                                />
                            </div>
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
                    :disabled="productForm.processing"
                    @click="emit('update:open', false)"
                >
                    Cancel
                </Button>
                <Button
                    class="min-w-[160px] bg-[#FF6600] font-semibold text-white shadow-sm hover:bg-[#FF6600]/90 disabled:opacity-70"
                    :disabled="productForm.processing"
                    @click="submitForm"
                >
                    <Loader2
                        v-if="productForm.processing"
                        class="mr-2 h-4 w-4 animate-spin"
                    />
                    {{ productForm.processing ? 'Saving…' : 'Save product' }}
                </Button>
            </div>
        </DialogContent>
    </Dialog>
</template>
