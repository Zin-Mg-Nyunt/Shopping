<script setup>
import { Form, Head, Link } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { home, register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Log in to your account',
        description: 'Enter your email and password below to log in',
    },
});

defineProps({
    status: String,
    canResetPassword: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="Log in" />

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
                    Log in to your account
                </h1>
                <p class="text-sm text-muted-foreground">
                    Enter your email and password below to continue.
                </p>
            </div>

            <div
                v-if="status"
                class="mb-5 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-2 text-center text-sm font-medium text-emerald-700 dark:text-emerald-300"
            >
                {{ status }}
            </div>

            <Form
                v-bind="store.form()"
                :reset-on-success="['password']"
                v-slot="{ errors, processing }"
                class="flex flex-col gap-6"
            >
                <div
                    class="space-y-5 rounded-2xl border border-border/60 bg-card/60 p-5 shadow-sm"
                >
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
                            name="email"
                            required
                            autofocus
                            :tabindex="1"
                            autocomplete="email"
                            placeholder="email@example.com"
                            class="h-11 rounded-xl"
                        />
                        <InputError :message="errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <div class="flex items-center justify-between">
                            <Label
                                for="password"
                                class="text-sm font-medium text-foreground"
                            >
                                Password
                            </Label>
                            <TextLink
                                v-if="canResetPassword"
                                :href="request()"
                                class="text-xs font-medium text-primary hover:text-primary/80"
                                :tabindex="5"
                            >
                                Forgot password?
                            </TextLink>
                        </div>
                        <PasswordInput
                            id="password"
                            name="password"
                            required
                            :tabindex="2"
                            autocomplete="current-password"
                            placeholder="Password"
                            class="h-11 rounded-xl"
                        />
                        <InputError :message="errors.password" />
                    </div>

                    <div
                        class="rounded-xl border border-border/70 bg-muted/40 px-3 py-2.5"
                    >
                        <Label
                            for="remember"
                            class="flex items-center gap-3 text-sm"
                        >
                            <Checkbox
                                id="remember"
                                name="remember"
                                :tabindex="3"
                            />
                            <span class="text-muted-foreground"
                                >Remember me</span
                            >
                        </Label>
                    </div>

                    <Button
                        type="submit"
                        class="h-11 w-full rounded-xl text-sm font-semibold"
                        :tabindex="4"
                        :disabled="processing"
                        data-test="login-button"
                    >
                        <Spinner v-if="processing" />
                        Log in
                    </Button>
                </div>
                <div
                    v-if="canRegister"
                    class="text-center text-sm text-muted-foreground"
                >
                    Don't have an account?
                    <TextLink
                        :href="register()"
                        :tabindex="5"
                        class="font-medium text-primary hover:text-primary/80"
                    >
                        Sign up
                    </TextLink>
                </div>
            </Form>
        </div>
    </main>
</template>
