<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="flex min-h-svh items-center justify-center bg-background px-4 py-10 sm:px-6 md:px-8"
    >
        <div class="w-full max-w-md">
            <div class="rounded-3xl border border-border/70 bg-card/80 p-6 shadow-xl backdrop-blur sm:p-8">
                <div class="flex flex-col gap-7">
                <div class="flex flex-col items-center gap-4">
                    <Link
                        :href="home()"
                        class="flex flex-col items-center gap-2 font-medium"
                    >
                        <div
                            class="mb-1 flex h-10 w-10 items-center justify-center rounded-xl bg-primary/10 ring-1 ring-primary/20"
                        >
                            <AppLogoIcon
                                class="size-9 fill-current text-foreground dark:text-white"
                            />
                        </div>
                        <span class="sr-only">{{ title }}</span>
                    </Link>
                    <div class="space-y-2 text-center">
                        <h1 class="text-2xl font-semibold tracking-tight text-foreground">
                            {{ title }}
                        </h1>
                        <p class="text-center text-sm leading-6 text-muted-foreground">
                            {{ description }}
                        </p>
                    </div>
                </div>
                <slot />
            </div>
            </div>
        </div>
    </div>
</template>
