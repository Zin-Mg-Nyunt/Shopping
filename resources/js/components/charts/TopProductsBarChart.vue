<script setup>
import { useAppearance } from '@/composables/useAppearance';
import {
    BarElement,
    CategoryScale,
    Chart as ChartJS,
    Legend,
    LinearScale,
    Title,
    Tooltip,
} from 'chart.js';
import { computed } from 'vue';
import { Bar } from 'vue-chartjs';

ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
);

const { resolvedAppearance } = useAppearance();
const isDark = computed(() => resolvedAppearance.value === 'dark');

const textColor = computed(() =>
    isDark.value ? 'rgb(209, 213, 219)' : 'rgb(75, 85, 99)',
);
const gridColor = computed(() =>
    isDark.value ? 'rgba(75, 85, 99, 0.3)' : 'rgba(229, 231, 235, 0.8)',
);

const chartData = computed(() => ({
    labels: ['Product A', 'Product B', 'Product C', 'Product D', 'Product E'],
    datasets: [
        {
            label: 'Units Sold',
            data: [65, 89, 44, 72, 58],
            backgroundColor: [
                'rgba(59, 130, 246, 0.7)',
                'rgba(16, 185, 129, 0.7)',
                'rgba(139, 92, 246, 0.7)',
                'rgba(245, 158, 11, 0.7)',
                'rgba(236, 72, 153, 0.7)',
            ],
            borderRadius: 6,
        },
    ],
}));

const chartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false,
        },
    },
    scales: {
        x: {
            grid: { display: false },
            ticks: { color: textColor.value },
        },
        y: {
            grid: { color: gridColor.value },
            ticks: { color: textColor.value },
        },
    },
}));
</script>

<template>
    <div class="h-64">
        <Bar
            :data="chartData"
            :options="chartOptions"
        />
    </div>
</template>
