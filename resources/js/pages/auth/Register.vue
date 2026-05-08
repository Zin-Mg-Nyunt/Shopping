<script setup>
import { Form, Head, Link } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { home, login } from '@/routes';
import { store } from '@/routes/register';

defineOptions({
    layout: {
        title: 'Create an account',
        description: 'Enter your details below to create your account',
    },
});
</script>

<template>
    <Head title="Register" />

    <main
        class="flex min-h-svh items-center justify-center bg-background px-4 py-10"
    >
        <div
            class="w-full max-w-md rounded-3xl border border-border/70 bg-card/85 p-6 shadow-xl sm:p-8"
        >
            <Link :href="home()" class="mb-5 flex justify-center">
                <AppLogoIcon class="size-20 fill-current text-foreground" />
            </Link>

            <div class="mb-6 space-y-2 text-center">
                <h1
                    class="text-2xl font-semibold tracking-tight text-foreground"
                >
                    Create an account
                </h1>
                <p class="text-sm text-muted-foreground">
                    Enter your details below to create your account.
                </p>
            </div>

            <Form
                v-bind="store.form()"
                :reset-on-success="['password', 'password_confirmation']"
                v-slot="{ errors, processing }"
                class="flex flex-col gap-6"
            >
                <div
                    class="space-y-5 rounded-2xl border border-border/60 bg-card/60 p-5 shadow-sm"
                >
                    <div class="grid gap-2">
                        <Label
                            for="name"
                            class="text-sm font-medium text-foreground"
                        >
                            Name
                        </Label>
                        <Input
                            id="name"
                            type="text"
                            required
                            autofocus
                            :tabindex="1"
                            autocomplete="name"
                            name="name"
                            placeholder="Full name"
                            class="h-11 rounded-xl"
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label
                            for="email"
                            class="text-sm font-medium text-foreground"
                        >
                            Email address
                        </Label>
                        <Input
                            id="email"
                            type="email"
                            required
                            :tabindex="2"
                            autocomplete="email"
                            name="email"
                            placeholder="email@example.com"
                            class="h-11 rounded-xl"
                        />
                        <InputError :message="errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <Label
                            for="password"
                            class="text-sm font-medium text-foreground"
                        >
                            Password
                        </Label>
                        <PasswordInput
                            id="password"
                            required
                            :tabindex="3"
                            autocomplete="new-password"
                            name="password"
                            placeholder="Password"
                            class="h-11 rounded-xl"
                        />
                        <InputError :message="errors.password" />
                    </div>

                    <div class="grid gap-2">
                        <Label
                            for="password_confirmation"
                            class="text-sm font-medium text-foreground"
                        >
                            Confirm password
                        </Label>
                        <PasswordInput
                            id="password_confirmation"
                            required
                            :tabindex="4"
                            autocomplete="new-password"
                            name="password_confirmation"
                            placeholder="Confirm password"
                            class="h-11 rounded-xl"
                        />
                        <InputError :message="errors.password_confirmation" />
                    </div>

                    <Button
                        type="submit"
                        class="h-11 w-full rounded-xl text-sm font-semibold"
                        tabindex="5"
                        :disabled="processing"
                        data-test="register-user-button"
                    >
                        <Spinner v-if="processing" />
                        Create account
                    </Button>
                </div>

                <div class="text-center text-sm text-muted-foreground">
                    Already have an account?
                    <TextLink
                        :href="login()"
                        class="font-medium text-primary hover:text-primary/80"
                        :tabindex="6"
                    >
                        Log in
                    </TextLink>
                </div>
            </Form>
        </div>
    </main>
</template>
