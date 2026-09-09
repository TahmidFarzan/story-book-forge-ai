<script setup>
import AuthTopbarDropdownMenu from '@/components/common/layout/public-layout/AuthTopbarDropdownMenu.vue'
import FlashMessageToaster from '@/components/common/layout/FlashMessageToaster.vue'

import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { usePage } from '@inertiajs/vue3'

import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

import {
    faArrowRightToBracket,
    faBookOpen,
    faBars,
    faXmark,
} from '@fortawesome/free-solid-svg-icons'

library.add(
    faArrowRightToBracket,
    faBookOpen,
    faBars,
    faXmark
)

const page = usePage()

const appName = import.meta.env.VITE_APP_NAME
const appEnv = import.meta.env.VITE_APP_ENV

const showLoginUrl = appEnv !== 'production'

const authUser = computed(() => {
    return page.props.auth?.user ?? null
})

const flashMessage = computed(() => {
    return page.props.flashMessage
})

const year = new Date().getFullYear()

const homeUrl = route('home')

const navLinks = [
    { label: 'How It Works', href: route('home') + '#how-it-works' },
    { label: 'Features', href: route('home') + '#features' },
    { label: 'Preview', href: route('home') + '#preview' },
]

const mobileMenuOpen = ref(false)

function closeMobileMenu() {
    mobileMenuOpen.value = false
}

let desktopMedia

function handleDesktopBreakpoint(event) {
    if (event.matches) {
        mobileMenuOpen.value = false
    }
}

onMounted(() => {
    desktopMedia = window.matchMedia('(min-width: 64rem)')
    desktopMedia.addEventListener('change', handleDesktopBreakpoint)
})

onBeforeUnmount(() => {
    desktopMedia?.removeEventListener('change', handleDesktopBreakpoint)
})
</script>

<template>
    <div class="sbfa-layout flex min-h-screen flex-col">
        <header class="sbfa-header">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:h-[4.5rem]">
                <a :href="homeUrl" class="sbfa-logo group flex items-center gap-3" aria-label="Story Book Forge AI home">
                    <span class="sbfa-logo-mark" aria-hidden="true">
                        <FontAwesomeIcon :icon="['fas', 'book-open']" />
                    </span>
                    <span v-if="appName" class="sbfa-brand">
                        <span class="sbfa-brand-line-1">Story Book</span>
                        <span class="sbfa-brand-line-2">Forge AI</span>
                    </span>
                </a>

                <nav class="hidden items-center gap-1 lg:flex" aria-label="Primary navigation">
                    <a
                        v-for="link in navLinks"
                        :key="link.label"
                        :href="link.href"
                        class="sbfa-nav-link"
                    >
                        {{ link.label }}
                    </a>
                </nav>

                <div class="hidden items-center gap-3 lg:flex">
                    <div v-if="authUser">
                        <AuthTopbarDropdownMenu :auth-user="authUser" />
                    </div>

                    <a
                        v-else-if="showLoginUrl"
                        :href="route('login')"
                        class="sbfa-nav-btn"
                    >
                        <FontAwesomeIcon icon="arrow-right-to-bracket" class="text-sm" />
                        <span>Login</span>
                    </a>
                </div>

                <button
                    type="button"
                    class="sbfa-menu-toggle flex lg:hidden"
                    :aria-expanded="mobileMenuOpen"
                    aria-controls="mobile-nav"
                    :aria-label="mobileMenuOpen ? 'Close navigation menu' : 'Open navigation menu'"
                    @click="mobileMenuOpen = !mobileMenuOpen"
                >
                    <FontAwesomeIcon :icon="mobileMenuOpen ? 'xmark' : 'bars'" />
                </button>
            </div>

            <div
                id="mobile-nav"
                class="sbfa-mobile-nav lg:hidden"
                :class="{ 'is-open': mobileMenuOpen }"
            >
                <template v-if="mobileMenuOpen">
                    <a
                        v-for="link in navLinks"
                        :key="link.label"
                        :href="link.href"
                        class="sbfa-mobile-link"
                        @click="closeMobileMenu"
                    >
                        {{ link.label }}
                    </a>

                    <div class="sbfa-mobile-actions">
                        <div v-if="authUser">
                            <AuthTopbarDropdownMenu :auth-user="authUser" />
                        </div>

                        <a
                            v-else-if="showLoginUrl"
                            :href="route('login')"
                            class="sbfa-mobile-login"
                            @click="closeMobileMenu"
                        >
                            <FontAwesomeIcon icon="arrow-right-to-bracket" class="text-sm" />
                            <span>Login</span>
                        </a>
                    </div>
                </template>
            </div>
        </header>

        <main class="sbfa-main flex-1">
            <slot />
        </main>

        <footer class="sbfa-footer">
            <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 md:grid-cols-3">
                <div class="md:col-span-1">
                    <div class="flex items-center gap-3">
                        <span class="sbfa-footer-logo" aria-hidden="true">
                            <FontAwesomeIcon :icon="['fas', 'book-open']" />
                        </span>
                        <span class="sbfa-footer-brand">
                            <span class="block">Story Book</span>
                            <span class="block">Forge AI</span>
                        </span>
                    </div>
                    <p class="sbfa-footer-tagline mt-4 text-sm">
                        Turn your ideas into beautifully illustrated AI storybooks.
                    </p>
                </div>

                <nav class="md:col-span-1" aria-label="Footer navigation">
                    <h3 class="sbfa-footer-heading">Explore</h3>
                    <ul class="mt-3 space-y-2">
                        <li v-for="link in navLinks" :key="link.label">
                            <a :href="link.href" class="sbfa-footer-link">{{ link.label }}</a>
                        </li>
                    </ul>
                </nav>

                <div class="md:col-span-1">
                    <h3 class="sbfa-footer-heading">Account</h3>
                    <ul class="mt-3 space-y-2">
                        <li v-if="showLoginUrl && !authUser">
                            <a :href="route('login')" class="sbfa-footer-link">Login</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="sbfa-footer-bottom">
                <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-2 px-4 py-5 text-sm sm:flex-row sm:px-6">
                    <span class="sbfa-footer-text">
                        &copy; {{ year }} {{ appName }}. All rights reserved.
                    </span>

                    <span class="sbfa-footer-text">
                        Developed by
                        <a
                            href="https://www.linkedin.com/in/sk-md-tahmid-farzan/"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="sbfa-footer-link"
                        >
                            Sk. Md. Tahmid Farzan
                        </a>
                    </span>
                </div>
            </div>
        </footer>

        <FlashMessageToaster :flash-message="flashMessage" />
    </div>
</template>

<style scoped>
.sbfa-layout {
    font-family: var(--story-book-forge-ai-font);
    background: var(--story-book-forge-ai-background);
    color: var(--story-book-forge-ai-text);
    text-rendering: optimizeLegibility;
    -webkit-font-smoothing: antialiased;
}

.sbfa-layout ::selection {
    background: var(--story-book-forge-ai-accent-soft);
    color: var(--story-book-forge-ai-accent-active);
}

.sbfa-layout :deep(a:focus-visible),
.sbfa-layout :deep(button:focus-visible) {
    outline: 0;
    box-shadow: var(--story-book-forge-ai-focus-ring);
    border-radius: var(--story-book-forge-ai-radius-sm);
}

.sbfa-header {
    position: sticky;
    top: 0;
    z-index: 50;
    background: var(--story-book-forge-ai-header-bg);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 4px 24px rgb(43 38 51 / 18%);
}

.sbfa-logo {
    text-decoration: none;
}

.sbfa-logo-mark {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 12px;
    color: #fff;
    background: linear-gradient(135deg, var(--story-book-forge-ai-accent) 0%, var(--story-book-forge-ai-violet) 100%);
    box-shadow: 0 6px 16px rgb(79 70 229 / 35%);
    font-size: 1.05rem;
    transition: transform var(--story-book-forge-ai-transition);
}

.sbfa-logo:hover .sbfa-logo-mark {
    transform: translateY(-1px);
}

.sbfa-brand {
    line-height: 1.1;
    letter-spacing: -0.01em;
    color: var(--story-book-forge-ai-header-text);
}

.sbfa-brand-line-1 {
    display: block;
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--story-book-forge-ai-header-text-muted);
}

.sbfa-brand-line-2 {
    display: block;
    font-size: 1.05rem;
    font-weight: 700;
    color: #fff;
}

.sbfa-nav-link {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 0.85rem;
    border-radius: var(--story-book-forge-ai-radius-sm);
    font-size: 0.9rem;
    font-weight: 500;
    color: var(--story-book-forge-ai-header-text-muted);
    text-decoration: none;
    transition: color var(--story-book-forge-ai-transition), background var(--story-book-forge-ai-transition);
}

.sbfa-nav-link:hover {
    color: #fff;
    background: rgba(255, 255, 255, 0.1);
}

.sbfa-nav-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: var(--story-book-forge-ai-radius-sm);
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--story-book-forge-ai-accent);
    background: #fff;
    border: 1px solid transparent;
    box-shadow: 0 2px 10px rgb(43 38 51 / 12%);
    text-decoration: none;
    white-space: nowrap;
    transition: all var(--story-book-forge-ai-transition);
}

.sbfa-nav-btn:hover {
    color: var(--story-book-forge-ai-accent-hover);
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgb(43 38 51 / 16%);
}

.sbfa-menu-toggle {
    align-items: center;
    justify-content: center;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 10px;
    color: #fff;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
    font-size: 1.15rem;
    cursor: pointer;
    transition: background var(--story-book-forge-ai-transition);
}

.sbfa-menu-toggle:hover {
    background: rgba(255, 255, 255, 0.16);
}

.sbfa-mobile-nav {
    max-height: 0;
    overflow: hidden;
    transition: max-height 220ms ease;
    background: var(--story-book-forge-ai-header-bg);
}

.sbfa-mobile-nav.is-open {
    max-height: 30rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.sbfa-mobile-link {
    display: flex;
    align-items: center;
    padding: 0.85rem 1.25rem;
    font-size: 0.95rem;
    font-weight: 500;
    color: var(--story-book-forge-ai-header-text-muted);
    text-decoration: none;
    transition: color var(--story-book-forge-ai-transition), background var(--story-book-forge-ai-transition);
}

.sbfa-mobile-link:hover {
    color: #fff;
    background: rgba(255, 255, 255, 0.08);
}

.sbfa-mobile-actions {
    padding: 0.75rem 1.25rem 1.25rem;
}

.sbfa-mobile-login {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 1.1rem;
    border-radius: var(--story-book-forge-ai-radius-sm);
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--story-book-forge-ai-accent);
    background: #fff;
    text-decoration: none;
}

.sbfa-main {
    flex: 1;
}

.sbfa-footer {
    background: var(--story-book-forge-ai-surface-muted);
    border-top: 1px solid var(--story-book-forge-ai-border);
}

.sbfa-footer-logo {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2.25rem;
    height: 2.25rem;
    border-radius: 10px;
    color: #fff;
    background: linear-gradient(135deg, var(--story-book-forge-ai-accent) 0%, var(--story-book-forge-ai-violet) 100%);
    font-size: 0.95rem;
}

.sbfa-footer-brand {
    font-size: 0.95rem;
    font-weight: 700;
    line-height: 1.2;
    color: var(--story-book-forge-ai-heading);
    letter-spacing: -0.01em;
}

.sbfa-footer-tagline {
    color: var(--story-book-forge-ai-text-muted);
    line-height: 1.6;
}

.sbfa-footer-heading {
    font-size: 0.8125rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--story-book-forge-ai-text-muted);
}

.sbfa-footer-link {
    color: var(--story-book-forge-ai-text);
    font-size: 0.9rem;
    font-weight: 500;
    text-decoration: none;
    transition: color var(--story-book-forge-ai-transition);
}

.sbfa-footer-link:hover {
    color: var(--story-book-forge-ai-accent);
}

.sbfa-footer-bottom {
    border-top: 1px solid var(--story-book-forge-ai-border);
}

.sbfa-footer-text {
    color: var(--story-book-forge-ai-text-muted);
}
</style>
