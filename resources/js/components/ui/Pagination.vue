<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    pagination: {
        type: Object,
        required: true,
    },
});

const links = computed(() => props.pagination.links ?? []);

const showPagination = computed(
    () => (props.pagination.last_page ?? 1) > 1,
);

function isPreviousLink(label) {
    const plain = label.replace(/&[^;]+;/g, '').trim().toLowerCase();

    return plain.includes('previous') || label.includes('laquo');
}

function isNextLink(label) {
    const plain = label.replace(/&[^;]+;/g, '').trim().toLowerCase();

    return plain.includes('next') || label.includes('raquo');
}
</script>

<template>
    <nav
        v-if="showPagination"
        class="flex flex-wrap items-center justify-center gap-1 border-t border-border pt-8"
        aria-label="Pagination"
    >
        <ul class="flex flex-wrap items-center justify-center gap-1">
            <li v-for="(link, index) in links" :key="index">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    preserve-scroll
                    class="inline-flex min-h-10 min-w-10 items-center justify-center rounded-lg border text-sm font-medium transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary"
                    :class="
                        link.active
                            ? 'border-primary bg-primary text-primary-foreground shadow-sm'
                            : 'border-border bg-card text-foreground hover:border-primary/40 hover:bg-muted/50'
                    "
                    :aria-current="link.active ? 'page' : undefined"
                >
                    <span
                        v-if="isPreviousLink(link.label)"
                        class="inline-flex items-center gap-1 px-2"
                    >
                        <svg
                            class="h-4 w-4 shrink-0"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 18l-6-6 6-6"
                            />
                        </svg>
                        <span class="hidden sm:inline">Previous</span>
                        <span class="sr-only sm:hidden">Previous page</span>
                    </span>
                    <span
                        v-else-if="isNextLink(link.label)"
                        class="inline-flex items-center gap-1 px-2"
                    >
                        <span class="hidden sm:inline">Next</span>
                        <span class="sr-only sm:hidden">Next page</span>
                        <svg
                            class="h-4 w-4 shrink-0"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 18l6-6-6-6"
                            />
                        </svg>
                    </span>
                    <span
                        v-else
                        class="min-w-10 px-3 py-2"
                        v-html="link.label"
                    />
                </Link>
                <span
                    v-else
                    class="inline-flex min-h-10 min-w-10 items-center justify-center rounded-lg border border-transparent px-3 py-2 text-sm text-muted-foreground"
                    :aria-disabled="true"
                >
                    <span
                        v-if="isPreviousLink(link.label)"
                        class="inline-flex items-center gap-1 opacity-50"
                    >
                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 18l-6-6 6-6"
                            />
                        </svg>
                        <span class="hidden sm:inline">Previous</span>
                    </span>
                    <span
                        v-else-if="isNextLink(link.label)"
                        class="inline-flex items-center gap-1 opacity-50"
                    >
                        <span class="hidden sm:inline">Next</span>
                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 18l6-6-6-6"
                            />
                        </svg>
                    </span>
                    <span v-else v-html="link.label" />
                </span>
            </li>
        </ul>
    </nav>
</template>
