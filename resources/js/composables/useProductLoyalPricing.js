import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export function useProductLoyalPricing() {
    const page = usePage();

    const isLoyalCustomer = computed(
        () => page.props.auth?.user?.is_loyal === true,
    );

    /**
     * Matches server-side checkout: loyal customers pay loyal_price when set.
     */
    function lineUnitPrice(line) {
        if (isLoyalCustomer.value && line.loyal_price) {
            return Number(line.loyal_price);
        }

        return Number(line.discount_price ?? line.price);
    }

    return { isLoyalCustomer, lineUnitPrice };
}
