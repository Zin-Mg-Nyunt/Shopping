<script setup>
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { router } from '@inertiajs/vue3';
import { AlertTriangle } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

const props = defineProps({
    deleteItem: Object,
});
const emit = defineEmits(['closeDeleteDialog']);

function closeDeleteDialog() {
    emit('closeDeleteDialog');
}

function confirmDelete() {
    if (!props.deleteItem) return;
    const product = props.deleteItem;
    closeDeleteDialog();
    router.delete(route('admin.products.delete', product.slug), {
        onSuccess: () => {
            toast.success('Product deleted successfully');
        },
        onError: () => {
            toast.error('Failed to delete product');
        },
    });
}
</script>
<template>
    <Dialog
        :open="true"
        @update:open="closeDeleteDialog"
    >
        <DialogContent
            :show-close-button="false"
            class="max-w-md border-gray-200 bg-white shadow-xl sm:rounded-xl dark:border-gray-700 dark:bg-gray-900"
        >
            <DialogHeader class="sm:text-left">
                <div
                    class="mx-auto flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 dark:bg-red-900/30"
                >
                    <AlertTriangle
                        class="h-6 w-6 text-red-600 dark:text-red-400"
                        aria-hidden="true"
                    />
                </div>
                <DialogTitle
                    class="text-lg font-semibold text-gray-900 dark:text-white"
                >
                    Delete product
                </DialogTitle>
                <DialogDescription
                    class="text-sm text-gray-600 dark:text-gray-400"
                >
                    Are you sure you want to delete
                    <strong
                        v-if="props.deleteItem"
                        class="font-medium text-gray-900 dark:text-white"
                    >
                        {{ props.deleteItem.name }} </strong
                    >? This action cannot be undone.
                </DialogDescription>
            </DialogHeader>
            <DialogFooter
                class="flex flex-row gap-2 border-t border-gray-100 pt-4 sm:justify-end dark:border-gray-800"
            >
                <Button
                    variant="outline"
                    class="min-w-24"
                    @click="closeDeleteDialog"
                >
                    Cancel
                </Button>
                <Button
                    variant="destructive"
                    class="min-w-24"
                    @click="confirmDelete"
                >
                    <Trash2 class="mr-2 h-4 w-4" />
                    Delete
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<style></style>
