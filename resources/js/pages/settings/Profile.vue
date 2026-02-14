<script setup>
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import DeleteUser from '@/components/DeleteUser.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { getInitials } from '@/composables/useInitials';
import AdminLayout from '@/layouts/AdminLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import UserLayout from '@/layouts/UserLayout.vue';
import { send } from '@/routes/verification';
import { Form, Head, Link, usePage } from '@inertiajs/vue3';
import { Camera } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});

const page = usePage();
const user = page.props.auth.user;
const layoutComponent = user.role == 'user' ? UserLayout : AdminLayout;
const avatarInputRef = ref(null);

function triggerAvatarChange() {
    avatarInputRef.value?.click();
}

function onAvatarChange(event) {
    const file = event.target.files?.[0];
    if (file) {
        // TODO: upload avatar (e.g. Form with enctype, or separate endpoint)
    }
    event.target.value = '';
}

defineOptions({
    layout: null,
});
</script>

<template>
    <Head title="Profile settings" />
    <component :is="layoutComponent">
        <h1 class="sr-only">Profile Settings</h1>

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <Heading
                    variant="small"
                    title="Profile information"
                    description="Update your name and email address"
                />

                <Form
                    v-bind="ProfileController.update.form()"
                    class="space-y-6"
                    v-slot="{ errors, processing, recentlySuccessful }"
                >
                    <div
                        class="flex flex-col items-start gap-4 sm:flex-row sm:items-center"
                    >
                        <div class="relative shrink-0">
                            <Avatar
                                class="size-20 border-2 border-border sm:size-24"
                            >
                                <AvatarImage
                                    v-if="user.avatar"
                                    :src="user.avatar"
                                    :alt="user.name"
                                />
                                <AvatarFallback
                                    class="bg-muted text-lg font-medium text-foreground"
                                >
                                    {{ getInitials(user.name) }}
                                </AvatarFallback>
                            </Avatar>
                            <input
                                ref="avatarInputRef"
                                type="file"
                                accept="image/*"
                                class="sr-only"
                                aria-label="Change avatar"
                                @change="onAvatarChange"
                            />
                            <Button
                                type="button"
                                variant="secondary"
                                size="icon"
                                class="absolute right-0 bottom-0 size-8 rounded-full border-2 border-background shadow-sm"
                                aria-label="Change avatar"
                                @click="triggerAvatarChange"
                            >
                                <Camera
                                    class="size-4"
                                    aria-hidden="true"
                                />
                            </Button>
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm font-medium text-foreground">
                                Profile photo
                            </p>
                            <p class="text-sm text-muted-foreground">
                                Click the camera icon to change your avatar.
                            </p>
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="name"> Name </Label>
                        <Input
                            id="name"
                            class="mt-1 block w-full"
                            name="name"
                            :default-value="user.name"
                            required
                            autocomplete="name"
                            placeholder="Full name"
                        />
                        <InputError
                            class="mt-2"
                            :message="errors.name"
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email"> Email address </Label>
                        <Input
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            name="email"
                            :default-value="user.email"
                            required
                            autocomplete="username"
                            placeholder="Email address"
                        />
                        <InputError
                            class="mt-2"
                            :message="errors.email"
                        />
                    </div>

                    <div v-if="mustVerifyEmail && !user.email_verified_at">
                        <p class="-mt-4 text-sm text-muted-foreground">
                            Your email address is unverified.
                            <Link
                                :href="send()"
                                as="button"
                                class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                            >
                                Click here to resend the verification email.
                            </Link>
                        </p>

                        <div
                            v-if="status === 'verification-link-sent'"
                            class="mt-2 text-sm font-medium text-green-600"
                        >
                            A new verification link has been sent to your email
                            address.
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button
                            :disabled="processing"
                            data-test="update-profile-button"
                        >
                            Save
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
                </Form>
            </div>

            <DeleteUser />
        </SettingsLayout>
    </component>
</template>
