import { onMounted, onUnmounted, ref } from 'vue';
import { router } from '@inertiajs/vue3';

export function useCartCount() {
    const cartCount = ref(Number(localStorage.getItem('cart_count')) || 0);
    let unregister = null;

    const updateCartCount = (newCount) => {
        cartCount.value = Number(newCount);
        localStorage.setItem('cart_count', String(newCount));
    };
    onMounted(() => {
        unregister = router.on('success', (event) => {
            if (
                event.detail.page.props.cart_count !== undefined &&
                event.detail.page.props.cart_count !== null
            ) {
                updateCartCount(event.detail.page.props.cart_count);
            }
        });
    });
    onUnmounted(() => {
        if (unregister) {
            unregister();
        }
    });
    return { cartCount };
}
