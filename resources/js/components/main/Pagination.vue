<script setup>
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps({
    meta: {
        type: Object,
        required: true,
    },
});

const currentPage = computed(() => props.meta.current_page ?? 1);
const lastPage = computed(() => props.meta.last_page ?? 1);
const links = computed(() => props.meta.links ?? []);

const prevUrl = computed(() => {
    const first = links.value[0];
    return first?.url ?? props.meta.prev_page_url ?? null;
});
const nextUrl = computed(() => {
    const last = links.value[links.value.length - 1];
    return last?.url ?? props.meta.next_page_url ?? null;
});

const isPrevDisabled = computed(() => currentPage.value <= 1);
const isNextDisabled = computed(() => currentPage.value >= lastPage.value);

/**
 * Sliding window: always show exactly 5 page numbers.
 * Current 1–5: show 1, 2, 3, 4, 5.
 * Current ≥6: show (current - 4) to current, e.g. 2,3,4,5,6 or 3,4,5,6,7.
 * If window hasn't reached last page, append "...", lastPage.
 */
const visiblePages = computed(() => {
    const cur = currentPage.value;
    const last = lastPage.value;

    if (last <= 0) return [];

    let windowStart;
    let windowEnd;

    if (last <= 5) {
        windowStart = 1;
        windowEnd = last;
    } else if (cur <= 5) {
        windowStart = 1;
        windowEnd = 5;
    } else {
        windowStart = cur - 4;
        windowEnd = cur;
    }

    const windowNumbers = Array.from(
        { length: windowEnd - windowStart + 1 },
        (_, i) => windowStart + i,
    );

    if (last - windowEnd == 1 && windowStart > 1) {
        return [1, '...', ...windowNumbers, last];
    } else if (windowEnd < last) {
        return [...windowNumbers, '...', last];
    } else if (windowEnd == last && windowStart > 1) {
        return [1, '...', ...windowNumbers];
    }

    return windowNumbers;
});

function urlForPage(page) {
    if (typeof page !== 'number') return null;
    const link = links.value.find((l) => l.label === String(page));
    return link?.url ?? null;
}
</script>

<template>
    <nav
        v-if="lastPage > 1"
        class="mt-8 flex flex-wrap items-center justify-center gap-1 sm:gap-2"
        aria-label="Pagination"
    >
        <!-- Previous -->
        <Link
            v-if="prevUrl && !isPrevDisabled"
            :href="prevUrl"
            class="inline-flex min-w-9 items-center justify-center rounded-lg border border-border bg-background px-2 py-2 text-sm font-medium text-foreground transition-colors hover:bg-muted hover:text-foreground focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:outline-none dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-800"
            preserve-scroll
        >
            <ChevronLeft
                class="size-4 sm:size-5"
                aria-hidden="true"
            />
            <span class="ml-0.5 hidden sm:inline">Previous</span>
        </Link>
        <span
            v-else
            class="inline-flex min-w-9 cursor-not-allowed items-center justify-center rounded-lg border border-border bg-muted/50 px-2 py-2 text-sm font-medium text-muted-foreground dark:border-slate-700 dark:bg-slate-800/50 dark:text-slate-500"
            aria-disabled="true"
        >
            <ChevronLeft
                class="size-4 sm:size-5"
                aria-hidden="true"
            />
            <span class="ml-0.5 hidden sm:inline">Previous</span>
        </span>

        <!-- Page numbers (sliding window of 5 + optional ... + last) -->
        <div
            class="flex flex-wrap items-center justify-center gap-1 sm:gap-1.5"
        >
            <template
                v-for="(page, i) in visiblePages"
                :key="i"
            >
                <Link
                    v-if="typeof page === 'number' && urlForPage(page)"
                    :href="urlForPage(page)"
                    :aria-current="page === currentPage ? 'page' : undefined"
                    :class="[
                        'inline-flex min-w-9 items-center justify-center rounded-lg border px-2.5 py-2 text-sm font-medium transition-colors focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:outline-none sm:min-w-10 sm:px-3',
                        page === currentPage
                            ? 'border-primary bg-primary text-white dark:border-primary dark:bg-primary dark:text-white'
                            : 'border-border bg-background text-foreground hover:bg-muted hover:text-foreground dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-800',
                    ]"
                    preserve-scroll
                >
                    {{ page }}
                </Link>
                <span
                    v-else-if="page === '...'"
                    class="inline-flex min-w-9 items-center justify-center px-1 py-2 text-sm text-muted-foreground sm:min-w-10 dark:text-slate-500"
                    aria-hidden="true"
                >
                    …
                </span>
            </template>
        </div>

        <!-- Next -->
        <Link
            v-if="nextUrl && !isNextDisabled"
            :href="nextUrl"
            class="inline-flex min-w-9 items-center justify-center rounded-lg border border-border bg-background px-2 py-2 text-sm font-medium text-foreground transition-colors hover:bg-muted hover:text-foreground focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:outline-none dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-800"
            preserve-scroll
        >
            <span class="mr-0.5 hidden sm:inline">Next</span>
            <ChevronRight
                class="size-4 sm:size-5"
                aria-hidden="true"
            />
        </Link>
        <span
            v-else
            class="inline-flex min-w-9 cursor-not-allowed items-center justify-center rounded-lg border border-border bg-muted/50 px-2 py-2 text-sm font-medium text-muted-foreground dark:border-slate-700 dark:bg-slate-800/50 dark:text-slate-500"
            aria-disabled="true"
        >
            <span class="mr-0.5 hidden sm:inline">Next</span>
            <ChevronRight
                class="size-4 sm:size-5"
                aria-hidden="true"
            />
        </span>
    </nav>
</template>
