<script setup>
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import {
    Database,
    Download,
    Eye,
    EyeOff,
    Globe,
    Mail,
    ShoppingBag,
} from 'lucide-vue-next';
import { ref } from 'vue';

defineOptions({
    layout: AdminLayout,
});

// UI-only state (no backend)
const showPassword = ref(false);
const autoBackupEnabled = ref(false);
const maintenanceMode = ref(false);
const logoPreview = ref(null);
const selectedCurrency = ref('USD');

const lastBackupAgo = '2 hours ago';
const currencies = [
    { value: 'USD', label: 'USD' },
    { value: 'MMK', label: 'MMK' },
    { value: 'THB', label: 'THB' },
];

function onLogoChange(event) {
    const file = event.target?.files?.[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (e) => {
        logoPreview.value = e.target?.result ?? null;
    };
    reader.readAsDataURL(file);
}

function removeLogo() {
    logoPreview.value = null;
}
</script>

<template>
    <Head title="System Settings - Admin" />
    <div class="space-y-6 font-[Inter,sans-serif]">
        <!-- Page Header -->
        <div>
            <h1
                class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white"
            >
                System Settings
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Configure system settings and maintenance options.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- 1. General Identity -->
            <Card
                class="border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900/50"
            >
                <CardHeader>
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary"
                        >
                            <Globe
                                class="h-5 w-5"
                                aria-hidden="true"
                            />
                        </div>
                        <div>
                            <CardTitle
                                class="text-lg text-gray-900 dark:text-white"
                            >
                                General Identity
                            </CardTitle>
                            <CardDescription
                                class="text-gray-500 dark:text-gray-400"
                            >
                                Site name, logo, and contact details
                            </CardDescription>
                        </div>
                    </div>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div class="grid gap-2">
                        <Label class="text-gray-700 dark:text-gray-300">
                            Site Logo
                        </Label>
                        <div
                            class="flex flex-col items-start gap-4 sm:flex-row sm:items-center"
                        >
                            <label
                                class="flex h-24 w-24 cursor-pointer flex-col items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 transition-colors hover:border-primary hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:hover:border-primary dark:hover:bg-gray-700/50"
                            >
                                <template v-if="logoPreview">
                                    <img
                                        :src="logoPreview"
                                        alt="Site logo preview"
                                        class="h-full w-full object-cover"
                                    />
                                </template>
                                <template v-else>
                                    <Upload
                                        class="h-8 w-8 text-gray-400 dark:text-gray-500"
                                        aria-hidden="true"
                                    />
                                    <span
                                        class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        Upload
                                    </span>
                                </template>
                                <input
                                    type="file"
                                    accept="image/*"
                                    class="sr-only"
                                    @change="onLogoChange"
                                />
                            </label>
                            <Button
                                v-if="logoPreview"
                                type="button"
                                variant="outline"
                                size="sm"
                                class="text-gray-700 dark:text-gray-200"
                                @click="removeLogo"
                            >
                                Remove logo
                            </Button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label
                                for="site-name"
                                class="text-gray-700 dark:text-gray-300"
                            >
                                Site Name
                            </Label>
                            <Input
                                id="site-name"
                                type="text"
                                placeholder="e.g. My Awesome Store"
                                class="border-gray-300 bg-white text-gray-900 placeholder:text-gray-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:placeholder:text-gray-400"
                            />
                        </div>
                        <div class="grid gap-2">
                            <Label
                                for="support-email"
                                class="text-gray-700 dark:text-gray-300"
                            >
                                Support Email
                            </Label>
                            <Input
                                id="support-email"
                                type="email"
                                class="border-gray-300 bg-white text-gray-900 placeholder:text-gray-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:placeholder:text-gray-400"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label
                                for="contact-phone"
                                class="text-gray-700 dark:text-gray-300"
                            >
                                Contact Phone
                            </Label>
                            <Input
                                id="contact-phone"
                                type="text"
                                class="border-gray-300 bg-white text-gray-900 placeholder:text-gray-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:placeholder:text-gray-400"
                            />
                        </div>
                    </div>
                    <div class="space-y-4">
                        <Label class="text-gray-700 dark:text-gray-300">
                            Social Links
                        </Label>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div
                                class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50/50 px-3 py-2 dark:border-gray-800 dark:bg-gray-800/50"
                            >
                                <Facebook
                                    class="h-5 w-5 shrink-0 text-gray-500 dark:text-gray-400"
                                    aria-hidden="true"
                                />
                                <Input
                                    type="url"
                                    placeholder="Facebook URL"
                                    class="flex-1 border-0 bg-transparent p-0 text-gray-900 shadow-none placeholder:text-gray-500 focus-visible:ring-0 dark:bg-transparent dark:text-gray-200 dark:placeholder:text-gray-400"
                                />
                            </div>
                            <div
                                class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50/50 px-3 py-2 dark:border-gray-800 dark:bg-gray-800/50"
                            >
                                <Instagram
                                    class="h-5 w-5 shrink-0 text-gray-500 dark:text-gray-400"
                                    aria-hidden="true"
                                />
                                <Input
                                    type="url"
                                    placeholder="Instagram URL"
                                    class="flex-1 border-0 bg-transparent p-0 text-gray-900 shadow-none placeholder:text-gray-500 focus-visible:ring-0 dark:bg-transparent dark:text-gray-200 dark:placeholder:text-gray-400"
                                />
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- 2. Store Configuration -->
            <Card
                class="border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900/50"
            >
                <CardHeader>
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary"
                        >
                            <ShoppingBag
                                class="h-5 w-5"
                                aria-hidden="true"
                            />
                        </div>
                        <div>
                            <CardTitle
                                class="text-lg text-gray-900 dark:text-white"
                            >
                                Store Configuration
                            </CardTitle>
                            <CardDescription
                                class="text-gray-500 dark:text-gray-400"
                            >
                                Currency, tax, and order settings
                            </CardDescription>
                        </div>
                    </div>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label
                                for="currency"
                                class="text-gray-700 dark:text-gray-300"
                            >
                                Currency
                            </Label>
                            <select
                                id="currency"
                                v-model="selectedCurrency"
                                class="h-9 w-full rounded-md border border-gray-300 bg-white px-3 py-1 text-sm text-gray-900 shadow-xs transition-[color,box-shadow] outline-none focus:border-primary focus:ring-1 focus:ring-primary dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
                            >
                                <option
                                    v-for="c in currencies"
                                    :key="c.value"
                                    :value="c.value"
                                >
                                    {{ c.label }}
                                </option>
                            </select>
                        </div>
                        <div class="grid gap-2">
                            <Label
                                for="tax-percent"
                                class="text-gray-700 dark:text-gray-300"
                            >
                                Tax Percentage (%)
                            </Label>
                            <Input
                                id="tax-percent"
                                type="number"
                                min="0"
                                step="0.01"
                                placeholder="0"
                                class="border-gray-300 bg-white text-gray-900 placeholder:text-gray-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:placeholder:text-gray-400"
                            />
                        </div>
                        <div class="grid gap-2">
                            <Label
                                for="min-order"
                                class="text-gray-700 dark:text-gray-300"
                            >
                                Minimum Order Amount
                            </Label>
                            <div class="relative">
                                <span
                                    class="absolute top-1/2 left-3 -translate-y-1/2 text-sm text-gray-500 dark:text-gray-400"
                                >
                                    {{ selectedCurrency }}
                                </span>
                                <Input
                                    id="min-order"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    placeholder="0.00"
                                    class="border-gray-300 bg-white pl-14 text-gray-900 placeholder:text-gray-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:placeholder:text-gray-400"
                                />
                            </div>
                        </div>
                        <div class="grid gap-2">
                            <Label
                                for="order-prefix"
                                class="text-gray-700 dark:text-gray-300"
                            >
                                Order ID Prefix
                            </Label>
                            <Input
                                id="order-prefix"
                                type="text"
                                placeholder="e.g. ORD-"
                                class="border-gray-300 bg-white text-gray-900 placeholder:text-gray-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:placeholder:text-gray-400"
                            />
                        </div>
                    </div>
                    <div
                        class="flex flex-col gap-3 rounded-lg border border-gray-200 bg-gray-50/50 px-4 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800 dark:bg-gray-800/50"
                    >
                        <div class="min-w-0 flex-1">
                            <p
                                class="font-medium text-gray-900 dark:text-white"
                            >
                                Maintenance Mode
                            </p>
                            <p
                                class="mt-0.5 text-sm text-gray-500 dark:text-gray-400"
                            >
                                When enabled, customers see a coming soon page
                            </p>
                        </div>
                        <button
                            type="button"
                            role="switch"
                            :aria-checked="maintenanceMode"
                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:focus-visible:ring-offset-gray-900"
                            :class="
                                maintenanceMode
                                    ? 'bg-primary'
                                    : 'bg-gray-200 dark:bg-gray-700'
                            "
                            @click="maintenanceMode = !maintenanceMode"
                        >
                            <span
                                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition-transform"
                                :class="
                                    maintenanceMode
                                        ? 'translate-x-5'
                                        : 'translate-x-0.5'
                                "
                            />
                        </button>
                    </div>
                </CardContent>
            </Card>

            <!-- 3. Email Configuration (SMTP) -->
            <Card
                class="border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900/50"
            >
                <CardHeader>
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary"
                        >
                            <Mail
                                class="h-5 w-5"
                                aria-hidden="true"
                            />
                        </div>
                        <div>
                            <CardTitle
                                class="text-lg text-gray-900 dark:text-white"
                            >
                                Email Settings
                            </CardTitle>
                            <CardDescription
                                class="text-gray-500 dark:text-gray-400"
                            >
                                SMTP configuration for outgoing mail
                            </CardDescription>
                        </div>
                    </div>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid gap-2">
                        <Label
                            for="smtp-host"
                            class="text-gray-700 dark:text-gray-300"
                        >
                            SMTP Host
                        </Label>
                        <Input
                            id="smtp-host"
                            type="text"
                            placeholder="e.g. smtp.mailtrap.io"
                            class="border-gray-300 bg-white text-gray-900 placeholder:text-gray-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:placeholder:text-gray-400"
                        />
                    </div>
                    <div class="grid gap-2">
                        <Label
                            for="smtp-port"
                            class="text-gray-700 dark:text-gray-300"
                        >
                            SMTP Port
                        </Label>
                        <Input
                            id="smtp-port"
                            type="text"
                            placeholder="e.g. 2525"
                            class="border-gray-300 bg-white text-gray-900 placeholder:text-gray-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:placeholder:text-gray-400"
                        />
                    </div>
                    <div class="grid gap-2">
                        <Label
                            for="smtp-username"
                            class="text-gray-700 dark:text-gray-300"
                        >
                            Username
                        </Label>
                        <Input
                            id="smtp-username"
                            type="text"
                            class="border-gray-300 bg-white text-gray-900 placeholder:text-gray-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:placeholder:text-gray-400"
                        />
                    </div>
                    <div class="grid gap-2">
                        <Label
                            for="smtp-password"
                            class="text-gray-700 dark:text-gray-300"
                        >
                            Password
                        </Label>
                        <div class="relative">
                            <Input
                                id="smtp-password"
                                :type="showPassword ? 'text' : 'password'"
                                class="border-gray-300 bg-white pr-10 text-gray-900 placeholder:text-gray-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:placeholder:text-gray-400"
                            />
                            <button
                                type="button"
                                class="absolute top-1/2 right-3 -translate-y-1/2 rounded p-1 text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200"
                                :aria-label="
                                    showPassword
                                        ? 'Hide password'
                                        : 'Show password'
                                "
                                @click="showPassword = !showPassword"
                            >
                                <EyeOff
                                    v-if="showPassword"
                                    class="h-4 w-4"
                                    aria-hidden="true"
                                />
                                <Eye
                                    v-else
                                    class="h-4 w-4"
                                    aria-hidden="true"
                                />
                            </button>
                        </div>
                    </div>
                    <div class="grid gap-2">
                        <Label
                            for="sender-email"
                            class="text-gray-700 dark:text-gray-300"
                        >
                            Sender Email
                        </Label>
                        <Input
                            id="sender-email"
                            type="email"
                            class="border-gray-300 bg-white text-gray-900 placeholder:text-gray-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:placeholder:text-gray-400"
                        />
                    </div>
                    <div class="grid gap-2">
                        <Label
                            for="sender-name"
                            class="text-gray-700 dark:text-gray-300"
                        >
                            Sender Name
                        </Label>
                        <Input
                            id="sender-name"
                            type="text"
                            class="border-gray-300 bg-white text-gray-900 placeholder:text-gray-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:placeholder:text-gray-400"
                        />
                    </div>
                </CardContent>
                <CardFooter class="flex flex-wrap gap-3">
                    <Button
                        variant="outline"
                        class="text-gray-700 dark:text-gray-200"
                    >
                        Test Connection
                    </Button>
                    <Button
                        class="bg-primary text-primary-foreground hover:bg-primary/90"
                    >
                        Save SMTP Settings
                    </Button>
                </CardFooter>
            </Card>

            <!-- 4. Database Backup & Maintenance -->
            <Card
                class="border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900/50"
            >
                <CardHeader>
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary"
                        >
                            <Database
                                class="h-5 w-5"
                                aria-hidden="true"
                            />
                        </div>
                        <div>
                            <CardTitle
                                class="text-lg text-gray-900 dark:text-white"
                            >
                                Database Backup
                            </CardTitle>
                            <CardDescription
                                class="text-gray-500 dark:text-gray-400"
                            >
                                Backups and maintenance
                            </CardDescription>
                        </div>
                    </div>
                </CardHeader>
                <CardContent class="space-y-6">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Create a full backup of your database and storage files.
                    </p>
                    <div>
                        <p
                            class="text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Last Backup Date
                        </p>
                        <p
                            class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                        >
                            Last backup: {{ lastBackupAgo }}
                        </p>
                    </div>
                    <Button
                        size="lg"
                        class="w-full gap-2 bg-primary text-primary-foreground hover:bg-primary/90"
                    >
                        <Download
                            class="h-5 w-5"
                            aria-hidden="true"
                        />
                        Generate Backup Now
                    </Button>
                    <div
                        class="flex items-center justify-between rounded-lg border border-gray-200 bg-gray-50/50 px-4 py-3 dark:border-gray-800 dark:bg-gray-800/50"
                    >
                        <Label
                            for="auto-backup"
                            class="cursor-pointer text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Enable Weekly Auto-Backups
                        </Label>
                        <button
                            id="auto-backup"
                            type="button"
                            role="switch"
                            :aria-checked="autoBackupEnabled"
                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:focus-visible:ring-offset-gray-900"
                            :class="
                                autoBackupEnabled
                                    ? 'bg-primary'
                                    : 'bg-gray-200 dark:bg-gray-700'
                            "
                            @click="autoBackupEnabled = !autoBackupEnabled"
                        >
                            <span
                                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition-transform"
                                :class="
                                    autoBackupEnabled
                                        ? 'translate-x-5'
                                        : 'translate-x-0.5'
                                "
                            />
                        </button>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Save All Changes (sticky at bottom of section) -->
        <div
            class="sticky bottom-0 z-10 -mx-2 flex flex-col gap-3 rounded-xl border-t border-gray-200 bg-white/95 px-4 py-4 backdrop-blur supports-backdrop-filter:bg-white/80 sm:flex-row sm:items-center sm:justify-end dark:border-gray-800 dark:bg-gray-900/95 dark:supports-backdrop-filter:bg-gray-900/80"
        >
            <Button
                size="lg"
                class="w-full gap-2 bg-primary text-primary-foreground hover:bg-primary/90 sm:w-auto"
            >
                Save All Changes
            </Button>
        </div>
    </div>
</template>
