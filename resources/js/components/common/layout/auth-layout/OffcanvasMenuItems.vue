<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

import {
    faUser,
    faUsers,
    faChevronDown,
    faChevronUp,
    faGauge,
    faPhotoFilm,
    faBrain,
} from '@fortawesome/free-solid-svg-icons'

library.add(
    faUser,
    faUsers,
    faChevronDown,
    faChevronUp,
    faGauge,
    faPhotoFilm,
    faBrain
)

import {
    canAccessUser,
    canAccessAiBrain,
} from '@/composables/useUserPermissions'

const {
    authUser
} = defineProps({
    authUser: {
        type: Object,
        default: null
    }
})

const emit = defineEmits(['navigate'])

const page = usePage()

const subMenus = ref({
    UserManagement: false,
    AiManagement: false,
})

const routeMap = {
    UserManagement: ['/back-office/users/*'],
    AiManagement: ['/back-office/ai-brains/*'],
}

const canAccessUserComputed = computed(() => {
    return canAccessUser(authUser)
})

const canAccessAiBrainComputed = computed(() => {
    return canAccessAiBrain(authUser);
});

const toggleShowSubMenu = (key) => {
    subMenus.value[key] = !subMenus.value[key]
}

const handleNavigate = () => {
    emit('navigate')
}

const isCurrentPage = (url) => {
    const currentUrl = typeof page.url === 'string'
        ? page.url.split('?')[0].replace(/\/+$/, '')
        : ''

    const cleanUrl = url.replace(/\/+$/, '')

    if (cleanUrl.endsWith('/*')) {
        const basePattern = cleanUrl.slice(0, -2)

        return currentUrl === basePattern || currentUrl.startsWith(`${basePattern}/`)
    }

    return currentUrl === cleanUrl
}

const isAnyCurrentPage = (urls = []) => {
    return urls.some((url) => isCurrentPage(url))
}

const isSubMenuVisible = (key) => {
    const routes = routeMap[key] || []
    const inRoute = isAnyCurrentPage(routes)

    return subMenus.value[key] || inRoute
}
</script>

<template>
    <div class="flex flex-col space-y-1 text-sm">

        <a :href="route('auth-user.dashboard.index')" class="sbfa-nav-item"
            :class="isCurrentPage('/auth-user/dashboard/*') ? 'is-active' : ''" @click="handleNavigate">
            <FontAwesomeIcon icon="gauge" />
            Dashboard
        </a>

        <a :href="route('back-office.medias.index')" class="sbfa-nav-item"
            :class="isCurrentPage('/back-office/medias/*') ? 'is-active' : ''" @click="handleNavigate">
            <FontAwesomeIcon icon="photo-film" />
            Media
        </a>

        <button @click="toggleShowSubMenu('AiAttributes')" class="sbfa-nav-item">
            <span class="flex items-center gap-2">
                <FontAwesomeIcon icon="brain" />
                Ai Attributes
            </span>

            <FontAwesomeIcon :icon="isSubMenuVisible('AiAttributes') ? 'chevron-up' : 'chevron-down'" />
        </button>

        <Transition
            enter-active-class="transition-all duration-300 ease-out" enter-from-class="opacity-0 max-h-0"
            enter-to-class="opacity-100 max-h-40" leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100 max-h-40" leave-to-class="opacity-0 max-h-0">
            <div v-if="isSubMenuVisible('AiAttributes')" class="ml-4 flex flex-col space-y-1 overflow-hidden">

                <a :href="route('back-office.ai-brains.index')" class="sbfa-nav-item"
                    :class="isAnyCurrentPage(routeMap.AiManagement) ? 'is-active' : ''" @click="handleNavigate">
                    <FontAwesomeIcon icon="brain" />
                    Ai Brain
                </a>

            </div>
        </Transition>

        <button type="button" class="sbfa-nav-item" @click="toggleShowSubMenu('UserManagement')" :aria-expanded="isSubMenuVisible('UserManagement')">
            <span class="flex items-center gap-2">
                <FontAwesomeIcon icon="users" />
                User Management
            </span>
            <FontAwesomeIcon :icon="isSubMenuVisible('UserManagement') ? 'chevron-up' : 'chevron-down'" class="ml-auto" />
        </button>

        <Transition enter-active-class="transition-all duration-300 ease-out" enter-from-class="opacity-0 max-h-0"
            enter-to-class="opacity-100 max-h-40" leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100 max-h-40" leave-to-class="opacity-0 max-h-0">
            <div v-if="isSubMenuVisible('UserManagement') && canAccessUserComputed"
                class="ml-4 flex flex-col space-y-1 overflow-hidden">

                <a :href="route('back-office.users.index')" class="sbfa-nav-item"
                    :class="isAnyCurrentPage(routeMap.UserManagement) ? 'is-active' : ''" @click="handleNavigate">
                    <FontAwesomeIcon icon="user" />
                    Users
                </a>

            </div>
        </Transition>
    </div>
</template>

<style scoped>
.sbfa-nav-item {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    width: 100%;
    padding: 0.6rem 0.8rem;
    border-radius: 10px;
    border-left: 3px solid transparent;
    font-size: 0.9rem;
    font-weight: 500;
    line-height: 1.3;
    color: var(--story-book-forge-ai-header-text-muted);
    background: transparent;
    text-align: left;
    text-decoration: none;
    cursor: pointer;
    transition:
        color var(--story-book-forge-ai-transition),
        background var(--story-book-forge-ai-transition),
        border-color var(--story-book-forge-ai-transition);
}

.sbfa-nav-item:hover {
    color: #fff;
    background: rgba(255, 255, 255, 0.08);
}

.sbfa-nav-item.is-active {
    color: var(--story-book-forge-ai-gold);
    border-left-color: var(--story-book-forge-ai-gold);
    background: rgb(199 154 59 / 14%);
    font-weight: 600;
}

.sbfa-nav-item:focus-visible {
    outline: 0;
    box-shadow: var(--story-book-forge-ai-focus-ring);
}

@media (prefers-reduced-motion: reduce) {
    .sbfa-nav-item {
        transition: none;
    }
}
</style>
