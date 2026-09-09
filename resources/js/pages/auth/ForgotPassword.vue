<script setup>
import layout from '@/pages/layouts/PublicLayout.vue'

import { Head, useForm } from '@inertiajs/vue3'

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library as FontAwesomeLibrary } from '@fortawesome/fontawesome-svg-core'
import { faSpinner, faEnvelope, faBookOpen, faArrowLeft, faWandMagicSparkles } from '@fortawesome/free-solid-svg-icons'

FontAwesomeLibrary.add(faSpinner, faEnvelope, faBookOpen, faArrowLeft, faWandMagicSparkles)

defineOptions({ layout })

const resetRequestForm = useForm({
    email: '',
})

function validateForm() {
    resetRequestForm.clearErrors()

    let valid = true

    if (!resetRequestForm.email || resetRequestForm.email.trim() === '') {
        resetRequestForm.setError('email', 'Email is required.')
        valid = false
    } else if (resetRequestForm.email.length > 200) {
        resetRequestForm.setError('email', 'Email must not exceed 200 characters.')
        valid = false
    }

    return valid
}

function handleForgotPassword() {
    if (resetRequestForm.processing) return
    if (!validateForm()) return

    resetRequestForm.post(route('forgot-password.submit'), {
        preserveScroll: true,

        onSuccess: () => {
            resetRequestForm.reset()
            resetRequestForm.clearErrors()
        },

        onError: (errors) => {
            resetRequestForm.clearErrors()
            resetRequestForm.setError(errors)
        }
    })
}
</script>

<template>
    <Head title="Forgot Password" />

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
                        Back to your stories
                    </p>
                    <p class="sbfa-auth-visual-sub">
                        We'll help you regain access to your storybook studio.
                    </p>
                </div>
            </div>

            <div class="sbfa-auth-form-col">
                <a :href="route('login')" class="sbfa-auth-back">
                    <span class="sbfa-auth-back-icon">
                        <FontAwesomeIcon :icon="['fas', 'arrow-left']" />
                    </span>
                    Back to sign in
                </a>

                <div class="sbfa-auth-header">
                    <h1 class="sbfa-auth-title">Forgot your password?</h1>
                    <p class="sbfa-auth-subtitle">
                        Enter your email address and we'll send you a link to reset your password.
                    </p>
                </div>

                <form @submit.prevent="handleForgotPassword" class="sbfa-auth-form">
                    <div class="sbfa-field">
                        <label for="email" class="sbfa-field-label">Email</label>
                        <input
                            id="email"
                            v-model="resetRequestForm.email"
                            type="email"
                            placeholder="you@example.com"
                            autofocus
                            autocomplete="email"
                            class="sbfa-input"
                            :class="{ 'has-error': resetRequestForm.errors.email }"
                        />
                        <p v-if="resetRequestForm.errors.email" class="sbfa-error">
                            {{ resetRequestForm.errors.email }}
                        </p>
                    </div>

                    <button
                        type="submit"
                        :disabled="resetRequestForm.processing"
                        class="sbfa-auth-primary"
                    >
                        <FontAwesomeIcon v-if="resetRequestForm.processing" icon="spinner" spin />
                        <FontAwesomeIcon v-else icon="envelope" />
                        <span>{{ resetRequestForm.processing ? 'Sending link...' : 'Send Reset Link' }}</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>