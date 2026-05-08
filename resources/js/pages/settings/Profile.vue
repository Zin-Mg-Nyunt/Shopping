<script setup>
import { Form, Head, Link, useForm } from '@inertiajs/vue3';
import { Crown, UserRound } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import DeleteUser from '@/components/DeleteUser.vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import UserDashboardLayout from '@/layouts/UserDashboardLayout.vue';
import { logout } from '@/routes';
import { route } from 'ziggy-js';
import { toast } from 'vue-sonner';

const { user } = defineProps({
    user: Object,
});

defineOptions({
    layout: UserDashboardLayout,
});

const userInitial = computed(
    () => user.name?.trim()?.charAt(0)?.toUpperCase() ?? 'U',
);
const profilePhotoInput = ref(null);
const isEditingName = ref(false);
const isEditingEmail = ref(false);
const isPasswordDialogOpen = ref(false);
const profilePhotoUrl = computed(() =>
    user.value?.profile_photo_path
        ? `/storage/${user.value.profile_photo_path}`
        : null,
);
const nameForm = useForm({
    name: user.name ?? '',
    email: user.email ?? '',
});
const emailForm = useForm({
    name: user.name ?? '',
    email: user.email ?? '',
});
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});
const canSaveName = computed(
    () =>
        isEditingName.value &&
        nameForm.name.trim().length > 0 &&
        nameForm.name !== user.name,
);
const canSaveEmail = computed(
    () =>
        isEditingEmail.value &&
        emailForm.email.trim().length > 0 &&
        emailForm.email !== user.email,
);

function handlePhotoSelected(event, submit) {
    if (event.target.files?.length) {
        submit();
    }
}

function handleLogout() {
    sessionStorage.removeItem('addresses');
}

function startEditName() {
    isEditingName.value = true;
    nameForm.name = user.name ?? '';
    nameForm.email = user.email ?? '';
    nameForm.clearErrors();
}

function cancelEditName() {
    isEditingName.value = false;
    nameForm.name = user.name ?? '';
    nameForm.email = user.email ?? '';
    nameForm.clearErrors();
}

function saveName() {
    nameForm.patch(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            isEditingName.value = false;
            toast.success('Name updated successfully');
        },
    });
}

function startEditEmail() {
    isEditingEmail.value = true;
    emailForm.email = user.email ?? '';
    emailForm.name = user.name ?? '';
    emailForm.clearErrors();
}

function cancelEditEmail() {
    isEditingEmail.value = false;
    emailForm.email = user.email ?? '';
    emailForm.name = user.name ?? '';
    emailForm.clearErrors();
}

function saveEmail() {
    emailForm.patch(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            isEditingEmail.value = false;
            toast.success('Email updated successfully');
        },
    });
}

function closePasswordDialog() {
    isPasswordDialogOpen.value = false;
    passwordForm.reset();
    passwordForm.clearErrors();
}

function savePassword() {
    passwordForm.put(route('user-password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Password updated successfully');
            closePasswordDialog();
        },
    });
}
</script>

<template>
    <Head title="Profile settings" />

    <h1 class="sr-only">Profile settings</h1>

    <section class="mb-5 space-y-6">
        <div
            class="overflow-hidden rounded-3xl border border-border bg-card shadow-sm"
        >
            <div
                class="bg-linear-to-r from-primary/15 via-primary/8 to-transparent px-6 py-7"
            >
                <div class="flex flex-wrap items-start justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <div
                            class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-primary/15 text-2xl font-semibold text-primary ring-1 ring-primary/25"
                        >
                            {{ userInitial }}
                        </div>
                        <div class="space-y-1">
                            <h2 class="text-xl font-semibold text-foreground">
                                {{ user.name }}
                            </h2>
                            <p class="text-sm text-muted-foreground">
                                {{ user.email }}
                            </p>
                        </div>
                    </div>

                    <span
                        class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold"
                        :class="
                            user.is_loyal
                                ? 'border-amber-500/30 bg-amber-500/10 text-amber-800 dark:text-amber-300'
                                : 'border-border bg-muted/60 text-muted-foreground'
                        "
                    >
                        <Crown v-if="user.is_loyal" class="h-3.5 w-3.5" />
                        <UserRound v-else class="h-3.5 w-3.5" />
                        {{
                            user.is_loyal ? 'Loyal Customer' : 'Normal Customer'
                        }}
                    </span>
                </div>

                <div class="mt-4">
                    <Button as-child variant="outline" size="sm">
                        <Link
                            :href="logout()"
                            method="post"
                            @success="handleLogout"
                        >
                            Logout
                        </Link>
                    </Button>
                </div>
            </div>

            <div class="grid gap-4 px-6 py-6 md:grid-cols-2">
                <div class="rounded-2xl border border-border bg-background p-4">
                    <p class="text-xs font-medium text-muted-foreground">
                        Profile photo
                    </p>
                    <div class="mt-3 flex items-center gap-3">
                        <img
                            v-if="profilePhotoUrl"
                            :src="profilePhotoUrl"
                            alt="Profile photo"
                            class="h-11 w-11 rounded-xl border border-border object-cover"
                        />
                        <div
                            v-else
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary/15 text-sm font-semibold text-primary"
                        >
                            {{ userInitial }}
                        </div>
                        <p class="text-sm text-muted-foreground">
                            Upload your own profile photo.
                        </p>
                    </div>
                    <Form
                        v-bind="ProfileController.update.form()"
                        v-slot="{ errors, processing, submit }"
                        class="mt-4 space-y-2"
                    >
                        <input
                            ref="profilePhotoInput"
                            type="file"
                            name="photo"
                            accept="image/*"
                            class="hidden"
                            @change="
                                (event) => handlePhotoSelected(event, submit)
                            "
                        />
                        <input type="hidden" name="name" :value="user.name" />
                        <input type="hidden" name="email" :value="user.email" />
                        <Button
                            type="button"
                            variant="outline"
                            :disabled="processing"
                            @click="profilePhotoInput.click()"
                        >
                            Change photo
                        </Button>
                        <InputError :message="errors.photo" />
                    </Form>
                </div>

                <div class="rounded-2xl border border-border bg-background p-4">
                    <p class="text-xs font-medium text-muted-foreground">
                        Name
                    </p>
                    <p
                        v-if="!isEditingName"
                        class="mt-2 text-sm font-medium text-foreground"
                    >
                        {{ user.name }}
                    </p>

                    <div v-else class="mt-3 space-y-2">
                        <Input
                            v-model="nameForm.name"
                            type="text"
                            placeholder="Full name"
                        />
                        <InputError :message="nameForm.errors.name" />
                    </div>

                    <div class="mt-4 flex items-center gap-2">
                        <Button
                            v-if="!isEditingName"
                            variant="outline"
                            @click="startEditName"
                        >
                            Edit name
                        </Button>
                        <template v-else>
                            <Button variant="outline" @click="cancelEditName">
                                Cancel
                            </Button>
                            <Button
                                v-if="canSaveName"
                                :disabled="nameForm.processing"
                                @click="saveName"
                            >
                                Save
                            </Button>
                        </template>
                    </div>
                </div>

                <div class="rounded-2xl border border-border bg-background p-4">
                    <p class="text-xs font-medium text-muted-foreground">
                        Email address
                    </p>
                    <p
                        v-if="!isEditingEmail"
                        class="mt-2 text-sm font-medium text-foreground"
                    >
                        {{ user.email }}
                    </p>

                    <div v-else class="mt-3 space-y-2">
                        <Input
                            v-model="emailForm.email"
                            type="email"
                            placeholder="Email address"
                        />
                        <InputError :message="emailForm.errors.email" />
                    </div>

                    <div class="mt-4 flex items-center gap-2">
                        <Button
                            v-if="!isEditingEmail"
                            variant="outline"
                            @click="startEditEmail"
                        >
                            Edit email
                        </Button>
                        <template v-else>
                            <Button variant="outline" @click="cancelEditEmail">
                                Cancel
                            </Button>
                            <Button
                                v-if="canSaveEmail"
                                :disabled="emailForm.processing"
                                @click="saveEmail"
                            >
                                Save
                            </Button>
                        </template>
                    </div>
                </div>

                <div class="rounded-2xl border border-border bg-background p-4">
                    <p class="text-xs font-medium text-muted-foreground">
                        Password
                    </p>
                    <p class="mt-2 text-sm font-medium text-foreground">
                        ••••••••••
                    </p>
                    <Dialog v-model:open="isPasswordDialogOpen">
                        <DialogTrigger as-child>
                            <Button variant="outline" class="mt-4">
                                Change password
                            </Button>
                        </DialogTrigger>
                        <DialogContent>
                            <DialogHeader>
                                <DialogTitle>Change password</DialogTitle>
                                <DialogDescription>
                                    Enter your current password and set a new
                                    one.
                                </DialogDescription>
                            </DialogHeader>

                            <div class="space-y-4">
                                <div class="grid gap-2">
                                    <Label for="current_password">
                                        Current password
                                    </Label>
                                    <PasswordInput
                                        id="current_password"
                                        v-model="passwordForm.current_password"
                                        name="current_password"
                                        autocomplete="current-password"
                                        placeholder="Current password"
                                    />
                                    <InputError
                                        :message="
                                            passwordForm.errors.current_password
                                        "
                                    />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="new_password"
                                        >New password</Label
                                    >
                                    <PasswordInput
                                        id="new_password"
                                        v-model="passwordForm.password"
                                        name="password"
                                        autocomplete="new-password"
                                        placeholder="New password"
                                    />
                                    <InputError
                                        :message="passwordForm.errors.password"
                                    />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="password_confirmation">
                                        Confirm password
                                    </Label>
                                    <PasswordInput
                                        id="password_confirmation"
                                        v-model="
                                            passwordForm.password_confirmation
                                        "
                                        name="password_confirmation"
                                        autocomplete="new-password"
                                        placeholder="Confirm password"
                                    />
                                    <InputError
                                        :message="
                                            passwordForm.errors
                                                .password_confirmation
                                        "
                                    />
                                </div>
                            </div>

                            <DialogFooter class="gap-2">
                                <DialogClose as-child>
                                    <Button
                                        variant="outline"
                                        type="button"
                                        @click="closePasswordDialog"
                                    >
                                        Cancel
                                    </Button>
                                </DialogClose>
                                <Button
                                    type="button"
                                    :disabled="passwordForm.processing"
                                    @click="savePassword"
                                >
                                    Save
                                </Button>
                            </DialogFooter>
                        </DialogContent>
                    </Dialog>
                </div>
            </div>
        </div>
    </section>

    <DeleteUser />
</template>
