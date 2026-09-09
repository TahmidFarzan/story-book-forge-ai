<script setup>
import { ref, computed, watch, nextTick, onMounted, onBeforeUnmount } from 'vue'
import { router as inertia } from '@inertiajs/vue3'

import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import {
    faUser,
    faGauge,
    faUserGear,
    faRightFromBracket,
    faXmark,
    faSpinner,
    faChevronDown,
    faChevronUp
} from '@fortawesome/free-solid-svg-icons'

library.add(
    faUser,
    faGauge,
    faUserGear,
    faRightFromBracket,
    faXmark,
    faSpinner,
    faChevronDown,
    faChevronUp
)

const { authUser } = defineProps({
    authUser: {
        type: Object,
        required: true
    }
})

const dropdownRef = ref(null)
const panelRef = ref(null)

const showDropdown = ref(false)
const showLogoutModal = ref(false)
const loggingOut = ref(false)

const panelStyle = ref(null)

const avatarUrl = computed(() => authUser?.profile_image?.preview_url || '/uploads/icons/auth/user.png')

const closeDropdown = () => {
    showDropdown.value = false
}

const positionPanel = () => {
    if (!showDropdown.value || !dropdownRef.value || typeof window === 'undefined') return

    const rect = dropdownRef.value.getBoundingClientRect()

    panelStyle.value = {
        top: `${rect.bottom + 8}px`,
        right: `${Math.max(12, window.innerWidth - rect.right)}px`
    }
}

watch(showDropdown, (isOpen) => {
    if (isOpen) {
        nextTick(positionPanel)
    }
})

const handleClickOutside = (event) => {
    const isInsideTrigger = dropdownRef.value && dropdownRef.value.contains(event.target)
    const isInsidePanel = panelRef.value && panelRef.value.contains(event.target)

    if (!isInsideTrigger && !isInsidePanel) {
        closeDropdown()
    }
}

const handleKeydown = (event) => {
    if (event.key === 'Escape') {
        closeDropdown()
        closeLogoutModal()
    }
}

const openLogoutModal = () => {
    showLogoutModal.value = true
    closeDropdown()
}

const closeLogoutModal = () => {
    if (loggingOut.value) return

    showLogoutModal.value = false
}

const logoutHandler = () => {
    if (loggingOut.value) return

    loggingOut.value = true

    inertia.post(route('logout'), {}, {
        onFinish: () => {
            loggingOut.value = false
            showLogoutModal.value = false
        }
    })
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
    document.addEventListener('keydown', handleKeydown)
    window.addEventListener('scroll', positionPanel, { capture: true, passive: true })
    window.addEventListener('resize', positionPanel)
})

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside)
    document.removeEventListener('keydown', handleKeydown)
    window.removeEventListener('scroll', positionPanel, { capture: true, passive: true })
    window.removeEventListener('resize', positionPanel)
})
</script>

<template>
    <div ref="dropdownRef">
        <button type="button" class="sbfa-user-trigger" @click.stop="showDropdown = !showDropdown"
            :aria-label="`${authUser?.name ?? 'User'} menu`" aria-haspopup="true" :aria-expanded="showDropdown">
            <img v-if="authUser?.profile_image?.preview_url" :src="avatarUrl" :alt="authUser?.name"
                class="h-7 w-7 rounded-full object-cover" />

            <span v-else class="sbfa-user-trigger-avatar">
                <FontAwesomeIcon icon="user" />
            </span>

            <span v-if="authUser?.name" class="sbfa-user-trigger-name hidden sm:inline">
                {{ authUser.name }}
            </span>

            <FontAwesomeIcon :icon="showDropdown ? 'chevron-up' : 'chevron-down'" class="sbfa-user-trigger-chevron" />
        </button>
    </div>

    <Teleport to="body">
        <Transition name="user-menu">
            <div v-if="showDropdown" ref="panelRef" :style="panelStyle" class="sbfa-user-menu" role="menu"
                :aria-label="`${authUser?.name ?? 'User'} menu`">
                <div class="sbfa-user-menu-header">
                    <img v-if="authUser?.profile_image?.preview_url" :src="avatarUrl" :alt="authUser?.name"
                        class="sbfa-user-menu-avatar" />

                    <span v-else class="sbfa-user-menu-avatar is-fallback">
                        <FontAwesomeIcon icon="user" />
                    </span>

                    <span class="min-w-0">
                        <span v-if="authUser?.name" class="sbfa-user-menu-name">
                            {{ authUser.name }}
                        </span>

                        <span v-if="authUser?.email" class="sbfa-user-menu-meta">
                            {{ authUser.email }}
                        </span>
                    </span>
                </div>

                <div class="sbfa-user-menu-divider"></div>

                <a role="menuitem" :href="route('auth-user.dashboard.index')" @click="closeDropdown"
                    class="sbfa-user-menu-item">
                    <FontAwesomeIcon icon="gauge" class="sbfa-user-menu-item-icon" />
                    <span>Dashboard</span>
                </a>

                <a role="menuitem" :href="route('auth-user.profile.index')" @click="closeDropdown"
                    class="sbfa-user-menu-item">
                    <FontAwesomeIcon icon="user" class="sbfa-user-menu-item-icon" />
                    <span>Profile</span>
                </a>

                <a role="menuitem" :href="route('auth-user.account.index')" @click="closeDropdown"
                    class="sbfa-user-menu-item">
                    <FontAwesomeIcon icon="user-gear" class="sbfa-user-menu-item-icon" />
                    <span>Account</span>
                </a>

                <div class="sbfa-user-menu-divider"></div>

                <button role="menuitem" type="button" class="sbfa-user-menu-item is-danger"
                    @click="openLogoutModal">
                    <FontAwesomeIcon icon="right-from-bracket" class="sbfa-user-menu-item-icon" />
                    <span>Logout</span>
                </button>
            </div>
        </Transition>
    </Teleport>

    <Teleport to="body">
        <Transition name="user-modal-fade">
            <div v-if="authUser && showLogoutModal" class="user-modal-overlay" @click.self="closeLogoutModal">
                <Transition name="user-modal-pop">
                    <div class="user-modal-card">
                        <div class="user-modal-title">
                            <FontAwesomeIcon icon="right-from-bracket" />
                            <span>Confirm Logout</span>
                        </div>

                        <p class="user-modal-copy">
                            Are you sure you want to logout?
                        </p>

                        <div class="user-modal-actions">
                            <button type="button" class="user-modal-cancel" @click="closeLogoutModal"
                                :disabled="loggingOut">
                                <FontAwesomeIcon icon="xmark" />
                                <span>Cancel</span>
                            </button>

                            <button type="button" class="user-modal-confirm" @click="logoutHandler"
                                :disabled="loggingOut">
                                <FontAwesomeIcon v-if="!loggingOut" icon="right-from-bracket" />

                                <FontAwesomeIcon v-else icon="spinner" spin />

                                <span>Logout</span>
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.sbfa-user-trigger {
    display: inline-flex;
    align-items: center;
    gap: 0.55rem;
    min-width: 2.5rem;
    height: 2.5rem;
    padding: 0 0.6rem 0 0.4rem;
    border-radius: 999px;
    color: var(--story-book-forge-ai-header-text);
    background: rgb(255 255 255 / 8%);
    border: 1px solid rgb(255 255 255 / 16%);
    box-shadow: 0 2px 10px rgb(43 38 51 / 16%);
    cursor: pointer;
    transition: background var(--story-book-forge-ai-transition),
        border-color var(--story-book-forge-ai-transition),
        box-shadow var(--story-book-forge-ai-transition),
        transform var(--story-book-forge-ai-transition);
}

.sbfa-user-trigger:hover {
    background: rgb(255 255 255 / 14%);
    border-color: rgb(255 255 255 / 26%);
    transform: translateY(-1px);
}

.sbfa-user-trigger:active {
    transform: translateY(0);
}

.sbfa-user-trigger:focus-visible {
    outline: 0;
    box-shadow: var(--story-book-forge-ai-focus-ring);
}

.sbfa-user-trigger-avatar {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 1.9rem;
    height: 1.9rem;
    border-radius: 50%;
    color: #fff;
    font-size: 0.8rem;
    background: linear-gradient(135deg, var(--story-book-forge-ai-accent), var(--story-book-forge-ai-violet));
    flex-shrink: 0;
}

.sbfa-user-trigger-name {
    max-width: 7.5rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--story-book-forge-ai-header-text);
}

.sbfa-user-trigger-chevron {
    font-size: 0.625rem;
    color: var(--story-book-forge-ai-header-text-muted);
    transition: color var(--story-book-forge-ai-transition);
}

.sbfa-user-trigger:hover .sbfa-user-trigger-chevron {
    color: var(--story-book-forge-ai-header-text);
}

.sbfa-user-menu {
    position: fixed;
    width: 15.5rem;
    padding: 0.5rem;
    border-radius: 14px;
    background: var(--story-book-forge-ai-surface);
    border: 1px solid var(--story-book-forge-ai-border);
    box-shadow: var(--story-book-forge-ai-shadow-lg);
    transform-origin: top right;
    z-index: 50;
}

.sbfa-user-menu-header {
    display: flex;
    align-items: center;
    gap: 0.7rem;
    padding: 0.65rem 0.7rem 0.8rem;
}

.sbfa-user-menu-avatar {
    width: 2.4rem;
    height: 2.4rem;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
    box-shadow: 0 3px 10px rgb(43 38 51 / 14%);
}

.sbfa-user-menu-avatar.is-fallback {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1rem;
    background: linear-gradient(135deg, var(--story-book-forge-ai-accent), var(--story-book-forge-ai-violet));
}

.sbfa-user-menu-name {
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-size: 0.875rem;
    font-weight: 700;
    letter-spacing: -0.01em;
    color: var(--story-book-forge-ai-heading);
}

.sbfa-user-menu-meta {
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-size: 0.75rem;
    color: var(--story-book-forge-ai-text-muted);
}

.sbfa-user-menu-divider {
    height: 1px;
    margin: 0.375rem 0;
    background: var(--story-book-forge-ai-border);
}

.sbfa-user-menu-item {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    width: 100%;
    padding: 0.55rem 0.75rem;
    border-radius: 9px;
    border: 0;
    background: transparent;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--story-book-forge-ai-text);
    text-align: left;
    text-decoration: none;
    cursor: pointer;
    transition: background var(--story-book-forge-ai-transition),
        color var(--story-book-forge-ai-transition);
}

.sbfa-user-menu-item:hover {
    background: var(--story-book-forge-ai-accent-soft);
    color: var(--story-book-forge-ai-accent);
}

.sbfa-user-menu-item:focus-visible {
    outline: 0;
    box-shadow: var(--story-book-forge-ai-focus-ring);
}

.sbfa-user-menu-item-icon {
    width: 1rem;
    flex-shrink: 0;
    font-size: 0.875rem;
    color: var(--story-book-forge-ai-text-muted);
    transition: color var(--story-book-forge-ai-transition);
}

.sbfa-user-menu-item:hover .sbfa-user-menu-item-icon {
    color: var(--story-book-forge-ai-accent);
}

.sbfa-user-menu-item.is-danger {
    color: var(--story-book-forge-ai-danger);
}

.sbfa-user-menu-item.is-danger:hover {
    background: rgb(220 76 100 / 10%);
    color: var(--story-book-forge-ai-danger);
}

.sbfa-user-menu-item.is-danger:hover .sbfa-user-menu-item-icon {
    color: var(--story-book-forge-ai-danger);
}

.user-modal-overlay {
    display: flex;
    align-items: center;
    justify-content: center;
    position: fixed;
    inset: 0;
    background: rgb(24 20 30 / 55%);
    z-index: 50;
}

.user-modal-card {
    width: 20rem;
    max-width: calc(100vw - 2.5rem);
    padding: 1.25rem;
    border-radius: 14px;
    background: var(--story-book-forge-ai-surface);
    border: 1px solid var(--story-book-forge-ai-border);
    box-shadow: var(--story-book-forge-ai-shadow-lg);
}

.user-modal-title {
    display: flex;
    align-items: center;
    gap: 0.55rem;
    margin-bottom: 0.75rem;
    font-size: 0.9375rem;
    font-weight: 700;
    color: var(--story-book-forge-ai-danger);
}

.user-modal-copy {
    margin-bottom: 1.25rem;
    font-size: 0.875rem;
    color: var(--story-book-forge-ai-text-muted);
}

.user-modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 0.6rem;
}

.user-modal-cancel,
.user-modal-confirm {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.45rem 0.9rem;
    border-radius: 9px;
    border: 0;
    font-size: 0.8125rem;
    font-weight: 600;
    cursor: pointer;
    transition: background var(--story-book-forge-ai-transition),
        color var(--story-book-forge-ai-transition),
        box-shadow var(--story-book-forge-ai-transition);
}

.user-modal-cancel:disabled,
.user-modal-confirm:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.user-modal-cancel {
    color: var(--story-book-forge-ai-text);
    background: var(--story-book-forge-ai-surface-muted);
    border: 1px solid var(--story-book-forge-ai-border);
}

.user-modal-cancel:hover {
    background: var(--story-book-forge-ai-gold-soft);
}

.user-modal-cancel:focus-visible,
.user-modal-confirm:focus-visible {
    outline: 0;
    box-shadow: var(--story-book-forge-ai-focus-ring);
}

.user-modal-confirm {
    color: #fff;
    background: var(--story-book-forge-ai-danger);
}

.user-modal-confirm:hover {
    background: #c03a52;
}

.user-menu-enter-active {
    transition: opacity var(--story-book-forge-ai-transition),
        transform var(--story-book-forge-ai-transition);
}

.user-menu-enter-from,
.user-menu-leave-to {
    opacity: 0;
    transform: translateY(6px) scale(0.97);
}

.user-menu-leave-active {
    transition: opacity 110ms ease, transform 110ms ease;
}

.user-modal-fade-enter-active,
.user-modal-fade-leave-active {
    transition: opacity 180ms ease;
}

.user-modal-fade-enter-from,
.user-modal-fade-leave-to {
    opacity: 0;
}

.user-modal-pop-enter-active {
    transition: opacity 200ms ease, transform 200ms ease;
}

.user-modal-pop-enter-from {
    opacity: 0;
    transform: translateY(8px) scale(0.97);
}

.user-modal-pop-leave-active {
    transition: opacity 140ms ease, transform 140ms ease;
}

.user-modal-pop-leave-to {
    opacity: 0;
    transform: translateY(4px) scale(0.99);
}

@media (prefers-reduced-motion: reduce) {
    .sbfa-user-trigger,
    .sbfa-user-trigger-chevron,
    .sbfa-user-menu-item,
    .sbfa-user-menu-item-icon,
    .user-modal-cancel,
    .user-modal-confirm {
        transition: none;
    }

    .user-menu-enter-active,
    .user-menu-leave-active,
    .user-modal-fade-enter-active,
    .user-modal-fade-leave-active,
    .user-modal-pop-enter-active,
    .user-modal-pop-leave-active {
        transition: none !important;
    }
}
</style>