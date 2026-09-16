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
    <div class="public-layout flex min-h-screen flex-col">
        <header class="header sticky top-0 z-50 border-b border-white/10">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:h-[4.5rem]">
                <a :href="homeUrl" class="logo group flex items-center gap-3 no-underline" aria-label="Story Book Forge AI home">
                    <span class="logo-mark inline-flex h-10 w-10 items-center justify-center rounded-xl text-[1.05rem] text-white" aria-hidden="true">
                        <FontAwesomeIcon :icon="['fas', 'book-open']" />
                    </span>
                    <span v-if="appName" class="brand leading-[1.1] tracking-[-0.01em]">
                        <span class="brand-line-1 block text-[0.8rem] font-semibold">Story Book</span>
                        <span class="brand-line-2 block text-[1.05rem] font-bold text-white">Forge AI</span>
                    </span>
                </a>

                <nav class="hidden items-center gap-1 lg:flex" aria-label="Primary navigation">
                    <a
                        v-for="link in navLinks"
                        :key="link.label"
                        :href="link.href"
                        class="nav-link inline-flex items-center rounded-lg py-2 px-[0.85rem] text-[0.9rem] font-medium no-underline transition-colors hover:bg-white/10 hover:text-white"
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
                        class="nav-btn inline-flex items-center gap-2 whitespace-nowrap rounded-lg border border-transparent bg-white px-4 py-2 text-sm font-semibold no-underline transition hover:-translate-y-px"
                    >
                        <FontAwesomeIcon icon="arrow-right-to-bracket" class="text-sm" />
                        <span>Login</span>
                    </a>
                </div>

                <button
                    type="button"
                    class="menu-toggle flex h-10 w-10 cursor-pointer items-center justify-center rounded-[10px] border border-white/[0.12] bg-white/[0.08] text-[1.15rem] text-white transition-colors hover:bg-white/[0.16] lg:hidden"
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
                class="mobile-nav lg:hidden"
                :class="{ 'is-open': mobileMenuOpen }"
            >
                <template v-if="mobileMenuOpen">
                    <a
                        v-for="link in navLinks"
                        :key="link.label"
                        :href="link.href"
                        class="mobile-link flex items-center px-5 py-[0.85rem] text-[0.95rem] font-medium no-underline transition-colors hover:bg-white/[0.08] hover:text-white"
                        @click="closeMobileMenu"
                    >
                        {{ link.label }}
                    </a>

                    <div class="mobile-actions px-5 pb-5 pt-3">
                        <div v-if="authUser">
                            <AuthTopbarDropdownMenu :auth-user="authUser" />
                        </div>

                        <a
                            v-else-if="showLoginUrl"
                            :href="route('login')"
                            class="mobile-login inline-flex items-center gap-2 rounded-lg bg-white px-[1.1rem] py-[0.6rem] text-sm font-semibold no-underline"
                            @click="closeMobileMenu"
                        >
                            <FontAwesomeIcon icon="arrow-right-to-bracket" class="text-sm" />
                            <span>Login</span>
                        </a>
                    </div>
                </template>
            </div>
        </header>

        <main class="main flex-1">
            <slot />
        </main>

        <footer class="footer">
            <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 md:grid-cols-3">
                <div class="md:col-span-1">
                    <div class="flex items-center gap-3">
                        <span class="footer-logo inline-flex h-9 w-9 items-center justify-center rounded-[10px] text-[0.95rem] text-white" aria-hidden="true">
                            <FontAwesomeIcon :icon="['fas', 'book-open']" />
                        </span>
                        <span class="footer-brand text-[0.95rem] font-bold leading-[1.2] tracking-[-0.01em]">
                            <span class="block">Story Book</span>
                            <span class="block">Forge AI</span>
                        </span>
                    </div>
                    <p class="footer-tagline mt-4 text-sm leading-[1.6]">
                        Turn your ideas into beautifully illustrated AI storybooks.
                    </p>
                </div>

                <nav class="md:col-span-1" aria-label="Footer navigation">
                    <h3 class="footer-heading text-[0.8125rem] font-bold uppercase tracking-[0.06em]">Explore</h3>
                    <ul class="mt-3 space-y-2">
                        <li v-for="link in navLinks" :key="link.label">
                            <a :href="link.href" class="footer-link text-[0.9rem] font-medium no-underline transition-colors">{{ link.label }}</a>
                        </li>
                    </ul>
                </nav>

                <div class="md:col-span-1">
                    <h3 class="footer-heading text-[0.8125rem] font-bold uppercase tracking-[0.06em]">Account</h3>
                    <ul class="mt-3 space-y-2">
                        <li v-if="showLoginUrl && !authUser">
                            <a :href="route('login')" class="footer-link text-[0.9rem] font-medium no-underline transition-colors">Login</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-2 px-4 py-5 text-sm sm:flex-row sm:px-6">
                    <span class="footer-text">
                        &copy; {{ year }} {{ appName }}. All rights reserved.
                    </span>

                    <span class="footer-text">
                        Developed by
                        <a
                            href="https://www.linkedin.com/in/sk-md-tahmid-farzan/"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="footer-link text-[0.9rem] font-medium no-underline transition-colors"
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
.public-layout :deep(a:focus-visible),
.public-layout :deep(button:focus-visible) {
    outline: 0;
    box-shadow: var(--focus-ring);
    border-radius: var(--radius-sm);
}

.public-layout .header {
    background: var(--header-bg);
    box-shadow: 0 4px 24px rgb(43 38 51 / 18%);
}

.public-layout .logo .logo-mark {
    background: linear-gradient(135deg, var(--accent) 0%, var(--violet) 100%);
    box-shadow: 0 6px 16px rgb(79 70 229 / 35%);
    transition: transform var(--transition);
}

.public-layout .logo:hover .logo-mark {
    transform: translateY(-1px);
}

.public-layout .brand {
    color: var(--header-text);
}

.public-layout .brand .brand-line-1 {
    color: var(--header-text-muted);
}

.public-layout .nav-link {
    color: var(--header-text-muted);
}

.public-layout .nav-btn {
    color: var(--accent);
    box-shadow: 0 2px 10px rgb(43 38 51 / 12%);
}

.public-layout .nav-btn:hover {
    color: var(--accent-hover);
    box-shadow: 0 4px 14px rgb(43 38 51 / 16%);
}

.public-layout .mobile-nav {
    max-height: 0;
    overflow: hidden;
    transition: max-height 220ms ease;
    background: var(--header-bg);
}

.public-layout .mobile-nav.is-open {
    max-height: 30rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.public-layout .mobile-link {
    color: var(--header-text-muted);
}

.public-layout .mobile-login {
    color: var(--accent);
}

.public-layout .footer {
    background: var(--surface-muted);
    border-top: 1px solid var(--border);
}

.public-layout .footer .footer-logo {
    background: linear-gradient(135deg, var(--accent) 0%, var(--violet) 100%);
}

.public-layout .footer .footer-brand {
    color: var(--heading);
}

.public-layout .footer .footer-tagline {
    color: var(--text-muted);
}

.public-layout .footer .footer-heading {
    color: var(--text-muted);
}

.public-layout .footer .footer-link {
    color: var(--text);
}

.public-layout .footer .footer-link:hover {
    color: var(--accent);
}

.public-layout .footer .footer-bottom {
    border-top: 1px solid var(--border);
}

.public-layout .footer .footer-text {
    color: var(--text-muted);
}
</style>