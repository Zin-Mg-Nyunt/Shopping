import { router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

export function useAddToWishlist() {
    function addToWishlist(id) {
        router.post(
            route('wishlist.add', id),
            {},
            {
                preserveScroll: true,
                onSuccess: (response) => {
                    toast.success(response.props.flash.success);
                },
                onError: (response) => {
                    toast.error(response.props.flash.error);
                },
            },
        );
    }
    return { addToWishlist };
}
