<script setup>
import { computed } from 'vue';
import { useProductLoyalPricing } from '@/composables/useProductLoyalPricing';

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
    variant: {
        type: String,
        default: 'card',
        validator: (v) => ['card', 'detail', 'compact'].includes(v),
    },
});

const { isLoyalCustomer } = useProductLoyalPricing();

const currency = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
});

function formatMoney(value) {
    return currency.format(Number(value));
}

const primaryClass = computed(() => {
    if (props.variant === 'detail') {
        return 'text-4xl font-bold text-primary';
    }
    if (props.variant === 'compact') {
        return 'text-sm font-semibold text-foreground';
    }

    return 'text-lg font-bold text-primary';
});

const standardPrimaryClass = computed(() => {
    if (props.variant === 'detail') {
        return 'text-4xl font-bold text-primary';
    }
    if (props.variant === 'compact') {
        return 'text-sm font-semibold text-foreground';
    }

    return 'text-lg font-bold text-primary';
});

const strikeClass = computed(() =>
    props.variant === 'detail'
        ? 'text-lg text-muted-foreground line-through'
        : 'text-sm text-muted-foreground line-through',
);

const listPriceStrikethroughClass = computed(() =>
    props.variant === 'detail'
        ? 'text-lg font-normal text-muted-foreground line-through'
        : 'text-sm font-normal text-muted-foreground line-through',
);

const badgeClass =
    'inline-flex shrink-0 items-center rounded-full border border-primary/35 bg-primary/10 px-2 py-0.5 text-[10px] font-semibold tracking-wide text-primary uppercase';
</script>

<template>
    <div>
        <template v-if="isLoyalCustomer">
            <p
                :class="[
                    primaryClass,
                    'space-x-2',
                ]"
            >
                <span>{{ formatMoney(product.loyal_price) }}</span>
                <span :class="badgeClass">Only for loyal customers</span>
            </p>
            <div class="mt-0.5 space-y-0.5 text-muted-foreground">
                <p v-if="product.discount_price != null">
                    <span :class="strikeClass">{{
                        formatMoney(product.discount_price)
                    }}</span>
                    <span class="ml-1 text-xs text-muted-foreground/80">
                        discount price
                    </span>
                </p>
                <p>
                    <span :class="strikeClass">{{ formatMoney(product.price) }}</span>
                    <span class="ml-1 text-xs text-muted-foreground/80">
                        original price
                    </span>
                </p>
            </div>
        </template>
        <template v-else>
            <p
                class="mt-1 text-sm text-muted-foreground opacity-60"
            >
                <span class="line-through">{{
                    formatMoney(product.loyal_price)
                }}</span>
                <span class="ml-2" :class="badgeClass">Only for loyal customers</span>
            </p>
            <div class="mt-1 space-y-0.5">
                <p :class="standardPrimaryClass">
                    {{ formatMoney(product.discount_price ?? product.price) }}
                    <span class="ml-1 text-xs font-medium text-muted-foreground">
                        {{
                            product.discount_price != null
                                ? 'discount price'
                                : 'original price'
                        }}
                    </span>
                </p>
                <p v-if="product.discount_price != null">
                    <span :class="listPriceStrikethroughClass">{{
                        formatMoney(product.price)
                    }}</span>
                    <span class="ml-1 text-xs text-muted-foreground/80">
                        original price
                    </span>
                </p>
            </div>
        </template>
    </div>
</template>
