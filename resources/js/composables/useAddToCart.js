import AddToCartToastContent from '@/components/AddToCartToastContent.vue';
import { router } from '@inertiajs/vue3';
import { markRaw } from 'vue';
import { toast } from 'vue-sonner';

export const useAddToCart = () => {
    const addToCart = (id, quantity, thumbnail) => {
        router.post(
            route('cart.add'),
            {
                product_id: id,
                quantity: quantity,
            },
            {
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => {
                    toast.custom(markRaw(AddToCartToastContent), {
                        componentProps: {
                            thumbnail: thumbnail ?? null,
                            message: `× ${quantity} added to cart.`,
                        },
                    });
                },
                onError: (error) => {
                    toast.error(error.message);
                },
            },
        );
    };
    return { addToCart };
};
