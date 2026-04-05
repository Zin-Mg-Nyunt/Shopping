<script setup>
import { SidebarProvider } from '@/components/ui/sidebar';

const { variant } = defineProps({
    variant: {
        type: String,
        default: 'sidebar',
    },
});
</script>

<template>
    <div v-if="variant === 'header'" class="flex min-h-screen w-full flex-col">
        <slot />
    </div>
    <SidebarProvider v-else>
        <slot />
    </SidebarProvider>
</template>
