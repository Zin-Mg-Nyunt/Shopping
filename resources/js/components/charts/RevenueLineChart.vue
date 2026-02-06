<script setup>
import { useAppearance } from '@/composables/useAppearance';
import {
    CategoryScale,
    Chart as ChartJS,
    Legend,
    LinearScale,
    LineElement,
    PointElement,
    Title,
    Tooltip,
} from 'chart.js';
import { computed } from 'vue';
import { Line } from 'vue-chartjs';

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
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
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
    datasets: [
        {
            label: 'Revenue',
            data: [4200, 5100, 4800, 6200, 5800, 7500, 8200],
            borderColor: 'rgb(16, 185, 129)',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            fill: true,
            tension: 0.4,
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
            grid: { color: gridColor.value },
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
        <Line
            :data="chartData"
            :options="chartOptions"
        />
    </div>
</template>
