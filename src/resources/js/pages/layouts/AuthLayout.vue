<script setup>
import OffCanvasMenu from '@/components/common/layout/auth-layout/OffCanvasMenu.vue'
import Breadcrumbs from '@/components/common/layout/auth-layout/Breadcrumbs.vue'
import AuthTopbarDropdownMenu from '@/components/common/layout/auth-layout/AuthTopbarDropdownMenu.vue'
import FlashMessageToaster from '@/components/common/layout/FlashMessageToaster.vue'

import { router, usePage } from '@inertiajs/vue3'
import { computed, onBeforeUnmount, onMounted, provide, ref } from 'vue'

import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faBookOpen } from '@fortawesome/free-solid-svg-icons'

library.add(faBookOpen)

const appName = import.meta.env.VITE_APP_NAME

const page = usePage()

const authUser = computed(() => page.props.auth?.user ?? null)
const flashMessage = computed(() => page.props.flashMessage ?? null)

provide('authUser', authUser)

const navigating = ref(false)
const navigationCleanups = []

const handleNavigationStart = () => {
    navigating.value = true
}

const handleNavigationEnd = () => {
    navigating.value = false
}

onMounted(() => {
    navigationCleanups.push(router.on('start', handleNavigationStart))
    navigationCleanups.push(router.on('finish', handleNavigationEnd))
    navigationCleanups.push(router.on('error', handleNavigationEnd))
    navigationCleanups.push(router.on('networkError', handleNavigationEnd))
    navigationCleanups.push(router.on('httpException', handleNavigationEnd))
})

onBeforeUnmount(() => {
    navigationCleanups.forEach((remove) => remove())
})
</script>

<template>
    <div class="auth-layout flex min-h-screen flex-col">
        <Transition
            enter-active-class="transition-opacity duration-200 ease-out motion-reduce:transition-none"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-200 ease-in motion-reduce:transition-none"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="navigating"
                class="pointer-events-none fixed inset-x-0 top-0 z-[60] h-0.5 overflow-hidden"
                aria-hidden="true"
            >
                <div
                    class="h-full w-2/5 animate-nav-progress rounded-full bg-gradient-to-r from-[var(--accent)] to-[var(--violet)] shadow-[0_0_10px_rgba(79,70,229,0.4)] motion-reduce:animate-none"
                ></div>
            </div>
        </Transition>

        <header class="header fixed inset-x-0 top-0 z-50 border-b border-white/10">
            <div class="mx-auto flex h-16 w-full max-w-[100rem] items-center justify-between gap-4 px-4 sm:px-6">
                <a :href="route('home')" class="brand inline-flex min-w-0 items-center gap-3 no-underline" :aria-label="appName">
                    <span class="brand-mark inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-[10px] text-base text-white" aria-hidden="true">
                        <FontAwesomeIcon :icon="['fas', 'book-open']" />
                    </span>

                    <span v-if="appName" class="brand-name truncate text-[0.95rem] font-bold tracking-[-0.01em]">{{ appName }}</span>
                </a>

                <div class="flex items-center gap-3">
                    <OffCanvasMenu mode="trigger" :auth-user="authUser" />

                    <AuthTopbarDropdownMenu :auth-user="authUser" />
                </div>
            </div>
        </header>

        <main class="flex flex-1 pt-16">
            <OffCanvasMenu mode="sidebar" :auth-user="authUser" />

            <div class="flex-1 min-w-0">
                <Transition mode="out-in"
                        enter-active-class="transition duration-200 ease-out motion-reduce:transition-none"
                        enter-from-class="opacity-0 translate-y-1.5 motion-reduce:opacity-100 motion-reduce:translate-y-0"
                        enter-to-class="opacity-100 translate-y-0 motion-reduce:opacity-100 motion-reduce:translate-y-0"
                        leave-active-class="transition duration-150 ease-in motion-reduce:transition-none"
                        leave-from-class="opacity-100 translate-y-0 motion-reduce:opacity-100 motion-reduce:translate-y-0"
                        leave-to-class="opacity-0 -translate-y-1.5 motion-reduce:opacity-100 motion-reduce:translate-y-0">
                    <div :key="page.component" class="p-4 sm:p-6">
                        <Breadcrumbs />

                        <div v-if="authUser && !authUser.email_verified_at" class="verify-banner mb-4 rounded-lg px-4 py-3 text-sm leading-normal" role="note">
                            Please verify your email address.
                        </div>

                        <slot />
                    </div>
                </Transition>
            </div>
        </main>

        <footer class="footer">
            <div class="mx-auto flex w-full max-w-[100rem] flex-col items-center justify-between gap-2 px-4 py-5 text-sm sm:flex-row sm:px-6">
                <span class="footer-text">
                    &copy; {{ new Date().getFullYear() }} {{ appName }}. All rights reserved.
                </span>

                <span class="footer-text">
                    Developed by
                    <a
                        href="https://www.linkedin.com/in/sk-md-tahmid-farzan/"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="footer-link font-semibold no-underline transition-colors"
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
.auth-layout :deep(a:focus-visible),
.auth-layout :deep(button:focus-visible),
.auth-layout :deep(input:focus-visible),
.auth-layout :deep(select:focus-visible),
.auth-layout :deep(textarea:focus-visible) {
    outline: 0;
    box-shadow: var(--focus-ring);
    border-radius: var(--radius-sm);
}

.auth-layout .header {
    background: var(--header-bg);
    box-shadow: 0 4px 24px rgb(43 38 51 / 18%);
}

.auth-layout .brand .brand-mark {
    background: linear-gradient(135deg, var(--accent) 0%, var(--violet) 100%);
    box-shadow: 0 6px 16px rgb(79 70 229 / 35%);
}

.auth-layout .brand .brand-name {
    color: var(--header-text);
}

.auth-layout .verify-banner {
    border: 1px solid rgb(199 154 59 / 40%);
    background: var(--gold-soft);
    color: var(--heading);
}

.auth-layout .footer {
    background: var(--surface-muted);
    border-top: 1px solid var(--border);
}

.auth-layout .footer .footer-text {
    color: var(--text-muted);
}

.auth-layout .footer .footer-link {
    color: var(--accent);
}

.auth-layout .footer .footer-link:hover {
    color: var(--accent-hover);
}
</style>
