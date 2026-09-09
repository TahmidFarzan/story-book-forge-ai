<script setup>
import layout from '@/pages/layouts/PublicLayout.vue'

import { Head, useForm } from '@inertiajs/vue3'

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library as FontAwesomeLibrary } from '@fortawesome/fontawesome-svg-core'
import { faEye, faEyeSlash, faSpinner, faRightToBracket, faBookOpen, faWandMagicSparkles } from '@fortawesome/free-solid-svg-icons'

import {
    showPassword,
    togglePasswordVisibility,
} from '@/composables/usePassword'

FontAwesomeLibrary.add(faEye, faEyeSlash, faSpinner, faRightToBracket, faBookOpen, faWandMagicSparkles)

defineOptions({ layout })

const loginForm = useForm({
    email: '',
    password: '',
    remember: false,
})

function validateForm() {
    loginForm.clearErrors()

    let valid = true

    if (!loginForm.email || loginForm.email.trim() === '') {
        loginForm.setError('email', 'Email is required.')
        valid = false
    } else if (loginForm.email.length > 200) {
        loginForm.setError('email', 'Email must not exceed 200 characters.')
        valid = false
    }

    if (!loginForm.password || loginForm.password.trim() === '') {
        loginForm.setError('password', 'Password is required.')
        valid = false
    }

    return valid
}

function handleLogin() {
    if (loginForm.processing) return
    if (!validateForm()) return

    loginForm.post(route('login.submit'), {
        preserveScroll: true,

        onSuccess: () => {
            loginForm.reset()
            loginForm.clearErrors()
        },

        onError: (errors) => {
            loginForm.clearErrors()
            loginForm.setError(errors)
        }
    })
}
</script>

<template>
    <Head title="Login" />

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
                        Turn ideas into storybooks
                    </p>
                    <p class="sbfa-auth-visual-sub">
                        Rejoin your creative studio and continue crafting AI-powered illustrated stories.
                    </p>
                </div>
            </div>

            <div class="sbfa-auth-form-col">
                <div class="sbfa-auth-header">
                    <h1 class="sbfa-auth-title">Welcome back</h1>
                    <p class="sbfa-auth-subtitle">Sign in to your storybook studio to continue.</p>
                </div>

                <form @submit.prevent="handleLogin" class="sbfa-auth-form">
                    <div class="sbfa-field">
                        <label for="email" class="sbfa-field-label">Email</label>
                        <input
                            id="email"
                            v-model="loginForm.email"
                            type="email"
                            placeholder="you@example.com"
                            autofocus
                            autocomplete="email"
                            class="sbfa-input"
                            :class="{ 'has-error': loginForm.errors.email }"
                        />
                        <p v-if="loginForm.errors.email" class="sbfa-error">
                            {{ loginForm.errors.email }}
                        </p>
                    </div>

                    <div class="sbfa-field">
                        <label for="password" class="sbfa-field-label">Password</label>
                        <div class="sbfa-input-wrap">
                            <input
                                id="password"
                                v-model="loginForm.password"
                                :type="showPassword ? 'text' : 'password'"
                                placeholder="Enter your password"
                                autocomplete="current-password"
                                class="sbfa-input sbfa-input-password"
                                :class="{ 'has-error': loginForm.errors.password }"
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
                        <p v-if="loginForm.errors.password" class="sbfa-error">
                            {{ loginForm.errors.password }}
                        </p>
                    </div>

                    <div class="sbfa-check">
                        <input
                            id="remember"
                            v-model="loginForm.remember"
                            type="checkbox"
                            class="sbfa-checkbox"
                        />
                        <label for="remember" class="sbfa-check-label">Remember me</label>
                    </div>

                    <button
                        type="submit"
                        :disabled="loginForm.processing"
                        class="sbfa-auth-primary"
                    >
                        <FontAwesomeIcon v-if="loginForm.processing" icon="spinner" spin />
                        <FontAwesomeIcon v-else icon="right-to-bracket" />
                        <span>{{ loginForm.processing ? 'Signing in...' : 'Sign In' }}</span>
                    </button>
                </form>

                <div class="sbfa-auth-footer is-center">
                    <a :href="route('forgot-password')" class="sbfa-auth-link">
                        Forgot password?
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>