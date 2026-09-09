<script setup>
import layout from '@/pages/layouts/PublicLayout.vue'

import { Head, useForm } from '@inertiajs/vue3'

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library as FontAwesomeLibrary } from '@fortawesome/fontawesome-svg-core'
import { faEye, faEyeSlash, faSpinner, faUserPlus, faBookOpen, faWandMagicSparkles } from '@fortawesome/free-solid-svg-icons'

import {
    showPassword,
    showConfirmPassword,
    togglePasswordVisibility,
    toggleConfirmPasswordVisibility,
} from '@/composables/usePassword'

FontAwesomeLibrary.add(faEye, faEyeSlash, faSpinner, faUserPlus, faBookOpen, faWandMagicSparkles)

defineOptions({ layout })

const registerForm = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
})

function validateForm() {
    registerForm.clearErrors()

    let valid = true

    if (!registerForm.name || registerForm.name.trim() === '') {
        registerForm.setError('name', 'Name is required.')
        valid = false
    } else if (registerForm.name.length > 200) {
        registerForm.setError('name', 'Name must not exceed 200 characters.')
        valid = false
    }

    if (!registerForm.email || registerForm.email.trim() === '') {
        registerForm.setError('email', 'Email is required.')
        valid = false
    } else if (registerForm.email.length > 200) {
        registerForm.setError('email', 'Email must not exceed 200 characters.')
        valid = false
    }

    if (!registerForm.password || registerForm.password.trim() === '') {
        registerForm.setError('password', 'Password is required.')
        valid = false
    }

    if (!registerForm.password_confirmation || registerForm.password_confirmation.trim() === '') {
        registerForm.setError('password_confirmation', 'Password confirmation is required.')
        valid = false
    } else if (registerForm.password !== registerForm.password_confirmation) {
        registerForm.setError('password_confirmation', 'Password confirmation does not match.')
        valid = false
    }

    return valid
}

function handleRegister() {
    if (registerForm.processing) return
    if (!validateForm()) return

    registerForm.post(route('register.submit'), {
        preserveScroll: true,

        onSuccess: () => {
            registerForm.reset()
            registerForm.clearErrors()
        },

        onError: (errors) => {
            registerForm.clearErrors()
            registerForm.setError(errors)
        }
    })
}
</script>

<template>
    <Head title="Register" />

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
                        Begin a new story
                    </p>
                    <p class="sbfa-auth-visual-sub">
                        Create your studio and start turning ideas into illustrated storybooks.
                    </p>
                </div>
            </div>

            <div class="sbfa-auth-form-col">
                <div class="sbfa-auth-header">
                    <h1 class="sbfa-auth-title">Create your account</h1>
                    <p class="sbfa-auth-subtitle">Join Story Book Forge AI and start creating.</p>
                </div>

                <form @submit.prevent="handleRegister" class="sbfa-auth-form">
                    <div class="sbfa-field">
                        <label for="name" class="sbfa-field-label">Name</label>
                        <input
                            id="name"
                            v-model="registerForm.name"
                            type="text"
                            placeholder="Your full name"
                            autofocus
                            autocomplete="name"
                            class="sbfa-input"
                            :class="{ 'has-error': registerForm.errors.name }"
                        />
                        <p v-if="registerForm.errors.name" class="sbfa-error">
                            {{ registerForm.errors.name }}
                        </p>
                    </div>

                    <div class="sbfa-field">
                        <label for="email" class="sbfa-field-label">Email</label>
                        <input
                            id="email"
                            v-model="registerForm.email"
                            type="email"
                            placeholder="you@example.com"
                            autocomplete="email"
                            class="sbfa-input"
                            :class="{ 'has-error': registerForm.errors.email }"
                        />
                        <p v-if="registerForm.errors.email" class="sbfa-error">
                            {{ registerForm.errors.email }}
                        </p>
                    </div>

                    <div class="sbfa-field">
                        <label for="password" class="sbfa-field-label">Password</label>
                        <div class="sbfa-input-wrap">
                            <input
                                id="password"
                                v-model="registerForm.password"
                                :type="showPassword ? 'text' : 'password'"
                                placeholder="Create a password"
                                autocomplete="new-password"
                                class="sbfa-input sbfa-input-password"
                                :class="{ 'has-error': registerForm.errors.password }"
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
                        <p v-if="registerForm.errors.password" class="sbfa-error">
                            {{ registerForm.errors.password }}
                        </p>
                    </div>

                    <div class="sbfa-field">
                        <label for="passwordConfirmation" class="sbfa-field-label">Confirm Password</label>
                        <div class="sbfa-input-wrap">
                            <input
                                id="passwordConfirmation"
                                v-model="registerForm.password_confirmation"
                                :type="showConfirmPassword ? 'text' : 'password'"
                                placeholder="Confirm your password"
                                autocomplete="new-password"
                                class="sbfa-input sbfa-input-password"
                                :class="{ 'has-error': registerForm.errors.password_confirmation }"
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
                        <p v-if="registerForm.errors.password_confirmation" class="sbfa-error">
                            {{ registerForm.errors.password_confirmation }}
                        </p>
                    </div>

                    <button
                        type="submit"
                        :disabled="registerForm.processing"
                        class="sbfa-auth-primary"
                    >
                        <FontAwesomeIcon v-if="registerForm.processing" icon="spinner" spin />
                        <FontAwesomeIcon v-else icon="user-plus" />
                        <span>{{ registerForm.processing ? 'Creating account...' : 'Create Account' }}</span>
                    </button>
                </form>

                <div class="sbfa-auth-footer is-center">
                    <span class="sbfa-auth-footer-text">
                        Already have an account?
                        <a :href="route('login')" class="sbfa-auth-link">Sign in</a>
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>