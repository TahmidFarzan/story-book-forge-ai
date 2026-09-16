<script setup>
import layout from '@/pages/layouts/PublicLayout.vue'

import { Head, useForm } from '@inertiajs/vue3'

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library as FontAwesomeLibrary } from '@fortawesome/fontawesome-svg-core'
import { faSpinner, faEnvelope, faArrowLeft } from '@fortawesome/free-solid-svg-icons'

import AuthCard from '@/components/common/layout/public-layout/AuthCard.vue'

FontAwesomeLibrary.add(faSpinner, faEnvelope, faArrowLeft)

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

    <AuthCard>
        <template #visual-title>Back to your stories</template>
        <template #visual-sub>We'll help you regain access to your storybook studio.</template>

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
    </AuthCard>
</template>

<style scoped>
.sbfa-auth-back {
    transition: color var(--transition), border-color var(--transition), box-shadow var(--transition), background var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--text-muted);
    text-decoration: none;
    margin-bottom: 1.25rem;
}

.sbfa-auth-back:hover {
    color: var(--accent);
}

.sbfa-auth-back-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 1.6rem;
    height: 1.6rem;
    border-radius: 50%;
    background: var(--accent-soft);
    color: var(--accent);
}
</style>
