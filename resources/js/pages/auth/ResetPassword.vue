<script setup>
import layout from '@/pages/layouts/PublicLayout.vue'

import { Head, useForm, usePage } from '@inertiajs/vue3'

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library as FontAwesomeIconLibrary } from '@fortawesome/fontawesome-svg-core'
import { faEye, faEyeSlash, faSpinner, faLock } from '@fortawesome/free-solid-svg-icons'

import AuthCard from '@/components/common/layout/public-layout/AuthCard.vue'

import {
    showPassword,
    showConfirmPassword,
    togglePasswordVisibility,
    toggleConfirmPasswordVisibility,
} from '@/composables/usePassword'

FontAwesomeIconLibrary.add(faEye, faEyeSlash, faSpinner, faLock)

defineOptions({ layout })

const page = usePage()
const token = page.props.token
const email = page.props.email

const resetPasswordForm = useForm({
    email: email || '',
    password: '',
    password_confirmation: '',
    token: token || '',
})

function validateForm() {
    resetPasswordForm.clearErrors()

    let valid = true

    if (!resetPasswordForm.password || resetPasswordForm.password.trim() === '') {
        resetPasswordForm.setError('password', 'Password is required.')
        valid = false
    }

    if (!resetPasswordForm.password_confirmation || resetPasswordForm.password_confirmation.trim() === '') {
        resetPasswordForm.setError('password_confirmation', 'Password confirmation is required.')
        valid = false
    } else if (resetPasswordForm.password !== resetPasswordForm.password_confirmation) {
        resetPasswordForm.setError('password_confirmation', 'Password confirmation does not match.')
        valid = false
    }

    return valid
}

function handleResetPassword() {
    if (resetPasswordForm.processing) return
    if (!validateForm()) return

    resetPasswordForm.post(route('password.reset.submit', { email, token }), {
        preserveScroll: true,

        onSuccess: () => {
            resetPasswordForm.reset()
            resetPasswordForm.clearErrors()
        },

        onError: (errors) => {
            resetPasswordForm.clearErrors()
            resetPasswordForm.setError(errors)
        }
    })
}
</script>

<template>
    <Head title="Reset Password" />

    <AuthCard>
        <template #visual-title>Secure your studio</template>
        <template #visual-sub>Choose a new password for your Story Book Forge AI account.</template>

        <div class="sbfa-auth-header">
            <h1 class="sbfa-auth-title">Set a new password</h1>
            <p class="sbfa-auth-subtitle">
                Enter your new password below to secure your account.
            </p>
        </div>

        <form @submit.prevent="handleResetPassword" class="sbfa-auth-form">
            <input v-model="resetPasswordForm.email" type="hidden" />
            <input v-model="resetPasswordForm.token" type="hidden" />

            <div class="sbfa-field">
                <label for="password" class="sbfa-field-label">New Password</label>
                <div class="sbfa-input-wrap">
                    <input
                        id="password"
                        v-model="resetPasswordForm.password"
                        :type="showPassword ? 'text' : 'password'"
                        placeholder="Enter new password"
                        autocomplete="new-password"
                        class="sbfa-input sbfa-input-password"
                        :class="{ 'has-error': resetPasswordForm.errors.password }"
                    />
                    <button
                        type="button"
                        class="sbfa-input-toggle"
                        :aria-label="showPassword ? 'Hide password' : 'Show password'"
                        @click="togglePasswordVisibility"
                    >
                        <FontAwesomeIcon :icon="showPassword ? 'eye-slash' : 'eye'" />
                    </button>
                </div>
                <p v-if="resetPasswordForm.errors.password" class="sbfa-error">
                    {{ resetPasswordForm.errors.password }}
                </p>
            </div>

            <div class="sbfa-field">
                <label for="password_confirmation" class="sbfa-field-label">Confirm Password</label>
                <div class="sbfa-input-wrap">
                    <input
                        id="password_confirmation"
                        v-model="resetPasswordForm.password_confirmation"
                        :type="showConfirmPassword ? 'text' : 'password'"
                        placeholder="Confirm your password"
                        autocomplete="new-password"
                        class="sbfa-input sbfa-input-password"
                        :class="{ 'has-error': resetPasswordForm.errors.password_confirmation }"
                    />
                    <button
                        type="button"
                        class="sbfa-input-toggle"
                        :aria-label="showConfirmPassword ? 'Hide confirm password' : 'Show confirm password'"
                        @click="toggleConfirmPasswordVisibility"
                    >
                        <FontAwesomeIcon :icon="showConfirmPassword ? 'eye-slash' : 'eye'" />
                    </button>
                </div>
                <p v-if="resetPasswordForm.errors.password_confirmation" class="sbfa-error">
                    {{ resetPasswordForm.errors.password_confirmation }}
                </p>
            </div>

            <button
                type="submit"
                :disabled="resetPasswordForm.processing"
                class="sbfa-auth-primary"
            >
                <FontAwesomeIcon v-if="resetPasswordForm.processing" icon="spinner" spin />
                <FontAwesomeIcon v-else icon="lock" />
                <span>{{ resetPasswordForm.processing ? 'Resetting...' : 'Reset Password' }}</span>
            </button>
        </form>

        <div class="sbfa-auth-footer is-center">
            <span class="sbfa-auth-footer-text">
                Remember your password?
                <a :href="route('login')" class="sbfa-auth-link">Back to sign in</a>
            </span>
        </div>
    </AuthCard>
</template>
