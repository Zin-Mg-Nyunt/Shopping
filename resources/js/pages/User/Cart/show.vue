<script setup>
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import {
    Banknote,
    CreditCard,
    MapPin,
    Minus,
    Phone,
    Plus,
    ShoppingBag,
    ShoppingCart,
    Smartphone,
    Trash2,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';

const PAYMENT_OPTIONS = [
    { id: 'kbz_pay', label: 'KBZ Pay', icon: Smartphone },
    { id: 'wave_money', label: 'Wave Money', icon: Smartphone },
    { id: 'cb_pay', label: 'CB Pay', icon: Smartphone },
    { id: 'mpu', label: 'MPU Card', icon: CreditCard },
    { id: 'bank_transfer', label: 'Bank transfer', icon: Banknote },
    { id: 'cod', label: 'Cash on delivery', icon: Banknote },
];

const selectedPayment = ref('kbz_pay');
const { items, address } = defineProps({
    items: Array,
    address: {
        type: Object,
        default: null,
    },
});

const page = usePage();

const form = useForm({
    phone: address?.phone ?? '',
    contact_name: address?.contact_name ?? '',
    street_address: address?.street_address ?? '',
    quarter_or_village: address?.quarter_or_village ?? '',
    township: address?.township ?? '',
    state_or_region: address?.state_or_region ?? '',
    email: page.props.auth.user.email ?? '',
});
const saveAsDefaultAddress = () => {
    form.post(route('user.address.save-as-default'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            if (page.props.flash.error) toast.error(page.props.flash.error);
            else toast.success(page.props.flash.success);
        },
        onError: () => {
            toast.error('Failed to save address as default');
        },
    });
};

const isEmpty = computed(() => items.length === 0);
const totalItems = computed(() =>
    items.reduce((sum, item) => sum + item.quantity, 0),
);
const subtotal = computed(() =>
    items.reduce((sum, item) => sum + item.product.price * item.quantity, 0),
);
const shippingEstimate = computed(() => (subtotal.value >= 100 ? 0 : 9.99));
const total = computed(() => subtotal.value + shippingEstimate.value);

function formatMoney(amount) {
    if (amount == null) return '—';
    return `$${Number(amount).toFixed(2)}`;
}

function lineTotal(item) {
    return item.product.price * item.quantity;
}

function updateQuantity(item, delta) {
    const quantity = Number(item.quantity) + delta;
    if (quantity <= 0 || quantity > item.product.stock) return;
    router.post(
        route('cart.update'),
        {
            id: item.id,
            quantity,
        },
        {
            preserveScroll: true,
            preserveState: true,
        },
    );
}

function setQuantity(item, quantity) {
    updateQuantity(item, quantity - item.quantity);
}

function removeItem(id) {
    router.post(
        route('cart.remove'),
        {
            id,
        },
        {
            preserveScroll: true,
            preserveState: true,
        },
    );
}
</script>

<template>
    <Head title="Shopping Cart" />
    <div
        class="min-h-screen bg-background text-foreground dark:bg-slate-950 dark:text-slate-100"
    >
        <div class="container mx-auto px-4 py-8 sm:px-6 lg:px-8">
            <!-- Header -->
            <header class="mb-6 flex flex-col gap-1">
                <h1
                    class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl"
                >
                    Shopping Cart
                </h1>
                <p class="text-sm text-muted-foreground">
                    <template v-if="!isEmpty">
                        {{ totalItems }}
                        {{ totalItems === 1 ? 'item' : 'items' }} in your cart
                    </template>
                    <template v-else> Your cart is empty </template>
                </p>
            </header>

            <template v-if="!isEmpty">
                <div class="grid gap-8 lg:grid-cols-[1fr_360px]">
                    <!-- Cart items list -->
                    <div class="space-y-4">
                        <Card
                            v-for="item in items"
                            :key="item.id"
                            class="overflow-hidden border-border bg-card shadow-sm"
                        >
                            <CardContent class="p-0">
                                <div
                                    class="flex flex-col gap-4 p-4 sm:flex-row sm:items-center sm:gap-6"
                                >
                                    <!-- Product image -->
                                    <div
                                        class="flex size-24 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-muted sm:size-28"
                                    >
                                        <img
                                            v-if="item.product.thumbnail"
                                            :src="item.product.thumbnail"
                                            :alt="item.product.name"
                                            class="size-full object-cover"
                                        />
                                        <ShoppingBag
                                            v-else
                                            class="size-10 text-muted-foreground opacity-50"
                                            aria-hidden="true"
                                        />
                                    </div>

                                    <!-- Details + quantity + actions -->
                                    <div class="min-w-0 flex-1 space-y-3">
                                        <div
                                            class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between"
                                        >
                                            <div>
                                                <h2
                                                    class="font-semibold text-foreground"
                                                >
                                                    {{ item.product.name }}
                                                </h2>
                                                <p
                                                    class="text-lg font-semibold text-foreground"
                                                >
                                                    {{
                                                        formatMoney(
                                                            item.product.price,
                                                        )
                                                    }}
                                                </p>
                                            </div>
                                            <p
                                                class="text-lg font-semibold text-foreground sm:shrink-0"
                                            >
                                                {{
                                                    formatMoney(lineTotal(item))
                                                }}
                                            </p>
                                        </div>

                                        <div
                                            class="flex flex-wrap items-center justify-between gap-3"
                                        >
                                            <!-- Quantity controls -->
                                            <div
                                                class="flex items-center gap-1 rounded-md border border-input bg-background"
                                            >
                                                <Button
                                                    type="button"
                                                    variant="ghost"
                                                    size="icon"
                                                    :class="[
                                                        'size-9 shrink-0 rounded-r-none text-foreground hover:bg-accent',
                                                        item.quantity <= 1
                                                            ? 'cursor-not-allowed opacity-50'
                                                            : '',
                                                    ]"
                                                    aria-label="Decrease quantity"
                                                    @click="
                                                        updateQuantity(item, -1)
                                                    "
                                                >
                                                    <Minus
                                                        class="size-4"
                                                        aria-hidden="true"
                                                    />
                                                </Button>
                                                <Input
                                                    type="number"
                                                    min="1"
                                                    :model-value="item.quantity"
                                                    class="h-9 w-14 border-0 bg-transparent text-center text-sm [-moz-appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                                                    @update:model-value="
                                                        (v) =>
                                                            setQuantity(item, v)
                                                    "
                                                />
                                                <Button
                                                    type="button"
                                                    variant="ghost"
                                                    size="icon"
                                                    :class="[
                                                        'size-9 shrink-0 rounded-l-none text-foreground hover:bg-accent',
                                                        item.quantity >=
                                                        item.product.stock
                                                            ? 'cursor-not-allowed opacity-50'
                                                            : '',
                                                    ]"
                                                    aria-label="Increase quantity"
                                                    @click="
                                                        updateQuantity(item, 1)
                                                    "
                                                >
                                                    <Plus
                                                        class="size-4"
                                                        aria-hidden="true"
                                                    />
                                                </Button>
                                            </div>

                                            <Button
                                                type="button"
                                                variant="ghost"
                                                size="sm"
                                                class="text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
                                                aria-label="Remove from cart"
                                                @click="removeItem(item.id)"
                                            >
                                                <Trash2
                                                    class="mr-1.5 size-4"
                                                    aria-hidden="true"
                                                />
                                                Remove
                                            </Button>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <!-- Address & contact -->
                        <Card
                            class="overflow-hidden border-border bg-card shadow-sm"
                        >
                            <form @submit.prevent="saveAsDefaultAddress">
                                <CardHeader>
                                    <div
                                        class="flex flex-col items-start justify-between gap-4 md:flex-row md:items-center"
                                    >
                                        <div>
                                            <h2
                                                class="text-lg font-semibold text-foreground"
                                            >
                                                Shipping & contact
                                            </h2>
                                            <p
                                                class="text-sm text-muted-foreground"
                                            >
                                                Where should we send your order?
                                            </p>
                                        </div>
                                        <div>
                                            <Button
                                                type="submit"
                                                class="w-full bg-primary text-primary-foreground hover:bg-primary/90 disabled:cursor-not-allowed"
                                                :disabled="
                                                    !form.isDirty ||
                                                    form.processing
                                                "
                                            >
                                                Save as default address
                                            </Button>
                                        </div>
                                    </div>
                                </CardHeader>
                                <CardContent class="space-y-6">
                                    <div class="space-y-4">
                                        <p
                                            class="flex items-center gap-2 text-sm font-medium text-foreground"
                                        >
                                            <Phone
                                                class="size-4 shrink-0 text-muted-foreground"
                                                aria-hidden="true"
                                            />
                                            Contact
                                        </p>
                                        <div
                                            class="grid gap-4 sm:grid-cols-[1fr_1fr_auto]"
                                        >
                                            <div class="grid gap-2">
                                                <Label for="contact_name"
                                                    >Contact Name</Label
                                                >
                                                <Input
                                                    id="contact_name"
                                                    v-model="form.contact_name"
                                                    type="text"
                                                    placeholder="Contact Name"
                                                    autocomplete="contact_name"
                                                    class="border-input bg-background"
                                                />
                                                <InputError
                                                    class="mt-2"
                                                    :message="
                                                        form.errors.contact_name
                                                    "
                                                />
                                            </div>
                                            <div class="grid gap-2">
                                                <Label for="phone">Phone</Label>
                                                <Input
                                                    id="phone"
                                                    v-model="form.phone"
                                                    type="tel"
                                                    placeholder="+1 (555) 000-0000"
                                                    autocomplete="phone"
                                                    class="border-input bg-background"
                                                />
                                                <InputError
                                                    class="mt-2"
                                                    :message="form.errors.phone"
                                                />
                                            </div>
                                            <div class="grid gap-2">
                                                <Label for="email">Email</Label>
                                                <Input
                                                    id="email"
                                                    v-model="form.email"
                                                    type="email"
                                                    placeholder="you@example.com"
                                                    autocomplete="email"
                                                    class="border-input bg-background"
                                                />
                                                <InputError
                                                    class="mt-2"
                                                    :message="form.errors.email"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="space-y-4">
                                        <p
                                            class="flex items-center gap-2 text-sm font-medium text-foreground"
                                        >
                                            <MapPin
                                                class="size-4 shrink-0 text-muted-foreground"
                                                aria-hidden="true"
                                            />
                                            Address
                                        </p>
                                        <div class="grid gap-4">
                                            <div class="grid gap-2">
                                                <Label for="street_address"
                                                    >Street address</Label
                                                >
                                                <Input
                                                    id="street_address"
                                                    v-model="
                                                        form.street_address
                                                    "
                                                    type="text"
                                                    placeholder="123 Main St"
                                                    autocomplete="street_address"
                                                    class="border-input bg-background"
                                                />
                                                <InputError
                                                    class="mt-2"
                                                    :message="
                                                        form.errors
                                                            .street_address
                                                    "
                                                />
                                            </div>
                                            <div
                                                class="grid gap-4 sm:grid-cols-[1fr_1fr_auto]"
                                            >
                                                <div class="grid gap-2">
                                                    <Label
                                                        for="quarter_or_village"
                                                        >Quarter/Village</Label
                                                    >
                                                    <Input
                                                        id="quarter_or_village"
                                                        v-model="
                                                            form.quarter_or_village
                                                        "
                                                        type="text"
                                                        placeholder="Quarter"
                                                        autocomplete="quarter_or_village"
                                                        class="border-input bg-background"
                                                    />
                                                    <InputError
                                                        class="mt-2"
                                                        :message="
                                                            form.errors
                                                                .quarter_or_village
                                                        "
                                                    />
                                                </div>
                                                <div class="grid gap-2">
                                                    <Label for="township"
                                                        >Township</Label
                                                    >
                                                    <Input
                                                        id="township"
                                                        v-model="form.township"
                                                        type="text"
                                                        placeholder="Township"
                                                        autocomplete="township"
                                                        class="border-input bg-background"
                                                    />
                                                    <InputError
                                                        class="mt-2"
                                                        :message="
                                                            form.errors.township
                                                        "
                                                    />
                                                </div>
                                                <div class="grid gap-2">
                                                    <Label for="state_or_region"
                                                        >State / Region</Label
                                                    >
                                                    <Input
                                                        id="state_or_region"
                                                        v-model="
                                                            form.state_or_region
                                                        "
                                                        type="text"
                                                        placeholder="State / Region"
                                                        autocomplete="state_or_region"
                                                        class="border-input bg-background"
                                                    />
                                                    <InputError
                                                        class="mt-2"
                                                        :message="
                                                            form.errors
                                                                .state_or_region
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </CardContent>
                            </form>
                        </Card>
                    </div>

                    <!-- Right column: Payment + Order summary -->
                    <div class="space-y-6 lg:sticky lg:top-24 lg:self-start">
                        <!-- Payment method (Myanmar) -->
                        <Card class="border-border bg-card shadow-sm">
                            <CardHeader>
                                <h2
                                    class="text-lg font-semibold text-foreground"
                                >
                                    Payment method
                                </h2>
                                <p class="text-sm text-muted-foreground">
                                    Select how you want to pay
                                </p>
                            </CardHeader>
                            <CardContent>
                                <fieldset class="space-y-2">
                                    <legend class="sr-only">
                                        Payment method
                                    </legend>
                                    <label
                                        v-for="option in PAYMENT_OPTIONS"
                                        :key="option.id"
                                        @click="selectedPayment = option.id"
                                        :class="[
                                            'flex cursor-pointer items-center gap-3 rounded-lg border p-3 transition-colors',
                                            selectedPayment === option.id
                                                ? 'border-primary bg-primary/5'
                                                : 'border-border hover:border-muted-foreground/30 hover:bg-muted/50',
                                        ]"
                                    >
                                        <component
                                            :is="option.icon"
                                            class="size-5 shrink-0 text-muted-foreground"
                                            aria-hidden="true"
                                        />
                                        <span
                                            class="text-sm font-medium text-foreground"
                                        >
                                            {{ option.label }}
                                        </span>
                                    </label>
                                </fieldset>
                            </CardContent>
                        </Card>

                        <Card class="border-border bg-card shadow-sm">
                            <CardHeader>
                                <h2
                                    class="text-lg font-semibold text-foreground"
                                >
                                    Order Summary
                                </h2>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <div class="flex justify-between text-sm">
                                    <span class="text-muted-foreground"
                                        >Subtotal</span
                                    >
                                    <span class="font-medium text-foreground">
                                        {{ formatMoney(subtotal) }}
                                    </span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-muted-foreground"
                                        >Shipping</span
                                    >
                                    <span class="font-medium text-foreground">
                                        {{
                                            shippingEstimate === 0
                                                ? 'Free'
                                                : formatMoney(shippingEstimate)
                                        }}
                                    </span>
                                </div>
                                <div
                                    class="flex justify-between border-t border-border pt-4 text-base font-semibold text-foreground"
                                >
                                    <span>Total</span>
                                    <span>{{ formatMoney(total) }}</span>
                                </div>
                                <Button
                                    class="w-full bg-primary text-primary-foreground hover:bg-primary/90"
                                    as-child
                                >
                                    <Link href="#">Proceed to Checkout</Link>
                                </Button>
                                <Button
                                    variant="outline"
                                    class="w-full border-border"
                                    as-child
                                >
                                    <Link :href="route('products')">
                                        Continue Shopping
                                    </Link>
                                </Button>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </template>

            <!-- Empty state -->
            <Card
                v-else
                class="border-border bg-card py-16 text-center shadow-sm"
            >
                <CardContent class="flex flex-col items-center gap-6 px-6">
                    <div
                        class="flex size-20 items-center justify-center rounded-full bg-muted text-muted-foreground sm:size-24"
                    >
                        <ShoppingCart
                            class="size-10 sm:size-12"
                            aria-hidden="true"
                        />
                    </div>
                    <div class="space-y-1">
                        <h2 class="text-lg font-semibold text-foreground">
                            Your cart is empty
                        </h2>
                        <p class="text-sm text-muted-foreground">
                            Add items from the store to get started.
                        </p>
                    </div>
                    <Button
                        size="sm"
                        class="bg-primary text-primary-foreground hover:bg-primary/90"
                        as-child
                    >
                        <Link :href="route('products')">
                            Continue Shopping
                        </Link>
                    </Button>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
