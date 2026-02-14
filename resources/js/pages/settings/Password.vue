<script setup>
import PasswordController from '@/actions/App/Http/Controllers/Settings/PasswordController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
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
import { Spinner } from '@/components/ui/spinner';
import AdminLayout from '@/layouts/AdminLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import UserLayout from '@/layouts/UserLayout.vue';
import { email } from '@/routes/password';
import { Form, Head, router, usePage } from '@inertiajs/vue3';
import { onBeforeUnmount, ref } from 'vue';

const page = usePage();
const user = page.props.auth.user;
const layoutComponent = user.role == 'user' ? UserLayout : AdminLayout;

const resetModalOpen = ref(false);
const submittingEmail = ref(false);

const removeFinishListener = router.on('finish', () => {
    if (submittingEmail.value) {
        resetModalOpen.value = false;
        submittingEmail.value = false;
    }
});

onBeforeUnmount(() => {
    if (typeof removeFinishListener === 'function') {
        removeFinishListener();
    }
});

function onEmailSubmit() {
    resetModalOpen.value = false;
}

defineOptions({
    layout: null,
});
</script>

<template>
    <Head title="Password settings" />
    <component :is="layoutComponent">
        <h1 class="sr-only">Password Settings</h1>

        <SettingsLayout>
            <div class="space-y-6">
                <Heading
                    variant="small"
                    title="Update password"
                    description="Ensure your account is using a long, random password to stay secure"
                />

                <Form
                    v-bind="PasswordController.update.form()"
                    :options="{
                        preserveScroll: true,
                    }"
                    reset-on-success
                    reset-on-error
                    class="space-y-6"
                    v-slot="{ errors, processing, recentlySuccessful }"
                >
                    <div class="grid gap-2">
                        <Label for="current_password"> Current password </Label>
                        <Input
                            id="current_password"
                            name="current_password"
                            type="password"
                            class="mt-1 block w-full"
                            autocomplete="current-password"
                            placeholder="Current password"
                        />
                        <InputError :message="errors.current_password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password"> New password </Label>
                        <Input
                            id="password"
                            name="password"
                            type="password"
                            class="mt-1 block w-full"
                            autocomplete="new-password"
                            placeholder="New password"
                        />
                        <InputError :message="errors.password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password_confirmation">
                            Confirm password
                        </Label>
                        <Input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            class="mt-1 block w-full"
                            autocomplete="new-password"
                            placeholder="Confirm password"
                        />
                        <InputError :message="errors.password_confirmation" />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button
                            :disabled="processing"
                            data-test="update-password-button"
                        >
                            Save password
                        </Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p
                                v-show="recentlySuccessful"
                                class="text-sm text-neutral-600"
                            >
                                Saved.
                            </p>
                        </Transition>
                    </div>

                    <p
                        class="border-t border-border pt-4 text-center text-sm text-muted-foreground"
                    >
                        Forgot password?
                        <button
                            type="button"
                            class="cursor-pointer underline underline-offset-4 transition-colors hover:text-foreground focus-visible:rounded focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:outline-none"
                            @click="resetModalOpen = true"
                        >
                            Reset password
                        </button>
                    </p>
                </Form>
            </div>
        </SettingsLayout>

        <!-- Reset password modal -->
        <Dialog v-model:open="resetModalOpen">
            <DialogContent
                class="border-border bg-background text-foreground sm:max-w-md"
            >
                <DialogHeader>
                    <DialogTitle>Reset password</DialogTitle>
                    <DialogDescription class="text-muted-foreground">
                        Enter your email address and we'll send you a link to
                        reset your password.
                    </DialogDescription>
                </DialogHeader>
                <Form
                    v-bind="email.form()"
                    class="space-y-4"
                    v-slot="{ errors, processing }"
                >
                    <div class="grid gap-2">
                        <Label for="reset-email">Email address</Label>
                        <Input
                            id="reset-email"
                            type="email"
                            name="email"
                            autocomplete="email"
                            placeholder="email@example.com"
                            class="border-input bg-background"
                        />
                        <InputError :message="errors.email" />
                    </div>
                    <DialogFooter class="gap-2">
                        <Button
                            type="button"
                            variant="outline"
                            class="border-border"
                            @click="resetModalOpen = false"
                        >
                            Cancel
                        </Button>
                        <Button
                            class="bg-primary text-primary-foreground hover:bg-primary/90"
                            :disabled="processing"
                        >
                            <Spinner
                                v-if="processing"
                                class="mr-2 size-4"
                            />
                            Send reset link
                        </Button>
                    </DialogFooter>
                </Form>
            </DialogContent>
        </Dialog>
    </component>
</template>
