import { toast } from 'vue-sonner';
import { markRaw } from 'vue';
import CustomAddToCart from '@/components/ui/CustomAddToCart.vue';

export const useCustomToast = () => {
    const customToast = (thumbnail, message) => {
        toast.success(markRaw(CustomAddToCart), {
            componentProps: {
                thumbnail,
                message,
            },
        });
    };
    return { customToast };
};
