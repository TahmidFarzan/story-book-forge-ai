<script setup>
import OffCanvasMenu from '@/components/common/layout/auth-layout/OffCanvasMenu.vue'
import Breadcrumbs from '@/components/common/layout/auth-layout/Breadcrumbs.vue'
import AuthTopbarDropdownMenu from '@/components/common/layout/auth-layout/AuthTopbarDropdownMenu.vue'
import FlashMessageToaster from '@/components/common/layout/FlashMessageToaster.vue'

import { usePage } from '@inertiajs/vue3'
import { computed, provide } from 'vue'

import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faBookOpen } from '@fortawesome/free-solid-svg-icons'

library.add(faBookOpen)

const appName = import.meta.env.VITE_APP_NAME

const page = usePage()

const authUser = computed(() => page.props.auth?.user ?? null)
const flashMessage = computed(() => page.props.flashMessage ?? null)

provide('authUser', authUser)
</script>

<template>
    <div class="sbfa-dashboard-layout flex min-h-screen flex-col">
        <header class="sbfa-dashboard-header">
            <div class="mx-auto flex h-16 w-full max-w-[100rem] items-center justify-between gap-4 px-4 sm:px-6">
                <a :href="route('home')" class="sbfa-dashboard-brand" :aria-label="appName">
                    <span class="sbfa-dashboard-brand-mark" aria-hidden="true">
                        <FontAwesomeIcon :icon="['fas', 'book-open']" />
                    </span>

                    <span v-if="appName" class="sbfa-dashboard-brand-name">{{ appName }}</span>
                </a>

                <div class="flex items-center gap-3">
                    <OffCanvasMenu mode="trigger" :auth-user="authUser" />

                    <AuthTopbarDropdownMenu :auth-user="authUser" />
                </div>
            </div>
        </header>

        <main class="flex flex-1 pt-16">
            <OffCanvasMenu mode="sidebar" :auth-user="authUser" />

            <div class="flex-1 min-w-0 p-4 sm:p-6">
                <Breadcrumbs />

                <div v-if="authUser && !authUser.email_verified_at" class="sbfa-verify-banner" role="note">
                    Please verify your email address.
                </div>

                <slot />
            </div>
        </main>

        <footer class="sbfa-dashboard-footer">
            <div class="mx-auto flex w-full max-w-[100rem] flex-col items-center justify-between gap-2 px-4 py-5 text-sm sm:flex-row sm:px-6">
                <span class="sbfa-dashboard-footer-text">
                    &copy; {{ new Date().getFullYear() }} {{ appName }}. All rights reserved.
                </span>

                <span class="sbfa-dashboard-footer-text">
                    Developed by
                    <a
                        href="https://www.linkedin.com/in/sk-md-tahmid-farzan/"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="sbfa-dashboard-footer-link"
                    >
                        Seikh Md Tahmid Farzan
                    </a>
                </span>
            </div>
        </footer>

        <FlashMessageToaster :flash-message="flashMessage" />
    </div>
</template>

<style scoped>
.sbfa-dashboard-layout {
    background: var(--story-book-forge-ai-background);
    color: var(--story-book-forge-ai-text);
    font-family: var(--story-book-forge-ai-font);
    text-rendering: optimizeLegibility;
    -webkit-font-smoothing: antialiased;
}

.sbfa-dashboard-layout :deep(a:focus-visible),
.sbfa-dashboard-layout :deep(button:focus-visible),
.sbfa-dashboard-layout :deep(input:focus-visible),
.sbfa-dashboard-layout :deep(select:focus-visible),
.sbfa-dashboard-layout :deep(textarea:focus-visible) {
    outline: 0;
    box-shadow: var(--story-book-forge-ai-focus-ring);
    border-radius: var(--story-book-forge-ai-radius-sm);
}

.sbfa-dashboard-header {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 50;
    background: var(--story-book-forge-ai-header-bg);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 4px 24px rgb(43 38 51 / 18%);
}

.sbfa-dashboard-brand {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    min-width: 0;
    text-decoration: none;
}

.sbfa-dashboard-brand-mark {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2.25rem;
    height: 2.25rem;
    flex-shrink: 0;
    border-radius: 10px;
    color: #fff;
    background: linear-gradient(135deg, var(--story-book-forge-ai-accent) 0%, var(--story-book-forge-ai-violet) 100%);
    box-shadow: 0 6px 16px rgb(79 70 229 / 35%);
    font-size: 1rem;
}

.sbfa-dashboard-brand-name {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-size: 0.95rem;
    font-weight: 700;
    letter-spacing: -0.01em;
    color: var(--story-book-forge-ai-header-text);
}

.sbfa-verify-banner {
    margin-bottom: 1rem;
    padding: 0.75rem 1rem;
    border: 1px solid rgb(199 154 59 / 40%);
    border-radius: var(--story-book-forge-ai-radius-sm);
    background: var(--story-book-forge-ai-gold-soft);
    color: var(--story-book-forge-ai-heading);
    font-size: 0.875rem;
    line-height: 1.5;
}

.sbfa-dashboard-footer {
    background: var(--story-book-forge-ai-surface-muted);
    border-top: 1px solid var(--story-book-forge-ai-border);
}

.sbfa-dashboard-footer-text {
    color: var(--story-book-forge-ai-text-muted);
}

.sbfa-dashboard-footer-link {
    color: var(--story-book-forge-ai-accent);
    font-weight: 600;
    text-decoration: none;
    transition: color var(--story-book-forge-ai-transition);
}

.sbfa-dashboard-footer-link:hover {
    color: var(--story-book-forge-ai-accent-hover);
}
</style>
