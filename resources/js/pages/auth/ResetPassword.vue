<script setup>
import layout from '@/pages/layouts/PublicLayout.vue'

import { Head, useForm, usePage } from '@inertiajs/vue3'

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library as FontAwesomeIconLibrary } from '@fortawesome/fontawesome-svg-core'
import { faEye, faEyeSlash, faSpinner, faLock, faBookOpen, faWandMagicSparkles } from '@fortawesome/free-solid-svg-icons'

import {
    showPassword,
    showConfirmPassword,
    togglePasswordVisibility,
    toggleConfirmPasswordVisibility,
} from '@/composables/usePassword'

FontAwesomeIconLibrary.add(faEye, faEyeSlash, faSpinner, faLock, faBookOpen, faWandMagicSparkles)

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

    <div class="sbfa-auth-shell">
        <div class="sbfa-auth-card">
            <div class="sbfa-auth-visual">
                <a :href="route('home')" class="sbfa-auth-brand">
                    <span class="sbfa-auth-brand-mark">
                        <FontAwesomeIcon :icon="['fas', 'book-open']" />
                    </span>
                    <span class="sbfa-auth-brand-name">
                        Story Book Forge AI
                        <span>Ai storybook studio</span>
                    </span>
                </a>

                <div class="sbfa-auth-visual-book" aria-hidden="true">
                    <div class="sbfa-auth-visual-page is-back">
                        <span class="sbfa-auth-page-line"></span>
                        <span class="sbfa-auth-page-line short"></span>
                    </div>
                    <div class="sbfa-auth-visual-page">
                        <FontAwesomeIcon :icon="['fas', 'wand-magic-sparkles']" class="sbfa-auth-visual-title" style="font-size: 1.4rem" />
                        <span class="sbfa-auth-page-line"></span>
                        <span class="sbfa-auth-page-line"></span>
                        <span class="sbfa-auth-page-line short"></span>
                    </div>
                    <div class="sbfa-auth-visual-page is-back">
                        <span class="sbfa-auth-page-line"></span>
                        <span class="sbfa-auth-page-line short"></span>
                    </div>
                </div>

                <div class="sbfa-auth-visual-copy">
                    <p class="sbfa-auth-visual-title">
                        <FontAwesomeIcon :icon="['fas', 'wand-magic-sparkles']" />
                        Secure your studio
                    </p>
                    <p class="sbfa-auth-visual-sub">
                        Choose a new password for your Story Book Forge AI account.
                    </p>
                </div>
            </div>

            <div class="sbfa-auth-form-col">
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
            </div>
        </div>
    </div>
</template>