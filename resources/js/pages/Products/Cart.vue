<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import {
    MapPin,
    Minus,
    Phone,
    Plus,
    ShoppingBag,
    Trash2,
} from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { route } from 'ziggy-js';
import { toast } from 'vue-sonner';
import { debounce } from 'lodash-es';

const props = defineProps({
    items: {
        type: Array,
        required: true,
    },
    addresses: {
        type: Array,
        required: true,
    },
    promos: {
        type: Array,
        required: true,
    },
});
const lines = ref([]);
watch(
    () => props.items,
    () => {
        lines.value = props.items.map((item) => ({
            ...item,
            quantity: item.pivot?.quantity ?? item.quantity,
            price: item.discount_price ?? item.price,
            brand: item.brand?.name,
        }));
    },
    {
        immediate: true,
        deep: true,
    },
);

const promoInput = ref('');
const appliedPromo = ref(null);
const promoFeedback = ref('idle');

const isEmpty = computed(() => lines.value.length === 0);

const currency = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
});

function formatMoney(value) {
    return currency.format(value);
}

const subtotal = computed(() =>
    lines.value.reduce((sum, line) => sum + line.price * line.quantity, 0),
);

const discountAmount = computed(() => {
    if (appliedPromo.value !== null) {
        let promo = appliedPromo.value;

        if (promo?.discount_type === 'percentage') {
            promoFeedback.value = 'success';
            return Math.round((subtotal.value * promo?.discount) / 100);
        } else if (
            promo?.discount_type === 'fixed' &&
            subtotal.value > 5 * promo?.discount
        ) {
            promoFeedback.value = 'success';
            return promo?.discount;
        } else {
            promoFeedback.value = 'idle';
            toast.error('You must spend at least $500 to use this promo code.');
            appliedPromo.value = null;
            return 0;
        }
    }
    return 0;
});

const afterDiscount = computed(() =>
    Math.max(0, subtotal.value - discountAmount.value),
);

const shipping = computed(() => (afterDiscount.value >= 200 ? 0 : 9.99));

const estimatedTax = computed(() => Math.round(afterDiscount.value * 5) / 100);

const orderTotal = computed(
    () => afterDiscount.value + shipping.value + estimatedTax.value,
);

const form = useForm({
    product_id: null,
    quantity: null,
});
const cartUpdate = debounce((line) => {
    form.product_id = line.id;
    form.quantity = line.quantity;

    form.post(route('cart.update'), {
        preserveScroll: true,
        preserveState: true,
        onError: (response) => {
            toast.error(response.error);
        },
    });
}, 500);

function increment(line) {
    if (line && line.quantity < line.stock) {
        line.quantity += 1;
        cartUpdate(line);
    }
}

function decrement(line) {
    if (line && line.quantity > 1) {
        line.quantity -= 1;
        cartUpdate(line);
    }
}

function removeLine(id) {
    router.delete(route('cart.destroy', { id }), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            toast.success('Item removed from cart');
        },
    });
}

watch(promoInput, () => {
    if (promoInput.value.trim().length === 0) {
        appliedPromo.value = null;
        promoFeedback.value = 'idle';
    }
});

function applyPromo() {
    const code = promoInput.value.trim().toUpperCase();

    if (!code) {
        promoFeedback.value = 'idle';
        appliedPromo.value = null;

        return;
    }

    const promo = props.promos.find((promo) => promo.code === code);

    if (promo) {
        appliedPromo.value = promo;
    } else {
        appliedPromo.value = null;
        promoFeedback.value = 'invalid';
    }
}

function lineSubtotal(line) {
    return line.price * line.quantity;
}

const addressUi = ref('compact');

const sessionAddresses = ref(
    JSON.parse(sessionStorage.getItem('addresses')) || [],
);

let allAddresses = computed(() => [
    ...props.addresses,
    ...sessionAddresses.value,
]);

const selectedAddressId = ref(
    allAddresses.value.find((a) => a.is_default)?.id ??
        allAddresses.value[0]?.id ??
        null,
);

const selectedAddress = computed(() =>
    allAddresses.value.find((a) => a.id === selectedAddressId.value),
);

const newAddress = ref({
    full_name: '',
    phone: '',
    street: '',
    city: '',
    postal_code: '',
    oneTimeUse: true,
});

const selected = ref(null);

function openAddressPicker() {
    selected.value = selectedAddressId.value;
    addressUi.value = 'list';
}

function closeAddressPicker() {
    selected.value = null;
    addressUi.value = 'compact';
}

function useAddress() {
    selectedAddressId.value = selected.value;
    addressUi.value = 'compact';
}

function saveNewAddress() {
    const n = newAddress.value;

    if (!n.full_name.trim() || !n.street.trim() || !n.city.trim()) {
        return;
    }
    n.id = 'temp-' + crypto.randomUUID();
    sessionAddresses.value.push(n);
    sessionStorage.setItem('addresses', JSON.stringify(sessionAddresses.value));
    selectedAddressId.value = n.id;
    addressUi.value = 'compact';
}

const paymentMethod = ref('bank');

const selectedPaymentDescription = computed(() =>
    paymentMethod.value === 'bank'
        ? 'Complete payment with bank transfer. We will process your order after confirmation.'
        : 'Pay with cash when your order arrives at your delivery address.',
);

const addressData = computed(() => {
    return {
        id: selectedAddress.value.oneTimeUse ? null : selectedAddress.value.id,
        full_name: selectedAddress.value.full_name,
        phone: selectedAddress.value.phone,
        street: selectedAddress.value.street,
        city: selectedAddress.value.city,
        postal_code: selectedAddress.value.postal_code,
    };
});

function handleCheckout() {
    if (!paymentMethod.value || !selectedAddress.value) {
        toast.error('Please select a payment method and shipping address');
        return;
    }
    const { id, ...addressDataWithoutId } = addressData.value;
    const orderData = {
        address_id: addressData.value.id,
        shipping_address: addressDataWithoutId,
        payment_method: paymentMethod.value,
        total_amount: orderTotal.value,
        product_ids: lines.value.map((line) => line.id),
        quantity: lines.value.map((line) => line.quantity),
        promo_code: appliedPromo.value?.code,
    };

    router.post(route('order.store'), orderData, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: (response) => {
            sessionStorage.removeItem('addresses');
            if (response.props.flash.success) {
                toast.success(response.props.flash.success);
            }
            if (response.props.flash.error) {
                toast.error(response.props.flash.error);
            }
        },
        onError: () => {
            toast.error('Order failed to place');
        },
    });
}
</script>

<template>
    <Head title="Shopping Cart" />

    <main class="bg-background">
        <div class="mx-auto w-full max-w-7xl px-6 py-10 lg:px-8">
            <!-- Empty state -->
            <div
                v-if="isEmpty"
                class="mx-auto flex max-w-lg flex-col items-center text-center"
            >
                <div
                    class="mb-8 flex h-48 w-full max-w-sm items-center justify-center rounded-3xl border border-dashed border-border bg-card px-8 py-10 shadow-sm"
                    aria-hidden="true"
                >
                    <svg
                        class="h-full max-h-36 w-full text-primary/90"
                        viewBox="0 0 320 240"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <rect
                            x="48"
                            y="72"
                            width="200"
                            height="120"
                            rx="16"
                            stroke="currentColor"
                            stroke-width="2.5"
                            class="opacity-40"
                        />
                        <path
                            d="M88 112h144M88 136h96"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            class="opacity-30"
                        />
                        <circle
                            cx="108"
                            cy="188"
                            r="10"
                            stroke="currentColor"
                            stroke-width="2"
                        />
                        <circle
                            cx="212"
                            cy="188"
                            r="10"
                            stroke="currentColor"
                            stroke-width="2"
                        />
                        <path
                            d="M72 72h176l-16-40H88L72 72Z"
                            stroke="currentColor"
                            stroke-width="2.5"
                            stroke-linejoin="round"
                            fill="currentColor"
                            fill-opacity="0.08"
                        />
                        <circle
                            cx="248"
                            cy="56"
                            r="28"
                            fill="currentColor"
                            fill-opacity="0.08"
                            stroke="currentColor"
                            stroke-width="2"
                        />
                        <path
                            d="M248 44v24M236 56h24"
                            stroke="currentColor"
                            stroke-width="2.5"
                            stroke-linecap="round"
                        />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-foreground">
                    Your cart is empty
                </h1>
                <p class="mt-1 max-w-sm text-sm text-muted-foreground">
                    Looks like you have not added anything yet. Browse our
                    catalog and find something you love.
                </p>
                <Link
                    :href="route('products.list')"
                    class="mt-8 inline-flex items-center justify-center rounded-full bg-primary px-8 py-3 text-sm font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90"
                >
                    Continue shopping
                </Link>
            </div>

            <!-- Cart with items -->
            <div v-else>
                <div
                    class="mb-8 flex flex-col gap-1 border-b border-border pb-6"
                >
                    <h1
                        class="text-2xl font-bold tracking-tight text-foreground"
                    >
                        Shopping cart
                    </h1>
                    <p class="text-sm text-muted-foreground">
                        Review your items and proceed to checkout when ready.
                    </p>
                </div>

                <div
                    class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_380px] lg:items-start"
                >
                    <!-- Line items -->
                    <div class="space-y-4">
                        <article
                            v-for="line in lines"
                            :key="line.id"
                            class="flex gap-4 rounded-2xl border border-border bg-card p-4 shadow-sm transition hover:shadow-md sm:gap-6 sm:p-5"
                        >
                            <div class="relative shrink-0">
                                <img
                                    v-if="line.thumbnail"
                                    :src="line.thumbnail"
                                    :alt="line.title"
                                    class="h-24 w-24 rounded-xl object-cover sm:h-28 sm:w-28"
                                />
                                <div
                                    v-else
                                    class="flex h-24 w-24 items-center justify-center rounded-xl bg-muted sm:h-28 sm:w-28"
                                >
                                    <ShoppingBag
                                        class="h-10 w-10 text-muted-foreground/50"
                                        stroke-width="1.5"
                                    />
                                </div>
                            </div>

                            <div class="mt-0.5 min-w-0 flex-1">
                                <p
                                    class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
                                >
                                    {{ line.brand }}
                                </p>
                                <h2
                                    class="mt-0.5 font-semibold text-foreground"
                                >
                                    {{ line.title }}
                                </h2>
                                <p class="mt-1 text-sm text-muted-foreground">
                                    {{
                                        formatMoney(
                                            line.discount_price ?? line.price,
                                        )
                                    }}
                                    each
                                </p>

                                <div
                                    class="mt-4 flex flex-wrap items-center gap-4"
                                >
                                    <div
                                        class="inline-flex items-center rounded-xl border border-border bg-muted/50 p-0.5"
                                    >
                                        <button
                                            type="button"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg text-primary transition hover:bg-primary/10 disabled:cursor-not-allowed disabled:opacity-40"
                                            :disabled="
                                                line.quantity <= 1 ||
                                                form.processing
                                            "
                                            aria-label="Decrease quantity"
                                            @click="decrement(line)"
                                        >
                                            <Minus class="h-4 w-4" />
                                        </button>
                                        <span
                                            class="min-w-8 text-center text-sm font-semibold text-foreground tabular-nums"
                                        >
                                            {{ line.quantity }}
                                        </span>
                                        <button
                                            type="button"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg text-primary transition hover:bg-primary/10 disabled:cursor-not-allowed disabled:opacity-40"
                                            :disabled="
                                                line.quantity >= line.stock ||
                                                form.processing
                                            "
                                            aria-label="Increase quantity"
                                            @click="increment(line)"
                                        >
                                            <Plus class="h-4 w-4" />
                                        </button>
                                    </div>

                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-1.5 rounded-lg px-2 py-1.5 text-sm font-medium text-muted-foreground transition hover:bg-destructive/10 hover:text-destructive"
                                        aria-label="Remove item"
                                        @click="removeLine(line.id)"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                        <span class="hidden sm:inline"
                                            >Remove</span
                                        >
                                    </button>
                                </div>
                            </div>

                            <div
                                class="flex shrink-0 flex-col items-end justify-between self-stretch sm:min-w-22"
                            >
                                <p
                                    class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
                                >
                                    Subtotal
                                </p>
                                <p
                                    class="text-lg font-bold text-primary tabular-nums"
                                >
                                    {{ formatMoney(lineSubtotal(line)) }}
                                </p>
                            </div>
                        </article>

                        <!-- Shipping address -->
                        <section
                            class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm"
                            aria-labelledby="shipping-heading"
                        >
                            <div
                                class="flex items-start justify-between gap-4 border-b border-border px-5 py-4"
                            >
                                <div class="flex items-center gap-2.5">
                                    <span
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary"
                                    >
                                        <MapPin
                                            class="h-5 w-5"
                                            stroke-width="2"
                                            aria-hidden="true"
                                        />
                                    </span>
                                    <div>
                                        <h2
                                            id="shipping-heading"
                                            class="text-base font-semibold text-foreground"
                                        >
                                            Shipping address
                                        </h2>
                                        <p
                                            class="text-xs text-muted-foreground"
                                        >
                                            Where should we deliver your order?
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-5">
                                <Transition
                                    mode="out-in"
                                    enter-active-class="transition duration-300 ease-out"
                                    enter-from-class="opacity-0 translate-y-1"
                                    enter-to-class="opacity-100 translate-y-0"
                                    leave-active-class="transition duration-200 ease-in"
                                    leave-from-class="opacity-100"
                                    leave-to-class="opacity-0"
                                >
                                    <!-- Default / compact: selected card -->
                                    <div
                                        v-if="addressUi === 'compact'"
                                        key="compact"
                                        class="space-y-4"
                                    >
                                        <div
                                            v-if="selectedAddress"
                                            class="relative rounded-xl border-2 border-primary bg-primary/3 p-4 dark:bg-primary/5"
                                        >
                                            <div
                                                class="absolute top-3 right-3 flex items-center gap-2"
                                            >
                                                <span
                                                    v-if="
                                                        selectedAddress.is_default
                                                    "
                                                    class="rounded-full bg-primary/15 px-2.5 py-0.5 text-[10px] font-bold tracking-wide text-primary uppercase"
                                                >
                                                    Default
                                                </span>
                                            </div>
                                            <ul
                                                class="space-y-1 pr-16 text-sm text-foreground"
                                            >
                                                <li
                                                    class="text-sm font-semibold text-foreground"
                                                >
                                                    {{
                                                        selectedAddress.label ??
                                                        'Untitled'
                                                    }}
                                                </li>
                                                <li
                                                    class="text-sm text-muted-foreground"
                                                >
                                                    {{
                                                        selectedAddress.full_name
                                                    }}
                                                </li>
                                                <li
                                                    class="flex items-center gap-2 text-muted-foreground"
                                                >
                                                    <Phone
                                                        class="h-3.5 w-3.5 shrink-0 text-primary/70"
                                                        aria-hidden="true"
                                                    />
                                                    {{ selectedAddress.phone }}
                                                </li>
                                                <li
                                                    class="text-muted-foreground"
                                                >
                                                    {{ selectedAddress.street }}
                                                </li>
                                                <li
                                                    class="text-muted-foreground"
                                                >
                                                    {{ selectedAddress.city }},
                                                    {{
                                                        selectedAddress.postal_code
                                                    }}
                                                </li>
                                            </ul>
                                        </div>

                                        <button
                                            type="button"
                                            class="text-sm font-semibold text-primary transition hover:text-primary/80 hover:underline"
                                            @click="openAddressPicker"
                                        >
                                            Change address
                                        </button>
                                    </div>

                                    <!-- Saved addresses (radio list) -->
                                    <div
                                        v-else-if="addressUi === 'list'"
                                        key="list"
                                        class="space-y-4"
                                    >
                                        <p
                                            class="text-sm text-muted-foreground"
                                        >
                                            Select a saved address or add a new
                                            one.
                                        </p>
                                        <ul class="space-y-3">
                                            <li
                                                v-for="addr in allAddresses"
                                                :key="addr.id"
                                            >
                                                <button
                                                    type="button"
                                                    class="flex w-full cursor-pointer rounded-xl border-2 p-4 text-left transition"
                                                    :class="
                                                        selected === addr.id
                                                            ? 'border-primary bg-primary/4 ring-2 ring-primary/15 dark:bg-primary/10'
                                                            : 'border-border bg-muted/30 hover:border-primary/40'
                                                    "
                                                    @click="selected = addr.id"
                                                >
                                                    <span
                                                        class="mt-0.5 mr-3 flex h-5 w-5 shrink-0 items-center justify-center rounded-full border-2 transition"
                                                        :class="
                                                            selected === addr.id
                                                                ? 'border-primary bg-primary'
                                                                : 'border-muted-foreground/40 bg-card'
                                                        "
                                                        aria-hidden="true"
                                                    >
                                                        <span
                                                            v-if="
                                                                selected ===
                                                                addr.id
                                                            "
                                                            class="h-2 w-2 rounded-full bg-primary-foreground"
                                                        />
                                                    </span>
                                                    <span
                                                        class="min-w-0 flex-1"
                                                    >
                                                        <span
                                                            class="flex items-center gap-2"
                                                        >
                                                            <div
                                                                class="flex flex-col gap-1"
                                                            >
                                                                <span
                                                                    class="text-sm font-semibold text-foreground"
                                                                >
                                                                    {{
                                                                        addr.label ??
                                                                        'Untitled'
                                                                    }}
                                                                </span>

                                                                <span
                                                                    class="text-sm text-muted-foreground"
                                                                >
                                                                    {{
                                                                        addr.full_name
                                                                    }}
                                                                </span>
                                                            </div>

                                                            <span
                                                                v-if="
                                                                    addr.is_default
                                                                "
                                                                class="rounded-full bg-primary/15 px-2 py-0.5 text-[10px] font-bold text-primary uppercase"
                                                            >
                                                                Default
                                                            </span>
                                                        </span>
                                                        <span
                                                            class="mt-1 block text-sm text-muted-foreground"
                                                            >{{
                                                                addr.street
                                                            }}</span
                                                        >
                                                        <span
                                                            class="block text-sm text-muted-foreground"
                                                            >{{ addr.city }},
                                                            {{
                                                                addr.postal_code
                                                            }}</span
                                                        >
                                                    </span>
                                                </button>
                                            </li>
                                        </ul>

                                        <div
                                            class="flex flex-col gap-3 border-t border-border pt-4 sm:flex-row sm:items-center sm:justify-between"
                                        >
                                            <button
                                                type="button"
                                                class="inline-flex items-center justify-center rounded-xl border-2 border-dashed border-primary/40 px-4 py-3 text-sm font-semibold text-primary transition hover:bg-primary/10"
                                                @click="addressUi = 'form'"
                                            >
                                                + Add new address
                                            </button>
                                            <div class="flex flex-wrap gap-2">
                                                <button
                                                    type="button"
                                                    class="rounded-xl border border-border px-4 py-2.5 text-sm font-medium text-muted-foreground transition hover:bg-muted"
                                                    @click="closeAddressPicker"
                                                >
                                                    Cancel
                                                </button>
                                                <button
                                                    type="button"
                                                    class="rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90"
                                                    @click="useAddress"
                                                >
                                                    Use this address
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- New address form -->
                                    <div v-else key="form" class="space-y-4">
                                        <button
                                            type="button"
                                            class="inline-flex items-center gap-1 text-sm font-medium text-primary transition hover:underline"
                                            @click="addressUi = 'list'"
                                        >
                                            ← Back to saved addresses
                                        </button>
                                        <div class="grid gap-4 sm:grid-cols-2">
                                            <div class="sm:col-span-2">
                                                <label
                                                    for="ship-name"
                                                    class="mb-1.5 block text-xs font-medium text-muted-foreground"
                                                    >Full name</label
                                                >
                                                <input
                                                    id="ship-name"
                                                    v-model="
                                                        newAddress.full_name
                                                    "
                                                    type="text"
                                                    autocomplete="name"
                                                    class="w-full rounded-xl border border-border bg-background px-3 py-2.5 text-sm text-foreground placeholder:text-muted-foreground focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none"
                                                    placeholder="Jane Doe"
                                                />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    for="ship-phone"
                                                    class="mb-1.5 block text-xs font-medium text-muted-foreground"
                                                    >Phone number</label
                                                >
                                                <input
                                                    id="ship-phone"
                                                    v-model="newAddress.phone"
                                                    type="tel"
                                                    autocomplete="tel"
                                                    class="w-full rounded-xl border border-border bg-background px-3 py-2.5 text-sm text-foreground placeholder:text-muted-foreground focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none"
                                                    placeholder="+1 (555) 000-0000"
                                                />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label
                                                    for="ship-street"
                                                    class="mb-1.5 block text-xs font-medium text-muted-foreground"
                                                    >Street address</label
                                                >
                                                <input
                                                    id="ship-street"
                                                    v-model="newAddress.street"
                                                    type="text"
                                                    autocomplete="street-address"
                                                    class="w-full rounded-xl border border-border bg-background px-3 py-2.5 text-sm text-foreground placeholder:text-muted-foreground focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none"
                                                    placeholder="123 Main St, Apt 4"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    for="ship-city"
                                                    class="mb-1.5 block text-xs font-medium text-muted-foreground"
                                                    >City</label
                                                >
                                                <input
                                                    id="ship-city"
                                                    v-model="newAddress.city"
                                                    type="text"
                                                    autocomplete="address-level2"
                                                    class="w-full rounded-xl border border-border bg-background px-3 py-2.5 text-sm text-foreground placeholder:text-muted-foreground focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none"
                                                    placeholder="San Francisco"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    for="ship-postal"
                                                    class="mb-1.5 block text-xs font-medium text-muted-foreground"
                                                    >Postal code</label
                                                >
                                                <input
                                                    id="ship-postal"
                                                    v-model="
                                                        newAddress.postal_code
                                                    "
                                                    type="text"
                                                    autocomplete="postal-code"
                                                    class="w-full rounded-xl border border-border bg-background px-3 py-2.5 text-sm text-foreground placeholder:text-muted-foreground focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none"
                                                    placeholder="94102"
                                                />
                                            </div>
                                        </div>
                                        <button
                                            type="button"
                                            class="w-full rounded-xl bg-primary py-3 text-sm font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90"
                                            @click="saveNewAddress"
                                        >
                                            Save &amp; use this address
                                        </button>
                                    </div>
                                </Transition>
                            </div>
                        </section>
                    </div>

                    <!-- Order summary -->
                    <aside class="lg:sticky lg:top-24 lg:self-start">
                        <div
                            class="rounded-2xl border border-border bg-card p-6 shadow-sm"
                        >
                            <h2 class="text-lg font-semibold text-foreground">
                                Order summary
                            </h2>

                            <dl
                                class="mt-6 space-y-3 border-b border-border pb-6 text-sm"
                            >
                                <div class="flex justify-between gap-4">
                                    <dt class="text-muted-foreground">
                                        Subtotal
                                    </dt>
                                    <dd
                                        class="font-medium text-foreground tabular-nums"
                                    >
                                        {{ formatMoney(subtotal) }}
                                    </dd>
                                </div>
                                <div
                                    v-if="discountAmount > 0"
                                    class="flex justify-between gap-4"
                                >
                                    <dt class="text-muted-foreground">
                                        Discount ({{ appliedPromo?.code }})
                                    </dt>
                                    <dd
                                        class="font-medium text-primary tabular-nums"
                                    >
                                        −{{ formatMoney(discountAmount) }}
                                    </dd>
                                </div>
                                <div class="flex justify-between gap-4">
                                    <dt class="text-muted-foreground">
                                        Shipping
                                    </dt>
                                    <dd
                                        class="font-medium text-foreground tabular-nums"
                                    >
                                        {{
                                            shipping === 0
                                                ? 'Free'
                                                : formatMoney(shipping)
                                        }}
                                    </dd>
                                </div>
                                <div class="flex justify-between gap-4">
                                    <dt class="text-muted-foreground">
                                        Estimated tax (5%)
                                    </dt>
                                    <dd
                                        class="font-medium text-foreground tabular-nums"
                                    >
                                        {{ formatMoney(estimatedTax) }}
                                    </dd>
                                </div>
                            </dl>

                            <div
                                class="flex items-baseline justify-between gap-4 pt-2"
                            >
                                <span
                                    class="text-base font-semibold text-foreground"
                                    >Total</span
                                >
                                <span
                                    class="text-2xl font-bold text-primary tabular-nums"
                                >
                                    {{ formatMoney(orderTotal) }}
                                </span>
                            </div>

                            <section
                                class="mt-6"
                                aria-labelledby="payment-heading"
                            >
                                <h3
                                    id="payment-heading"
                                    class="text-sm font-semibold text-foreground"
                                >
                                    Payment method
                                </h3>
                                <p class="mt-1 text-xs text-muted-foreground">
                                    Choose your preferred payment option.
                                </p>

                                <div class="mt-3 space-y-2.5">
                                    <label
                                        for="payment-bank"
                                        class="flex cursor-pointer items-start gap-3 rounded-xl border p-3 text-sm transition"
                                        :class="
                                            paymentMethod === 'bank'
                                                ? 'border-primary bg-primary/5 ring-1 ring-primary/20'
                                                : 'border-border bg-background hover:border-primary/50'
                                        "
                                    >
                                        <input
                                            id="payment-bank"
                                            v-model="paymentMethod"
                                            type="radio"
                                            value="bank"
                                            name="payment-method"
                                            class="mt-0.5 h-4 w-4 text-primary focus:ring-primary"
                                        />
                                        <span class="min-w-0">
                                            <span
                                                class="block font-medium text-foreground"
                                                >Bank Payment</span
                                            >
                                            <span
                                                class="mt-0.5 block text-xs text-muted-foreground"
                                                >Transfer to our bank account
                                                after placing your order.</span
                                            >
                                        </span>
                                    </label>

                                    <label
                                        for="payment-cod"
                                        class="flex cursor-pointer items-start gap-3 rounded-xl border p-3 text-sm transition"
                                        :class="
                                            paymentMethod === 'cod'
                                                ? 'border-primary bg-primary/5 ring-1 ring-primary/20'
                                                : 'border-border bg-background hover:border-primary/50'
                                        "
                                    >
                                        <input
                                            id="payment-cod"
                                            v-model="paymentMethod"
                                            type="radio"
                                            value="cod"
                                            name="payment-method"
                                            class="mt-0.5 h-4 w-4 text-primary focus:ring-primary"
                                        />
                                        <span class="min-w-0">
                                            <span
                                                class="block font-medium text-foreground"
                                                >Cash on Delivery (COD)</span
                                            >
                                            <span
                                                class="mt-0.5 block text-xs text-muted-foreground"
                                                >Pay in cash to the courier when
                                                your package is delivered.</span
                                            >
                                        </span>
                                    </label>
                                </div>

                                <p class="mt-3 text-xs text-muted-foreground">
                                    {{ selectedPaymentDescription }}
                                </p>
                            </section>

                            <div class="mt-6 space-y-2">
                                <label
                                    for="promo-code"
                                    class="text-xs font-medium text-muted-foreground"
                                >
                                    Promo code
                                </label>
                                <div class="flex gap-2">
                                    <input
                                        id="promo-code"
                                        v-model="promoInput"
                                        type="text"
                                        placeholder="Enter code"
                                        autocomplete="off"
                                        class="min-w-0 flex-1 rounded-xl border border-border bg-background px-3 py-2.5 text-sm text-foreground placeholder:text-muted-foreground focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none"
                                        @keydown.enter.prevent="applyPromo"
                                    />
                                    <button
                                        type="button"
                                        class="shrink-0 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90"
                                        @click="applyPromo"
                                    >
                                        Apply
                                    </button>
                                </div>
                                <p
                                    v-if="promoFeedback === 'success'"
                                    class="text-xs font-medium text-primary"
                                >
                                    Promo applied {{ appliedPromo?.discount }} %
                                    off your subtotal.
                                </p>
                                <p
                                    v-else-if="promoFeedback === 'invalid'"
                                    class="text-xs text-destructive"
                                >
                                    Invalid code. Try another one.
                                </p>
                            </div>

                            <button
                                type="button"
                                class="mt-6 w-full rounded-xl bg-primary py-3.5 text-base font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90"
                                @click="handleCheckout"
                            >
                                Proceed to checkout
                            </button>
                            <p
                                class="mt-3 text-center text-xs text-muted-foreground"
                            >
                                Secure checkout · Free returns within 30 days
                            </p>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </main>
</template>
