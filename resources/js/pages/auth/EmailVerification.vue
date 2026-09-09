<script setup>
import layout from '@/pages/layouts/PublicLayout.vue'

import { ref } from 'vue'
import { Head, router as inertiaJsRoute } from '@inertiajs/vue3'

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faSpinner, faEnvelopeCircleCheck, faPaperPlane, faBookOpen, faWandMagicSparkles } from '@fortawesome/free-solid-svg-icons'

library.add(faSpinner, faEnvelopeCircleCheck, faPaperPlane, faBookOpen, faWandMagicSparkles)

defineOptions({ layout })

const resending = ref(false)

function handleResendVerification() {
    if (resending.value) return

    resending.value = true

    inertiaJsRoute.post(route('verification.resend'), {}, {
        onFinish: () => {
            resending.value = false
        }
    })
}
</script>

<template>
    <Head title="Email Verification" />

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
                        Confirm your story begins
                    </p>
                    <p class="sbfa-auth-visual-sub">
                        Verify your email to unlock your full storybook studio.
                    </p>
                </div>
            </div>

            <div class="sbfa-auth-form-col">
                <div class="sbfa-auth-verify-icon">
                    <FontAwesomeIcon icon="envelope-circle-check" />
                </div>

                <div class="sbfa-auth-header">
                    <h1 class="sbfa-auth-title">Verify your email</h1>
                    <p class="sbfa-auth-subtitle">
                        We've sent a verification link to your email address. Check your inbox and click
                        the link to verify your account.
                    </p>
                </div>

                <form @submit.prevent="handleResendVerification">
                    <button
                        type="submit"
                        :disabled="resending"
                        class="sbfa-auth-primary"
                    >
                        <FontAwesomeIcon v-if="resending" icon="spinner" spin />
                        <FontAwesomeIcon v-else icon="paper-plane" />
                        <span>{{ resending ? 'Sending...' : 'Resend Verification Email' }}</span>
                    </button>
                </form>

                <div class="sbfa-auth-footer is-center">
                    <span class="sbfa-auth-footer-text">
                        Already verified?
                        <a :href="route('login')" class="sbfa-auth-link">Back to sign in</a>
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>