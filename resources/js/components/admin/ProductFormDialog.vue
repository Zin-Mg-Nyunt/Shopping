<script setup>
import { useForm } from '@inertiajs/vue3';
import { Loader2 } from 'lucide-vue-next';
import { watch } from 'vue';
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
    open: { type: Boolean, required: true },
    mode: { type: String, default: 'add' },
    product: { type: Object, default: null },
    saving: { type: Boolean, default: false },
    categoryOptions: { type: Array, default: () => [] },
});

const emit = defineEmits(['update:open', 'submit']);

const form = useForm({
    id: null,
    title: '',
    slug: '',
    description: '',
    category: '',
    price: '',
    discount_price: '',
    stock: 0,
    thumbnail: null,
    imageFile: null,
    category_id: '',
});

function resetForm() {
    form.defaults({
        id: null,
        title: '',
        slug: '',
        description: '',
        category: '',
        price: '',
        discount_price: '',
        stock: 0,
        thumbnail: null,
        imageFile: null,
        category_id: '',
    });
    form.reset();
    form.clearErrors();
}

function hydrateFromProduct() {
    const product = props.product;

    if (!product) {
        resetForm();

        return;
    }

    form.defaults({
        id: product.id,
        title: product.title ?? '',
        slug: product.slug ?? '',
        description: product.description ?? '',
        category: product.category_name ?? '',
        price: String(product.price ?? ''),
        discount_price: product.discount_price
            ? String(product.discount_price)
            : '',
        stock: Number(product.stock) || 0,
        thumbnail: product.thumbnail ?? null,
        imageFile: null,
        category_id: '',
    });
    form.reset();
    form.clearErrors();
}

watch(
    () => [props.open, props.mode, props.product],
    () => {
        if (!props.open) {
            return;
        }

        if (props.mode === 'edit') {
            hydrateFromProduct();

            return;
        }

        resetForm();
    },
    { immediate: true },
);

function closeDialog() {
    emit('update:open', false);
}

function onEditFileChange(e) {
    const input = e.target;
    const file = input.files?.[0];
    form.imageFile = file || null;
}

function submitForm() {
    emit('submit', {
        ...form.data(),
        category: form.category,
        price: Number(form.price),
        discount_price: form.discount_price
            ? Number(form.discount_price)
            : null,
        stock: Number(form.stock),
    });
}
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent
            :show-close-button="false"
            class="flex max-h-[min(92vh,880px)] flex-col gap-0 overflow-hidden border-border p-0 sm:max-w-4xl"
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
                    Fill product fields and save.
                </DialogDescription>
            </div>

            <div
                class="flex-1 overflow-y-auto bg-[#F9FAFB] px-4 py-5 sm:px-6 dark:bg-muted/30"
            >
                <div
                    class="grid gap-4 rounded-2xl border border-border bg-card p-5 shadow-sm"
                >
                    <div class="grid gap-2">
                        <Label for="edit-name">Product name</Label>
                        <Input id="edit-name" v-model="form.title" required />
                    </div>

                    <div class="grid gap-2">
                        <Label for="edit-description">Description</Label>
                        <textarea
                            id="edit-description"
                            v-model="form.description"
                            rows="5"
                            class="w-full rounded-lg border border-border bg-background px-3 py-2.5 text-sm focus:border-[#FF6600] focus:ring-2 focus:ring-[#FF6600]/20 focus:outline-none"
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label for="edit-category">Category</Label>
                        <select
                            id="edit-category"
                            v-model="form.category"
                            class="h-10 rounded-lg border border-border bg-background px-3 text-sm focus:border-[#FF6600] focus:ring-2 focus:ring-[#FF6600]/20 focus:outline-none"
                        >
                            <option value="">Select category</option>
                            <option
                                v-for="category in categoryOptions"
                                :key="category"
                                :value="category"
                            >
                                {{ category }}
                            </option>
                        </select>
                    </div>

                    <div class="grid gap-2">
                        <Label for="edit-price">Price</Label>
                        <Input
                            id="edit-price"
                            v-model="form.price"
                            type="number"
                            step="0.01"
                            min="0"
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label for="edit-discount-price">Discount price</Label>
                        <Input
                            id="edit-discount-price"
                            v-model="form.discount_price"
                            type="number"
                            step="0.01"
                            min="0"
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label for="edit-stock-qty">Stock</Label>
                        <Input
                            id="edit-stock-qty"
                            v-model.number="form.stock"
                            type="number"
                            min="0"
                            step="1"
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label for="edit-image">Product image</Label>
                        <Input
                            id="edit-image"
                            type="file"
                            accept="image/*"
                            @change="onEditFileChange"
                        />
                    </div>
                </div>
            </div>

            <div
                class="flex shrink-0 flex-col-reverse gap-3 border-t border-border bg-card px-4 py-4 sm:flex-row sm:justify-end sm:px-6"
            >
                <Button
                    variant="ghost"
                    class="text-muted-foreground hover:text-foreground"
                    :disabled="saving"
                    @click="closeDialog"
                >
                    Cancel
                </Button>
                <Button
                    class="min-w-[160px] bg-[#FF6600] font-semibold text-white shadow-sm hover:bg-[#FF6600]/90 disabled:opacity-70"
                    :disabled="saving"
                    @click="submitForm"
                >
                    <Loader2 v-if="saving" class="mr-2 h-4 w-4 animate-spin" />
                    {{ saving ? 'Saving…' : 'Save product' }}
                </Button>
            </div>
        </DialogContent>
    </Dialog>
</template>
