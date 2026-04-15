import { useCustomToast } from '@/composables/useCustomToast';
import { router } from '@inertiajs/vue3';

const { customToast } = useCustomToast();
export function useAddToCart() {
    function addToCart(product, quantity) {
        router.post(
            route('cart.add'),
            {
                product_id: product.id,
                quantity: quantity,
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    customToast(
                        product.thumbnail,
                        `${quantity > 1 ? ` x${quantity}` : ''} added to cart`,
                    );
                },
            },
        );
    }
    return { addToCart };
}
