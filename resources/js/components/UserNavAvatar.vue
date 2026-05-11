<script setup>
import { computed } from 'vue';

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
    sizeClass: {
        type: String,
        default: 'h-9 w-9 rounded-full',
    },
    textClass: {
        type: String,
        default: 'text-[11px] font-semibold',
    },
    frameClass: {
        type: String,
        default: '',
    },
});

const photoUrl = computed(() => props.user?.profile_photo?.path ?? null);

const initials = computed(() => {
    const name = String(props.user?.name ?? 'U').trim();
    const parts = name.split(/\s+/).filter(Boolean);

    if (parts.length >= 2) {
        return (
            parts[0].charAt(0) + parts[parts.length - 1].charAt(0)
        ).toUpperCase();
    }

    return (parts[0]?.charAt(0) || 'U').toUpperCase();
});
</script>

<template>
    <span
        :class="[
            'inline-flex shrink-0 cursor-pointer items-center justify-center overflow-hidden border border-border bg-card text-muted-foreground',
            frameClass,
            sizeClass,
        ]"
    >
        <img
            v-if="photoUrl"
            :src="photoUrl"
            alt=""
            class="h-full w-full object-cover"
        />
        <span v-else :class="textClass">{{ initials }}</span>
    </span>
</template>
