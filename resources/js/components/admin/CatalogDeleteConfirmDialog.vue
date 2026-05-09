<script setup>
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

const open = defineModel({ type: Boolean, default: false });

const props = defineProps({
    kind: {
        type: String,
        required: true,
        validator: (value) => ['category', 'brand', 'promo'].includes(value),
    },
    target: { type: Object, default: null },
    processing: { type: Boolean, default: false },
});

const emit = defineEmits(['confirm']);

const title = computed(() => {
    switch (props.kind) {
        case 'category':
            return 'Delete category?';
        case 'brand':
            return 'Delete brand?';
        case 'promo':
            return 'Delete promo code?';
        default:
            return 'Delete?';
    }
});

const description = computed(() => {
    if (!props.target) {
        return '';
    }

    const label =
        props.kind === 'promo' ? props.target.code : props.target.name;

    return `This will permanently remove “${label}” from the catalog. This action cannot be undone.`;
});

function closeDialog() {
    open.value = false;
}

function onConfirm() {
    emit('confirm');
}
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent class="border-destructive/20 sm:max-w-md">
            <DialogHeader>
                <DialogTitle>{{ title }}</DialogTitle>
                <DialogDescription>
                    {{ description }}
                </DialogDescription>
            </DialogHeader>
            <DialogFooter class="gap-2">
                <Button variant="outline" type="button" @click="closeDialog">
                    Cancel
                </Button>
                <Button
                    type="button"
                    class="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                    :disabled="processing"
                    @click="onConfirm"
                >
                    Confirm delete
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
