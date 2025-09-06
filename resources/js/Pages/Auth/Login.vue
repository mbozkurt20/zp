<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { reactive, ref } from "vue";


defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const inputRefs = ref([]);
const otpLength = 4;
const otp = reactive(Array(otpLength).fill(''));

const form = useForm({
    email: 'zp@zp.com',
    password: '',
    remember: true,
});

// OTP input olayları
const onInput = (e, index) => {
    const value = e.target.value;

    // sadece rakam izin
    if (!/^\d$/.test(value)) {
        otp[index] = '';
        return;
    }

    otp[index] = value;

    // sonraki inputa geç
    if (index < otpLength - 1) {
        inputRefs.value[index + 1]?.focus();
    } else {
        // son kutu -> submit et
        submit();
    }
};

const onKeyDown = (e, index) => {
    if (e.key === 'Backspace') {
        if (otp[index] === '') {
            if (index > 0) {
                otp[index - 1] = '';
                inputRefs.value[index - 1]?.focus();
            }
        }
    }
};

// Form submit
const submit = () => {
    form.password = otp.join(''); // 🔑 password buradan güncelleniyor
    console.log("Gönderilen:", form);

    form.post(route('login'), {
        onFinish: () => form.reset('password')
    });
};
</script>

<template>
    <GuestLayout class="bg-pink-200">
        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <TextInput
                    id="email"
                    type="email"
                    style="display: none"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div
                class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 dark:text-white/50 relative"
                style="background-image: url('/images/pasa.jpeg'); background-size: cover; background-position: center; background-repeat: no-repeat"
            >
                <!-- OTP input alanları -->
                <div class="w-full max-w-md px-4">
                    <div class="flex justify-center gap-3 mt-20">
                        <input
                            v-for="(digit, index) in otp"
                            :key="index"
                            v-model="otp[index]"
                            type="text"
                            inputmode="numeric"
                            maxlength="1"
                            class="w-14 h-14 text-center text-gray-900 text-2xl border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 dark:bg-gray-800 dark:text-white bg-white/80 backdrop-blur-sm"
                            @input="onInput($event, index)"
                            @keydown="onKeyDown($event, index)"
                            ref="inputRefs"
                        />
                    </div>
                </div>
            </div>
        </form>
    </GuestLayout>
</template>
