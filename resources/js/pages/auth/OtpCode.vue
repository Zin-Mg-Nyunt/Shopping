<script setup>
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { cn } from '@/lib/utils';
import { login } from '@/routes';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, nextTick, onMounted, onUnmounted, ref } from 'vue';
import { route } from 'ziggy-js';

const props = defineProps({
    email: {
        type: String,
        default: '',
    },
    expiredAt: {
        type: Number,
        default: 0,
    },
    status: {
        type: String,
        default: '',
    },
});

const countdown = ref(180);
const interval = ref(null);

const displayTime = computed(() => {
    let minutes = Math.floor(countdown.value / 60);
    let seconds = countdown.value % 60;
    return `${minutes}:${seconds.toString().padStart(2, '0')}`;
});

const startCountdown = () => {
    if (interval.value) {
        clearInterval(interval.value);
    }
    interval.value = setInterval(() => {
        if (countdown.value <= 0) {
            clearInterval(interval.value);
            return;
        }
        countdown.value--;
    }, 1000);
};

onMounted(() => {
    const now = Math.floor(Date.now() / 1000);
    const remainingTime = props.expiredAt - now;
    if (remainingTime > 0) {
        countdown.value = remainingTime;
        startCountdown();
    } else {
        countdown.value = 0;
    }
});

onUnmounted(() => {
    countdown.value = 0;
    clearInterval(interval.value);
});

const digits = ref(['', '', '', '', '', '']);
const digitInputBoxes = ref([]);

function onDigitInput(index, event) {
    const value = event.target.value.replace(/\D/g, '').slice(-1);
    digits.value[index] = value;
    if (value && index < 5) {
        digitInputBoxes.value[index + 1]?.focus();
    }
}

function onDigitKeydown(index, event) {
    if (event.key === 'Backspace' && !digits.value[index] && index > 0) {
        digitInputBoxes.value[index - 1]?.focus();
    }
}

function onDigitPaste(event) {
    event.preventDefault();
    const pasted =
        event.clipboardData?.getData('text').replace(/\D/g, '').slice(0, 6) ||
        '';
    pasted.split('').forEach((char, i) => {
        if (i < 6) digits.value[i] = char;
    });
    const next = Math.min(pasted.length, 5);
    nextTick(() => digitInputBoxes.value[next]?.focus());
}

const resendCode = () => {
    if (countdown.value > 0) return;
    router.post(
        route('password.email'),
        { email: props.email },
        {
            onSuccess: () => {
                countdown.value = 180;
                startCountdown();
            },
        },
    );
};
const form = useForm({
    otp: '',
});
const verifyCode = () => {
    form.otp = Number(digits.value.join(''));
    form.post(route('password.verify-otp'), { otp: form.otp });
};
</script>

<template>
    <AuthLayout
        title="Verify OTP"
        description=""
    >
        <Head title="Verify OTP" />

        <form
            @submit.prevent="verifyCode"
            class="space-y-6"
        >
            <div class="text-sm text-muted-foreground">
                <p>Enter the 6-digit code we sent to your email.</p>
                <p>
                    Wrong Email?
                    <Link
                        :href="route('password.change-email')"
                        class="underline"
                        >Change Email</Link
                    >
                </p>
            </div>
            <div
                class="flex items-center justify-start gap-2"
                v-if="status"
            >
                <div class="text-sm font-medium text-green-600">
                    {{ status }}
                </div>
                <span
                    :class="[
                        'text-sm font-medium',
                        countdown <= 0 ? 'text-red-600' : 'text-green-600',
                    ]"
                >
                    ({{ displayTime }})
                </span>
            </div>
            <div class="grid gap-2">
                <Label for="otp-0">Verification code</Label>
                <div
                    class="flex justify-center gap-2 sm:gap-3"
                    role="group"
                    aria-label="One-time password digits"
                >
                    <input
                        v-for="(_, index) in digits"
                        :key="index"
                        :ref="
                            (el) => {
                                digitInputBoxes[index] = el;
                            }
                        "
                        :id="index === 0 ? 'otp-0' : undefined"
                        v-model="digits[index]"
                        type="text"
                        inputmode="numeric"
                        maxlength="1"
                        autocomplete="one-time-code"
                        :aria-label="`Digit ${index + 1}`"
                        :class="
                            cn(
                                'h-11 w-11 text-center text-lg font-semibold tabular-nums sm:h-12 sm:w-12',
                                'rounded-md border border-input bg-transparent shadow-xs transition-[color,box-shadow] outline-none',
                                'focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50',
                                'placeholder:text-muted-foreground',
                                'dark:bg-input/30',
                            )
                        "
                        @input="(e) => onDigitInput(index, e)"
                        @keydown="(e) => onDigitKeydown(index, e)"
                        @paste="onDigitPaste"
                    />
                </div>
                <InputError
                    :message="form.errors.otp"
                    class="mt-2"
                />
            </div>

            <div class="flex flex-col gap-4">
                <Button
                    type="submit"
                    class="w-full"
                    data-test="verify-otp-button"
                    :disabled="digits.some((digit) => !digit)"
                >
                    Verify code
                </Button>

                <p class="text-center text-sm text-muted-foreground">
                    Didn't receive the code?
                    <Button
                        type="button"
                        variant="link"
                        :class="[
                            'cursor-pointer p-0 text-sm text-muted-foreground underline',
                            countdown > 0
                                ? 'cursor-not-allowed opacity-50'
                                : '',
                        ]"
                        :disabled="countdown > 0"
                        @click="resendCode"
                    >
                        Resend code
                    </Button>
                </p>
            </div>

            <div class="space-x-1 text-center text-sm text-muted-foreground">
                <span>Or, return to</span>
                <TextLink :href="login()">log in</TextLink>
            </div>
        </form>
    </AuthLayout>
</template>
