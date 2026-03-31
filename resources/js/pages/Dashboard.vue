<script setup>
import { Head } from '@inertiajs/vue3';
import UserDashboardLayout from '@/layouts/UserDashboardLayout.vue';

defineOptions({
    layout: UserDashboardLayout,
});
</script>

<template>
    <Head title="Dashboard" />

    <div class="space-y-8">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-950">
                Account overview
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                Welcome back — here is a snapshot of your activity.
            </p>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <div
                class="rounded-2xl border border-gray-200/80 bg-white p-5 shadow-sm"
            >
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Total orders
                        </p>
                        <p
                            class="mt-2 text-3xl font-bold text-gray-950 tabular-nums"
                        >
                            24
                        </p>
                    </div>
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary/10 text-primary"
                    >
                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path
                                d="M9 11V6a3 3 0 0 1 6 0v5M5 9h14l-1 12H6L5 9Z"
                            />
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="rounded-2xl border border-gray-200/80 bg-white p-5 shadow-sm"
            >
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Pending shipments
                        </p>
                        <p
                            class="mt-2 text-3xl font-bold text-gray-950 tabular-nums"
                        >
                            2
                        </p>
                    </div>
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary/10 text-primary"
                    >
                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path d="M3 7h18v10H3V7Z" />
                            <path d="M3 12h18" />
                            <path d="M8 7v10" />
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="rounded-2xl border border-gray-200/80 bg-white p-5 shadow-sm"
            >
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Loyalty points
                        </p>
                        <p
                            class="mt-2 text-3xl font-bold text-gray-950 tabular-nums"
                        >
                            1,280
                        </p>
                    </div>
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary/10 text-primary"
                    >
                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path
                                d="M12 2l2.4 7.4H22l-6 4.6 2.3 7L12 17.8 5.7 21l2.3-7-6-4.6h7.6L12 2Z"
                            />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="overflow-hidden rounded-2xl border border-gray-200/80 bg-white shadow-sm"
        >
            <div class="border-b border-gray-100 px-5 py-4">
                <h2 class="text-lg font-semibold text-gray-950">
                    Recent orders
                </h2>
                <p class="text-sm text-gray-500">Your latest purchases</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[640px] text-left text-sm">
                    <thead>
                        <tr
                            class="border-b border-gray-100 bg-gray-50/80 text-gray-600"
                        >
                            <th class="px-5 py-3 font-semibold">Order ID</th>
                            <th class="px-5 py-3 font-semibold">Date</th>
                            <th class="px-5 py-3 font-semibold">Status</th>
                            <th class="px-5 py-3 text-right font-semibold">
                                Total
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr class="transition hover:bg-gray-50/80">
                            <td class="px-5 py-4 font-medium text-gray-900">
                                #ORD-10492
                            </td>
                            <td class="px-5 py-4 text-gray-600">
                                Mar 12, 2026
                            </td>
                            <td class="px-5 py-4">
                                <span
                                    class="inline-flex rounded-full bg-primary/10 px-2.5 py-0.5 text-xs font-semibold text-primary"
                                >
                                    Shipped
                                </span>
                            </td>
                            <td
                                class="px-5 py-4 text-right font-semibold text-gray-900"
                            >
                                $129.00
                            </td>
                        </tr>
                        <tr class="transition hover:bg-gray-50/80">
                            <td class="px-5 py-4 font-medium text-gray-900">
                                #ORD-10471
                            </td>
                            <td class="px-5 py-4 text-gray-600">Mar 8, 2026</td>
                            <td class="px-5 py-4">
                                <span
                                    class="inline-flex rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-800"
                                >
                                    Processing
                                </span>
                            </td>
                            <td
                                class="px-5 py-4 text-right font-semibold text-gray-900"
                            >
                                $64.50
                            </td>
                        </tr>
                        <tr class="transition hover:bg-gray-50/80">
                            <td class="px-5 py-4 font-medium text-gray-900">
                                #ORD-10455
                            </td>
                            <td class="px-5 py-4 text-gray-600">
                                Feb 28, 2026
                            </td>
                            <td class="px-5 py-4">
                                <span
                                    class="inline-flex rounded-full bg-primary/10 px-2.5 py-0.5 text-xs font-semibold text-primary"
                                >
                                    Shipped
                                </span>
                            </td>
                            <td
                                class="px-5 py-4 text-right font-semibold text-gray-900"
                            >
                                $210.00
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
