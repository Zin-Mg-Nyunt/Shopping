<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import UserDashboardLayout from '@/layouts/UserDashboardLayout.vue';
import { Card } from '@/components/ui/card';
import { PlusIcon } from 'lucide-vue-next';
import { toast } from 'vue-sonner';
import InputError from '@/components/InputError.vue';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

defineOptions({
    layout: UserDashboardLayout,
});

const { addresses } = defineProps({
    addresses: {
        type: Array,
        required: true,
    },
});

const showAddAddressForm = ref(false);
const editingAddressId = ref(null);
const removeModalOpen = ref(false);
const removingAddress = ref(null);

const form = useForm({
    label: '',
    full_name: '',
    phone: '',
    street: '',
    city: '',
    postal_code: '',
    is_default: false,
});

function addNewAddress() {
    editingAddressId.value = null;
    form.defaults({
        label: '',
        full_name: '',
        phone: '',
        street: '',
        city: '',
        postal_code: '',
        is_default: false,
    });
    form.reset();
    form.clearErrors();
    showAddAddressForm.value = true;
}

function hideAddressForm() {
    editingAddressId.value = null;
    showAddAddressForm.value = false;
    form.clearErrors();
    form.reset();
}

function editAddress(address) {
    editingAddressId.value = address.id;

    form.defaults(address);
    form.reset();

    showAddAddressForm.value = true;
}

function submitForm() {
    editingAddressId.value ? updateAddress() : createAddress();
}

function createAddress() {
    form.post(route('user.address.store'), {
        preserveScroll: true,
        onSuccess: (response) => {
            hideAddressForm();
            form.reset();
            if (response.props.flash.success) {
                toast.success(response.props.flash.success);
            }
        },
        onError: (errors) => {
            if (errors.address) {
                toast.error(errors.address);
            }
        },
    });
}

function updateAddress() {
    if (!form.isDirty) {
        toast.info('No changes to update');
        return;
    }
    form.put(route('user.address.update', editingAddressId.value), {
        preserveScroll: true,
        onSuccess: (response) => {
            form.reset();
            hideAddressForm();
            if (response.props.flash.success) {
                toast.success(response.props.flash.success);
            } else if (response.props.flash.info) {
                toast.info(response.props.flash.info);
            }
        },
        onError: (errors) => {
            if (errors.address) {
                toast.error(errors.address);
            }
        },
    });
}

function openRemoveConfirmation(address) {
    removingAddress.value = address;
    removeModalOpen.value = true;
}

function closeRemoveModal() {
    removingAddress.value = null;
    removeModalOpen.value = false;
}

function submitRemoveAddress() {
    if (!removingAddress.value) {
        return;
    }

    const id = removingAddress.value.id;

    router.delete(route('user.address.destroy', id), {
        preserveScroll: true,
        onSuccess: (response) => {
            closeRemoveModal();
            if (response.props.flash?.success) {
                toast.success(response.props.flash.success);
            } else {
                toast.success('Address removed successfully');
            }
        },
        onError: () => {
            toast.error('Could not remove this address. Please try again.');
        },
    });
}
</script>

<template>
    <Head title="Shipping address" />

    <section class="space-y-8">
        <header
            class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center"
        >
            <div class="space-y-2">
                <h1 class="text-2xl font-bold tracking-tight text-foreground">
                    Shipping address
                </h1>
                <p class="text-sm text-muted-foreground">
                    Manage your delivery addresses for faster checkout.
                </p>
            </div>

            <button
                class="inline-flex items-center gap-2 rounded-full border border-primary bg-primary px-5 py-2 text-sm font-semibold text-primary-foreground transition hover:opacity-90"
                @click="addNewAddress"
            >
                <PlusIcon class="size-4" /> Add new shipping address
            </button>
        </header>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <article
                v-for="address in addresses"
                :key="address.id"
                class="rounded-2xl border border-border bg-card p-5 shadow-sm transition hover:shadow-md"
                :class="{
                    'border-primary/25': address.id === editingAddressId,
                }"
            >
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-foreground">
                            {{ address.label ?? 'Untitled' }}
                        </p>
                        <p class="mt-1 text-sm text-muted-foreground">
                            {{ address.full_name }}
                        </p>
                    </div>
                    <span
                        v-if="address.is_default"
                        class="rounded-full border border-primary/25 bg-primary/12 px-2.5 py-1 text-xs font-semibold text-primary"
                    >
                        Default
                    </span>
                </div>

                <p class="mt-4 text-sm text-foreground">
                    {{ address.phone }}
                </p>
                <p
                    class="mt-2 mb-4 text-sm leading-relaxed text-muted-foreground"
                >
                    {{ address.street }}, {{ address.city }},
                    {{ address.postal_code }}
                </p>

                <div class="flex flex-wrap gap-2">
                    <button
                        type="button"
                        class="rounded-full border border-primary/25 bg-primary/10 px-3 py-1.5 text-xs font-semibold text-primary transition hover:bg-primary hover:text-primary-foreground"
                        @click="editAddress(address)"
                    >
                        Edit
                    </button>
                    <button
                        type="button"
                        class="rounded-full border border-border bg-card px-3 py-1.5 text-xs font-semibold text-muted-foreground transition hover:border-destructive/35 hover:text-destructive"
                        @click="openRemoveConfirmation(address)"
                    >
                        Remove
                    </button>
                </div>
            </article>
        </div>

        <Transition
            mode="out-in"
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 translate-y-1"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <Card
                v-if="showAddAddressForm"
                class="rounded-2xl border border-border bg-card p-6 shadow-sm"
            >
                <div class="mb-5">
                    <h2 class="text-lg font-semibold text-foreground">
                        {{
                            editingAddressId
                                ? 'Edit shipping address'
                                : 'Add a new shipping address'
                        }}
                    </h2>
                    <p class="mt-1 text-sm text-muted-foreground">
                        {{
                            editingAddressId
                                ? 'Edit the form below to update the shipping address.'
                                : 'Fill in the form below to add a new shipping address.'
                        }}
                    </p>
                </div>

                <form class="space-y-5">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div class="space-y-2">
                            <Label for="label"> Label </Label>
                            <input
                                id="label"
                                v-model="form.label"
                                type="text"
                                placeholder="Enter label"
                                class="mt-1 w-full rounded-xl border border-border bg-background px-3 py-2.5 text-sm text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none"
                            />
                        </div>

                        <div class="space-y-2">
                            <Label for="full-name" :required="true">
                                Full name
                            </Label>
                            <input
                                id="full-name"
                                v-model="form.full_name"
                                type="text"
                                placeholder="Enter full name"
                                class="mt-1 w-full rounded-xl border border-border bg-background px-3 py-2.5 text-sm text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none"
                            />
                            <InputError :message="form.errors.full_name" />
                        </div>

                        <div class="space-y-2">
                            <Label for="phone-number" :required="true">
                                Phone number
                            </Label>
                            <input
                                id="phone-number"
                                v-model="form.phone"
                                type="text"
                                @input="
                                    form.phone = form.phone.replace(
                                        /[^0-9]/g,
                                        '',
                                    )
                                "
                                placeholder="Enter phone number"
                                class="mt-1 w-full rounded-xl border border-border bg-background px-3 py-2.5 text-sm text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none"
                            />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="street-address" :required="true">
                            Street address
                        </Label>
                        <input
                            id="street-address"
                            v-model="form.street"
                            type="text"
                            placeholder="Building, street, and area"
                            class="mt-1 w-full rounded-xl border border-border bg-background px-3 py-2.5 text-sm text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none"
                        />
                        <InputError :message="form.errors.street" />
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div class="space-y-2">
                            <Label for="city" :required="true"> City </Label>
                            <input
                                id="city"
                                v-model="form.city"
                                type="text"
                                placeholder="Yangon"
                                class="mt-1 w-full rounded-xl border border-border bg-background px-3 py-2.5 text-sm text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none"
                            />
                            <InputError :message="form.errors.city" />
                        </div>

                        <div class="space-y-2">
                            <Label for="postal-code" :required="true">
                                Postal code
                            </Label>

                            <input
                                id="postal-code"
                                v-model="form.postal_code"
                                type="text"
                                @input="
                                    form.postal_code = form.postal_code.replace(
                                        /[^0-9]/g,
                                        '',
                                    )
                                "
                                placeholder="123456"
                                class="mt-1 w-full rounded-xl border border-border bg-background px-3 py-2.5 text-sm text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none"
                            />
                            <InputError :message="form.errors.postal_code" />
                        </div>
                    </div>

                    <label
                        class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-muted-foreground"
                    >
                        <input
                            id="is-default"
                            v-model="form.is_default"
                            type="checkbox"
                            class="h-4 w-4 rounded border-border text-primary focus:ring-primary"
                        />
                        Set as default shipping address
                    </label>

                    <div class="flex flex-wrap gap-3 pt-2">
                        <button
                            type="button"
                            class="rounded-full border border-primary bg-primary px-5 py-2 text-sm font-semibold text-primary-foreground transition hover:opacity-90"
                            @click="submitForm"
                        >
                            {{
                                editingAddressId
                                    ? 'Update address'
                                    : 'Save address'
                            }}
                        </button>
                        <button
                            type="button"
                            class="rounded-full border border-border bg-card px-5 py-2 text-sm font-semibold text-muted-foreground transition hover:border-primary/30 hover:text-foreground"
                            @click="hideAddressForm"
                        >
                            Cancel
                        </button>
                    </div>
                </form>
            </Card>
        </Transition>

        <Dialog v-model:open="removeModalOpen">
            <DialogContent class="border-destructive/20 sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Remove this address?</DialogTitle>
                    <DialogDescription>
                        This address will no longer appear at checkout. You can
                        add it again later if you need it.
                    </DialogDescription>
                </DialogHeader>
                <div
                    v-if="removingAddress"
                    class="rounded-xl border border-border bg-muted/40 p-3 text-sm text-foreground"
                >
                    <p class="font-medium">
                        {{ removingAddress.label ?? 'Untitled' }}
                    </p>
                    <p class="mt-1 text-xs text-muted-foreground">
                        {{ removingAddress.full_name }} ·
                        {{ removingAddress.phone }}
                    </p>
                    <p
                        class="mt-2 text-xs leading-relaxed text-muted-foreground"
                    >
                        {{ removingAddress.street }},
                        {{ removingAddress.city }},
                        {{ removingAddress.postal_code }}
                    </p>
                </div>
                <DialogFooter class="gap-2">
                    <Button variant="outline" @click="closeRemoveModal">
                        Cancel
                    </Button>
                    <Button
                        class="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                        @click="submitRemoveAddress"
                    >
                        Remove address
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </section>
</template>
