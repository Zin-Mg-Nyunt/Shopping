<script setup>
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import UserLayout from '@/layouts/UserLayout.vue';
import { Head } from '@inertiajs/vue3';
import { MapPin, Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';

defineOptions({
    name: 'UserAddressBook',
    layout: UserLayout,
});

// UI-only mock addresses
const addresses = ref([
    {
        id: 1,
        label: 'Home',
        full_name: 'Alex Johnson',
        phone: '+95 9 123 456 789',
        state: 'Yangon',
        city: 'Yangon',
        postal_code: '11011',
        street: '123 Main Street, Hlaing Township',
        is_default: true,
    },
    {
        id: 2,
        label: 'Office',
        full_name: 'Alex Johnson',
        phone: '+95 9 987 654 321',
        state: 'Mandalay',
        city: 'Mandalay',
        postal_code: '05011',
        street: '456 Oak Avenue, Suite 200',
        is_default: false,
    },
]);

const modalOpen = ref(false);
const isEditing = ref(false);
const editingId = ref(null);

const form = ref({
    label: 'Home',
    full_name: '',
    phone: '',
    state: '',
    city: '',
    postal_code: '',
    street: '',
    is_default: false,
});

const stateOptions = [
    { value: '', label: 'Select state' },
    { value: 'Yangon', label: 'Yangon' },
    { value: 'Mandalay', label: 'Mandalay' },
    { value: 'Naypyidaw', label: 'Naypyidaw' },
    { value: 'Shan', label: 'Shan' },
];

const labelOptions = [
    { value: 'Home', label: 'Home' },
    { value: 'Office', label: 'Office' },
    { value: 'Other', label: 'Other' },
];

const isEmpty = computed(() => addresses.value.length === 0);

function fullAddress(addr) {
    const parts = [addr.street, addr.city, addr.state, addr.postal_code].filter(Boolean);
    return parts.join(', ');
}

function openAdd() {
    isEditing.value = false;
    editingId.value = null;
    form.value = {
        label: 'Home',
        full_name: '',
        phone: '',
        state: '',
        city: '',
        postal_code: '',
        street: '',
        is_default: false,
    };
    modalOpen.value = true;
}

function openEdit(addr) {
    isEditing.value = true;
    editingId.value = addr.id;
    form.value = {
        label: addr.label,
        full_name: addr.full_name,
        phone: addr.phone,
        state: addr.state,
        city: addr.city,
        postal_code: addr.postal_code,
        street: addr.street,
        is_default: !!addr.is_default,
    };
    modalOpen.value = true;
}

function saveAddress() {
    if (isEditing.value && editingId.value != null) {
        const idx = addresses.value.findIndex((a) => a.id === editingId.value);
        if (idx >= 0) {
            if (form.value.is_default) {
                addresses.value = addresses.value.map((a) => ({
                    ...a,
                    is_default: a.id === editingId.value,
                }));
            }
            addresses.value[idx] = { ...addresses.value[idx], ...form.value };
        }
    } else {
        if (form.value.is_default) {
            addresses.value = addresses.value.map((a) => ({ ...a, is_default: false }));
        }
        addresses.value.push({
            id: Date.now(),
            ...form.value,
        });
    }
    modalOpen.value = false;
}

function deleteAddress(id) {
    addresses.value = addresses.value.filter((a) => a.id !== id);
}
</script>

<template>
    <Head title="My Addresses" />
    <div class="space-y-6">
        <!-- Header -->
        <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h1
                class="font-serif text-2xl font-bold tracking-tight text-foreground sm:text-3xl"
            >
                My Addresses
            </h1>
            <Button
                size="sm"
                class="w-full shrink-0 sm:w-auto bg-primary text-primary-foreground hover:bg-primary/90"
                @click="openAdd"
            >
                <Plus class="mr-2 size-4" aria-hidden="true" />
                Add New Address
            </Button>
        </header>

        <!-- Address Grid -->
        <div
            v-if="!isEmpty"
            class="grid gap-4 sm:grid-cols-2"
        >
            <Card
                v-for="addr in addresses"
                :key="addr.id"
                class="border-border bg-card shadow-sm transition-shadow hover:shadow-md"
            >
                <CardContent class="flex flex-col p-5">
                    <div class="mb-3 flex flex-wrap items-center gap-2">
                        <span class="font-semibold capitalize text-foreground">
                            {{ addr.label }}
                        </span>
                        <Badge
                            v-if="addr.is_default"
                            variant="secondary"
                            class="text-xs"
                        >
                            Default
                        </Badge>
                    </div>
                    <p class="font-medium text-foreground">
                        {{ addr.full_name }}
                    </p>
                    <p class="text-sm text-muted-foreground">
                        {{ addr.phone }}
                    </p>
                    <p class="mt-2 whitespace-pre-line text-sm text-foreground">
                        {{ fullAddress(addr) }}
                    </p>
                    <div class="mt-4 flex gap-2 border-t border-border pt-4">
                        <button
                            type="button"
                            class="flex items-center gap-1.5 rounded-md px-2 py-1.5 text-sm font-medium text-foreground transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none"
                            aria-label="Edit address"
                            @click="openEdit(addr)"
                        >
                            <Pencil class="size-4" aria-hidden="true" />
                            Edit
                        </button>
                        <button
                            type="button"
                            class="flex items-center gap-1.5 rounded-md px-2 py-1.5 text-sm font-medium text-destructive transition-colors hover:bg-destructive/10 focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none"
                            aria-label="Delete address"
                            @click="deleteAddress(addr.id)"
                        >
                            <Trash2 class="size-4" aria-hidden="true" />
                            Delete
                        </button>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Empty State -->
        <Card
            v-else
            class="border-border bg-card py-16 text-center shadow-sm"
        >
            <CardContent class="flex flex-col items-center gap-6 px-6">
                <div
                    class="flex size-20 items-center justify-center rounded-full bg-muted text-muted-foreground sm:size-24"
                >
                    <MapPin class="size-10 sm:size-12" aria-hidden="true" />
                </div>
                <div class="space-y-1">
                    <h2 class="text-lg font-semibold text-foreground">
                        No addresses yet
                    </h2>
                    <p class="text-sm text-muted-foreground">
                        Add your first address to use at checkout.
                    </p>
                </div>
                <Button
                    size="sm"
                    class="bg-primary text-primary-foreground hover:bg-primary/90"
                    @click="openAdd"
                >
                    <Plus class="mr-2 size-4" aria-hidden="true" />
                    Add New Address
                </Button>
            </CardContent>
        </Card>

        <!-- Add / Edit Modal -->
        <Dialog v-model:open="modalOpen">
            <DialogContent
                class="border-border bg-background text-foreground sm:max-w-md"
                :aria-describedby="undefined"
            >
                <DialogHeader>
                    <DialogTitle>
                        {{ isEditing ? 'Edit Address' : 'Add New Address' }}
                    </DialogTitle>
                    <DialogDescription class="text-muted-foreground">
                        {{ isEditing ? 'Update your address details.' : 'Fill in the form to save a new address.' }}
                    </DialogDescription>
                </DialogHeader>
                <form
                    class="grid gap-4 py-2"
                    @submit.prevent="saveAddress"
                >
                    <div class="grid gap-2">
                        <Label for="address-label">Label</Label>
                        <select
                            id="address-label"
                            v-model="form.label"
                            class="h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm text-foreground shadow-xs outline-none focus-visible:border-ring focus-visible:ring-2 focus-visible:ring-ring/50"
                        >
                            <option
                                v-for="opt in labelOptions"
                                :key="opt.value"
                                :value="opt.value"
                            >
                                {{ opt.label }}
                            </option>
                        </select>
                    </div>
                    <div class="grid gap-2">
                        <Label for="address-name">Full Name</Label>
                        <Input
                            id="address-name"
                            v-model="form.full_name"
                            type="text"
                            placeholder="John Doe"
                            class="border-input bg-background"
                        />
                    </div>
                    <div class="grid gap-2">
                        <Label for="address-phone">Phone</Label>
                        <Input
                            id="address-phone"
                            v-model="form.phone"
                            type="tel"
                            placeholder="+95 9 123 456 789"
                            class="border-input bg-background"
                        />
                    </div>
                    <div class="grid gap-2">
                        <Label for="address-state">State</Label>
                        <select
                            id="address-state"
                            v-model="form.state"
                            class="h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm text-foreground shadow-xs outline-none focus-visible:border-ring focus-visible:ring-2 focus-visible:ring-ring/50"
                        >
                            <option
                                v-for="opt in stateOptions"
                                :key="opt.value"
                                :value="opt.value"
                            >
                                {{ opt.label }}
                            </option>
                        </select>
                    </div>
                    <div class="grid gap-2">
                        <Label for="address-city">City</Label>
                        <Input
                            id="address-city"
                            v-model="form.city"
                            type="text"
                            placeholder="City"
                            class="border-input bg-background"
                        />
                    </div>
                    <div class="grid gap-2">
                        <Label for="address-postal">Postal Code</Label>
                        <Input
                            id="address-postal"
                            v-model="form.postal_code"
                            type="text"
                            placeholder="Postal code"
                            class="border-input bg-background"
                        />
                    </div>
                    <div class="grid gap-2">
                        <Label for="address-street">Street Address</Label>
                        <textarea
                            id="address-street"
                            v-model="form.street"
                            rows="3"
                            placeholder="Street, building, floor..."
                            class="min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm text-foreground shadow-xs placeholder:text-muted-foreground outline-none focus-visible:border-ring focus-visible:ring-2 focus-visible:ring-ring/50"
                        />
                    </div>
                    <div class="flex items-center gap-2">
                        <Checkbox
                            id="address-default"
                            v-model:checked="form.is_default"
                        />
                        <Label
                            for="address-default"
                            class="cursor-pointer text-sm font-normal text-foreground"
                        >
                            Set as default address
                        </Label>
                    </div>
                    <DialogFooter class="gap-2 sm:gap-0">
                        <Button
                            type="button"
                            variant="outline"
                            class="border-border"
                            @click="modalOpen = false"
                        >
                            Cancel
                        </Button>
                        <Button
                            type="submit"
                            class="bg-primary text-primary-foreground hover:bg-primary/90"
                        >
                            {{ isEditing ? 'Save Changes' : 'Add Address' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </div>
</template>
