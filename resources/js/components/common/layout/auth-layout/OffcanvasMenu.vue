<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import OffCanvasMenuItems from '@/components/common/layout/auth-layout/OffCanvasMenuItems.vue'
import { useOffcanvasMenu } from '@/composables/useOffcanvasMenu'

import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faBars, faBookOpen, faXmark } from '@fortawesome/free-solid-svg-icons'

library.add(faBars, faBookOpen, faXmark)

const {
    authUser,
    mode = 'trigger'
} = defineProps({
    authUser: {
        type: Object,
        default: null
    },
    mode: {
        type: String,
        default: 'trigger',
        validator: (value) => ['trigger', 'sidebar'].includes(value)
    }
})

const isTriggerMode = computed(() => mode === 'trigger')
const isSidebarMode = computed(() => mode === 'sidebar')

const { isOffcanvasOpen, isMobile, closeOffcanvasMenu, toggleOffcanvasMenu } = useOffcanvasMenu()

const appName = import.meta.env.VITE_APP_NAME

const drawerId = computed(() => (isMobile.value ? 'auth-offcanvas-drawer' : 'auth-offcanvas-sidebar'))

const toggleBtnRef = ref(null)

const handleNavigate = () => {
    if (isMobile.value) {
        closeOffcanvasMenu()
    }
}

const syncBodyLock = () => {
    document.body.style.overflow = isOffcanvasOpen.value && isMobile.value ? 'hidden' : ''
}

const handleKeydown = (event) => {
    if (event.key === 'Escape' && isOffcanvasOpen.value) {
        closeOffcanvasMenu()
    }
}

watch(isOffcanvasOpen, (isOpen) => {
    syncBodyLock()

    if (!isOpen && isTriggerMode.value) {
        nextTick(() => toggleBtnRef.value?.focus())
    }
})

watch(isMobile, syncBodyLock)

onMounted(() => {
    if (isTriggerMode.value) {
        window.addEventListener('keydown', handleKeydown)
    }
})

onBeforeUnmount(() => {
    if (isTriggerMode.value) {
        window.removeEventListener('keydown', handleKeydown)
    }

    document.body.style.overflow = ''
})
</script>

<template>
    <template v-if="isTriggerMode">
        <button
            ref="toggleBtnRef"
            type="button"
            class="sbfa-offcanvas-toggle"
            :aria-expanded="isOffcanvasOpen"
            :aria-controls="drawerId"
            :aria-label="isOffcanvasOpen ? 'Close navigation menu' : 'Open navigation menu'"
            @click="toggleOffcanvasMenu"
        >
            <FontAwesomeIcon :icon="isOffcanvasOpen ? 'xmark' : 'bars'" class="sbfa-offcanvas-toggle-icon" />

            <span class="hidden sm:inline">{{ isOffcanvasOpen ? 'Close' : 'Menu' }}</span>
        </button>

        <Teleport to="body">
            <Transition name="offcanvas-fade">
                <div
                    v-if="isOffcanvasOpen && isMobile"
                    class="fixed inset-0 z-40 bg-black/50"
                    aria-hidden="true"
                    @click="closeOffcanvasMenu"
                />
            </Transition>

            <Transition name="offcanvas-slide">
                <aside
                    v-if="isOffcanvasOpen && isMobile"
                    id="auth-offcanvas-drawer"
                    class="sbfa-offcanvas-panel fixed top-0 left-0 z-50 h-full w-[82vw] max-w-sm"
                    aria-label="Navigation menu"
                >
                    <div class="sbfa-offcanvas-head">
                        <span class="sbfa-offcanvas-brand">
                            <span class="sbfa-offcanvas-mark" aria-hidden="true">
                                <FontAwesomeIcon :icon="['fas', 'book-open']" />
                            </span>

                            <span class="sbfa-offcanvas-brand-name">{{ appName }}</span>
                        </span>

                        <button
                            type="button"
                            class="sbfa-offcanvas-close"
                            aria-label="Close navigation menu"
                            @click="closeOffcanvasMenu"
                        >
                            <FontAwesomeIcon icon="xmark" />
                        </button>
                    </div>

                    <div class="sbfa-offcanvas-nav">
<OffCanvasMenuItems :auth-user="authUser" @navigate="handleNavigate" />
                    </div>

                    <div class="sbfa-offcanvas-foot">{{ appName }}</div>
                </aside>
            </Transition>
        </Teleport>
    </template>

    <aside
        v-if="isSidebarMode"
        id="auth-offcanvas-sidebar"
        class="sbfa-offcanvas-panel sbfa-offcanvas-sidebar sticky top-16 z-30 hidden flex-shrink-0 self-start md:flex"
        :class="isOffcanvasOpen ? 'is-open' : ''"
        aria-label="Navigation menu"
    >
        <div class="sbfa-offcanvas-head">
            <span class="sbfa-offcanvas-brand">
                <span class="sbfa-offcanvas-mark" aria-hidden="true">
                    <FontAwesomeIcon :icon="['fas', 'book-open']" />
                </span>

                <span class="sbfa-offcanvas-brand-name">{{ appName }}</span>
            </span>

            <button
                type="button"
                class="sbfa-offcanvas-close"
                aria-label="Close navigation menu"
                @click="closeOffcanvasMenu"
            >
                <FontAwesomeIcon icon="xmark" />
            </button>
        </div>

        <div class="sbfa-offcanvas-nav">
            <OffCanvasMenuItems :auth-user="authUser" @navigate="handleNavigate" />
        </div>

        <div class="sbfa-offcanvas-foot">{{ appName }}</div>
    </aside>
</template>

<style scoped>
.sbfa-offcanvas-toggle {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.55rem;
    min-width: 2.75rem;
    height: 2.75rem;
    padding: 0 0.9rem;
    border-radius: 12px;
    color: #fff;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.16);
    box-shadow: 0 2px 10px rgb(43 38 51 / 18%);
    font-size: 0.875rem;
    font-weight: 600;
    letter-spacing: 0.01em;
    cursor: pointer;
    transition:
        background var(--story-book-forge-ai-transition),
        border-color var(--story-book-forge-ai-transition),
        box-shadow var(--story-book-forge-ai-transition),
        transform var(--story-book-forge-ai-transition);
}

.sbfa-offcanvas-toggle:hover {
    background: rgba(255, 255, 255, 0.14);
    border-color: rgba(255, 255, 255, 0.26);
    transform: translateY(-1px);
}

.sbfa-offcanvas-toggle:active {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(0);
}

.sbfa-offcanvas-toggle:focus-visible {
    outline: 0;
    box-shadow: var(--story-book-forge-ai-focus-ring);
}

.sbfa-offcanvas-toggle-icon {
    font-size: 1rem;
}

.sbfa-offcanvas-panel {
    display: flex;
    flex-direction: column;
    background: var(--story-book-forge-ai-header-bg);
    border-right: 1px solid rgba(255, 255, 255, 0.12);
    box-shadow: 0 14px 40px rgb(43 38 51 / 32%);
    color: var(--story-book-forge-ai-header-text);
}

.sbfa-offcanvas-sidebar {
    align-self: flex-start;
    height: calc(100vh - 4rem);
    width: 0;
    min-width: 0;
    overflow: hidden;
    visibility: hidden;
    transition:
        width 260ms ease,
        box-shadow 260ms ease,
        visibility 0s linear 260ms;
}

.sbfa-offcanvas-sidebar.is-open {
    width: 18rem;
    visibility: visible;
    transition:
        width 260ms ease,
        box-shadow 260ms ease,
        visibility 0s;
}

.sbfa-offcanvas-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    padding: 1rem 1.1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.sbfa-offcanvas-brand {
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    min-width: 0;
}

.sbfa-offcanvas-mark {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2.1rem;
    height: 2.1rem;
    flex-shrink: 0;
    border-radius: 10px;
    color: #fff;
    background: linear-gradient(135deg, var(--story-book-forge-ai-accent) 0%, var(--story-book-forge-ai-violet) 100%);
    box-shadow: 0 5px 14px rgb(79 70 229 / 35%);
    font-size: 0.95rem;
}

.sbfa-offcanvas-brand-name {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-size: 0.9rem;
    font-weight: 700;
    letter-spacing: -0.01em;
    color: #fff;
}

.sbfa-offcanvas-close {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2.25rem;
    height: 2.25rem;
    flex-shrink: 0;
    border-radius: 10px;
    color: var(--story-book-forge-ai-header-text);
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.16);
    cursor: pointer;
    transition: background var(--story-book-forge-ai-transition), color var(--story-book-forge-ai-transition);
}

.sbfa-offcanvas-close:hover {
    background: rgba(255, 255, 255, 0.1);
}

.sbfa-offcanvas-close:focus-visible {
    outline: 0;
    box-shadow: var(--story-book-forge-ai-focus-ring);
}

.sbfa-offcanvas-nav {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    padding: 0.9rem 0.7rem 1.1rem;
}

.sbfa-offcanvas-foot {
    padding: 0.9rem 1.1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    color: var(--story-book-forge-ai-header-text-muted);
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.04em;
}

.offcanvas-fade-enter-active {
    transition: opacity 220ms ease;
}

.offcanvas-fade-leave-active {
    transition: opacity 160ms ease;
}

.offcanvas-fade-enter-from,
.offcanvas-fade-leave-to {
    opacity: 0;
}

.offcanvas-slide-enter-active {
    transition: transform 260ms ease;
}

.offcanvas-slide-leave-active {
    transition: transform 200ms ease;
}

.offcanvas-slide-enter-from,
.offcanvas-slide-leave-to {
    transform: translateX(-100%);
}

@media (prefers-reduced-motion: reduce) {
    .sbfa-offcanvas-sidebar {
        transition: none;
    }

    .sbfa-offcanvas-toggle,
    .offcanvas-fade-enter-active,
    .offcanvas-fade-leave-active,
    .offcanvas-slide-enter-active,
    .offcanvas-slide-leave-active {
        transition: none;
    }
}
</style>