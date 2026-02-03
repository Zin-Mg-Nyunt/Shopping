<script setup>
defineProps({
    thumbnail: {
        type: String,
        default: null,
    },
    message: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <div
        class="flex items-center gap-3 rounded-lg bg-emerald-200/50 p-2 dark:bg-emerald-900/50"
    >
        <div class="size-10 shrink-0 overflow-hidden rounded-lg">
            <img
                v-if="thumbnail"
                :src="thumbnail"
                alt=""
                class="h-full w-full object-cover"
            />
            <div
                v-else
                class="flex h-full w-full items-center justify-center text-muted-foreground"
            >
                —
            </div>
        </div>
        <p
            class="min-w-0 flex-1 text-sm font-medium text-emerald-900 dark:text-emerald-100"
        >
            {{ message }}
        </p>
    </div>
</template>
