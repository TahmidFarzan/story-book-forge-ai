<script setup>
import layout from '@/pages/layouts/PublicLayout.vue'

import { ref } from 'vue'
import { Head, router as inertiaJsRoute } from '@inertiajs/vue3'

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faSpinner, faEnvelopeCircleCheck, faPaperPlane } from '@fortawesome/free-solid-svg-icons'

import AuthCard from '@/components/common/layout/public-layout/AuthCard.vue'

library.add(faSpinner, faEnvelopeCircleCheck, faPaperPlane)

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

    <AuthCard>
        <template #visual-title>Confirm your story begins</template>
        <template #visual-sub>Verify your email to unlock your full storybook studio.</template>

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
    </AuthCard>
</template>

<style scoped>
.sbfa-auth-verify-icon {
    width: 4rem;
    height: 4rem;
    border-radius: 50%;
    background: var(--accent-soft);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.75rem;
}

.sbfa-auth-verify-icon :deep(svg) {
    font-size: 1.5rem;
    color: var(--accent);
}
</style>
