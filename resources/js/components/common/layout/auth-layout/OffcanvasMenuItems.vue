<script setup>
import { ref, computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";

import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

import {
    faUser,
    faUsers,
    faChevronDown,
    faChevronUp,
    faGauge,
    faPhotoFilm,
    faBrain,
    faBookOpen,
    faStar,
    faLanguage,
    faLayerGroup,
    faPalette,
    faClipboardList,
    faBook,
} from "@fortawesome/free-solid-svg-icons";

library.add(
    faUser,
    faUsers,
    faChevronDown,
    faChevronUp,
    faGauge,
    faPhotoFilm,
    faBrain,
    faBookOpen,
    faStar,
    faLanguage,
    faLayerGroup,
    faPalette,
    faClipboardList,
    faBook,
);

import {
    canAccessUser,
    canAccessGenre,
    canAccessAudience,
    canAccessLanguage,
    canAccessStoryBookType,
    canAccessIllustrationType,
    canAccessAiBrain,
    canAccessAiPrompt,
    canAccessAiBrainOutputType,
} from "@/composables/useUserPermissions";

const { authUser } = defineProps({
    authUser: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(["navigate"]);

const page = usePage();

const subMenus = ref({
    UserManagement: false,
    AiAttributes: false,
    StoryBookAttribute: false,
});

const routeMap = {
    UserManagement: ["/back-office/users/*"],
    AiAttributes: ["/back-office/ai-brains/*", "/back-office/ai-prompts/*", "/back-office/ai-brain-output-types/*"],
    StoryBookAttribute: [
        "/back-office/genres/*",
        "/back-office/audiences/*",
        "/back-office/languages/*",
        "/back-office/story-book-types/*",
        "/back-office/illustration-types/*",
    ],
};

const canAccessUserComputed = computed(() => {
    return canAccessUser(authUser);
});

const canAccessGenreComputed = computed(() => {
    return canAccessGenre(authUser);
});

const canAccessAudienceComputed = computed(() => {
    return canAccessAudience(authUser);
});

const canAccessLanguageComputed = computed(() => {
    return canAccessLanguage(authUser);
});

const canAccessStoryBookTypeComputed = computed(() => {
    return canAccessStoryBookType(authUser);
});

const canAccessIllustrationTypeComputed = computed(() => {
    return canAccessIllustrationType(authUser);
});

const canAccessAiBrainComputed = computed(() => {
    return canAccessAiBrain(authUser);
});

const canAccessAiPromptComputed = computed(() => {
    return canAccessAiPrompt(authUser);
});

const canAccessAiBrainOutputTypeComputed = computed(() => {
    return canAccessAiBrainOutputType(authUser);
});

const toggleShowSubMenu = (key) => {
    subMenus.value[key] = !subMenus.value[key];
};

const handleNavigate = () => {
    emit("navigate");
};

const isCurrentPage = (url) => {
    const currentUrl =
        typeof page.url === "string"
            ? page.url.split("?")[0].replace(/\/+$/, "")
            : "";

    const cleanUrl = url.replace(/\/+$/, "");

    if (cleanUrl.endsWith("/*")) {
        const basePattern = cleanUrl.slice(0, -2);

        return (
            currentUrl === basePattern ||
            currentUrl.startsWith(`${basePattern}/`)
        );
    }

    return currentUrl === cleanUrl;
};

const isAnyCurrentPage = (urls = []) => {
    return urls.some((url) => isCurrentPage(url));
};

const isSubMenuVisible = (key) => {
    const routes = routeMap[key] || [];
    const inRoute = isAnyCurrentPage(routes);

    return subMenus.value[key] || inRoute;
};
</script>

<template>
    <div class="flex flex-col space-y-1 text-sm">
        <Link
            :href="route('auth-user.dashboard.index')"
            class="sbfa-nav-item"
            :class="isCurrentPage('/auth-user/dashboard/*') ? 'is-active' : ''"
            @start="handleNavigate"
        >
            <FontAwesomeIcon icon="gauge" />
            Dashboard
        </Link>

        <Link
            :href="route('back-office.medias.index')"
            class="sbfa-nav-item"
            :class="isCurrentPage('/back-office/medias/*') ? 'is-active' : ''"
            @start="handleNavigate"
        >
            <FontAwesomeIcon icon="photo-film" />
            Media
        </Link>

        <Link
            :href="route('back-office.story-books.index')"
            class="sbfa-nav-item"
            :class="isCurrentPage('/back-office/story-books/*') ? 'is-active' : ''"
            @start="handleNavigate"
        >
            <FontAwesomeIcon icon="book" />
            Story books
        </Link>

        <button
            @click="toggleShowSubMenu('StoryBookAttribute')"
            class="sbfa-nav-item"
        >
            <span class="flex items-center gap-2">
                <FontAwesomeIcon icon="book" />
                Story Book Attributes
            </span>

            <FontAwesomeIcon
                :icon="
                    isSubMenuVisible('StoryBookAttribute')
                        ? 'chevron-up'
                        : 'chevron-down'
                "
            />
        </button>

        <Transition
            enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="opacity-0 max-h-0"
            enter-to-class="opacity-100 max-h-40"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100 max-h-40"
            leave-to-class="opacity-0 max-h-0"
        >
            <div
                v-if="isSubMenuVisible('StoryBookAttribute')"
                class="ml-4 flex flex-col space-y-1 overflow-hidden"
            >
                <Link
                    v-if="canAccessGenreComputed"
                    :href="route('back-office.genres.index')"
                    class="sbfa-nav-item"
                    :class="
                        isCurrentPage('/back-office/genres/*')
                            ? 'is-active'
                            : ''
                    "
                    @start="handleNavigate"
                >
                    <FontAwesomeIcon icon="star" />
                    Genres
                </Link>

                <Link
                    v-if="canAccessAudienceComputed"
                    :href="route('back-office.audiences.index')"
                    class="sbfa-nav-item"
                    :class="
                        isCurrentPage('/back-office/audiences/*')
                            ? 'is-active'
                            : ''
                    "
                    @start="handleNavigate"
                >
                    <FontAwesomeIcon icon="users" />
                    Audiences
                </Link>

                <Link
                    v-if="canAccessLanguageComputed"
                    :href="route('back-office.languages.index')"
                    class="sbfa-nav-item"
                    :class="
                        isCurrentPage('/back-office/languages/*')
                            ? 'is-active'
                            : ''
                    "
                    @start="handleNavigate"
                >
                    <FontAwesomeIcon icon="language" />
                    Languages
                </Link>

                <Link
                    v-if="canAccessStoryBookTypeComputed"
                    :href="route('back-office.story-book-types.index')"
                    class="sbfa-nav-item"
                    :class="
                        isCurrentPage('/back-office/story-book-types/*')
                            ? 'is-active'
                            : ''
                    "
                    @start="handleNavigate"
                >
                    <FontAwesomeIcon icon="layer-group" />
                    Story Book Types
                </Link>

                <Link
                    v-if="canAccessIllustrationTypeComputed"
                    :href="route('back-office.illustration-types.index')"
                    class="sbfa-nav-item"
                    :class="
                        isCurrentPage('/back-office/illustration-types/*')
                            ? 'is-active'
                            : ''
                    "
                    @start="handleNavigate"
                >
                    <FontAwesomeIcon icon="palette" />
                    Illustration Types
                </Link>
            </div>
        </Transition>

        <button
            @click="toggleShowSubMenu('AiAttributes')"
            class="sbfa-nav-item"
            :class="isAnyCurrentPage(routeMap.AiAttributes) ? 'is-active' : ''"
        >
            <span class="flex items-center gap-2">
                <FontAwesomeIcon icon="brain" />
                Ai Attributes
            </span>

            <FontAwesomeIcon
                :icon="
                    isSubMenuVisible('AiAttributes')
                        ? 'chevron-up'
                        : 'chevron-down'
                "
            />
        </button>

        <Transition
            enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="opacity-0 max-h-0"
            enter-to-class="opacity-100 max-h-40"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100 max-h-40"
            leave-to-class="opacity-0 max-h-0"
        >
            <div
                v-if="isSubMenuVisible('AiAttributes')"
                class="ml-4 flex flex-col space-y-1 overflow-hidden"
            >
                <Link
                    v-if="canAccessAiBrainComputed"
                    :href="route('back-office.ai-brains.index')"
                    class="sbfa-nav-item"
                    :class="
                        isCurrentPage('/back-office/ai-brains/*')
                            ? 'is-active'
                            : ''
                    "
                    @start="handleNavigate"
                >
                    <FontAwesomeIcon icon="brain" />
                    Ai Brains
                </Link>

                <Link
                    v-if="canAccessAiPromptComputed"
                    :href="route('back-office.ai-prompts.index')"
                    class="sbfa-nav-item"
                    :class="
                        isCurrentPage('/back-office/ai-prompts/*')
                            ? 'is-active'
                            : ''
                    "
                    @start="handleNavigate"
                >
                    <FontAwesomeIcon icon="clipboard-list" class="w-4" />
                    Ai Prompts
                </Link>

                <Link
                    v-if="canAccessAiBrainOutputTypeComputed"
                    :href="route('back-office.ai-brain-output-types.index')"
                    class="sbfa-nav-item"
                    :class="
                        isCurrentPage('/back-office/ai-brain-output-types/*')
                            ? 'is-active'
                            : ''
                    "
                    @start="handleNavigate"
                >
                    <FontAwesomeIcon icon="brain" class="w-4" />
                    Ai Brain Output Types
                </Link>
            </div>
        </Transition>

        <button
            type="button"
            class="sbfa-nav-item"
            @click="toggleShowSubMenu('UserManagement')"
            :aria-expanded="isSubMenuVisible('UserManagement')"
        >
            <span class="flex items-center gap-2">
                <FontAwesomeIcon icon="users" />
                User Management
            </span>
            <FontAwesomeIcon
                :icon="
                    isSubMenuVisible('UserManagement')
                        ? 'chevron-up'
                        : 'chevron-down'
                "
                class="ml-auto"
            />
        </button>

        <Transition
            enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="opacity-0 max-h-0"
            enter-to-class="opacity-100 max-h-40"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100 max-h-40"
            leave-to-class="opacity-0 max-h-0"
        >
            <div
                v-if="
                    isSubMenuVisible('UserManagement') && canAccessUserComputed
                "
                class="ml-4 flex flex-col space-y-1 overflow-hidden"
            >
                <Link
                    :href="route('back-office.users.index')"
                    class="sbfa-nav-item"
                    :class="
                        isAnyCurrentPage(routeMap.UserManagement)
                            ? 'is-active'
                            : ''
                    "
                    @start="handleNavigate"
                >
                    <FontAwesomeIcon icon="user" />
                    Users
                </Link>
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
