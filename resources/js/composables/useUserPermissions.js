
import { fetchFromApi } from '@/composables/useApiClient'
import { apiCacheKey, apiCacheTTL, useApiCache } from '@/composables/useApiCache'

const { clearByPrefix } = useApiCache()

export const groups = {
    User: 'User',
    Genre: 'Genre',
    Audience: 'Audience',
    Language: 'Language',
    StoryType: 'Story Type',
    IllustrationType: 'Illustration Type',
    AiBrain: 'Ai Brain',
    AiPrompt: 'Ai Prompt',
}

export const access = {
    ViewAny: 'View any',
    View: 'View',
    Create: 'Create',
    Update: 'Update',
    Delete: 'Delete',
    Restore: 'Restore',
    ForceDelete: 'Force delete',
}

export const getPermissions = async (authUser) => {
    if (!authUser) {
        return []
    }

    let permissions = authUser.user_permissions

    if (!Array.isArray(permissions)) {
        const apiUrl = route('search.user', { slugOrId: authUser.id, })
        const user = await fetchFromApi(
            apiUrl,
            {},
            {
                key: `${apiCacheKey.API_USER}:${authUser.id}`,
                ttl: apiCacheTTL.API_USER,
            }
        )

        permissions = user?.user_permissions || []
    }

    return permissions
}

export const clearPermissionCache = (userId) => {
    if (userId) {
        clearByPrefix(`${apiCacheKey.API_USER}:${userId}`)
        return
    }

    clearByPrefix(`${apiCacheKey.API_USER}:`)
}

export const hasPermission = async (authUser, module, permissionAccess) => {
    if (!authUser) {
        return false
    }

    if ( authUser.is_super_admin ) {
        return true
    }

    const permissions = await getPermissions( authUser )

    return permissions.some(
        permission =>
            permission.module ===
            module &&
            permission.access ===
            permissionAccess
    )
}

export const canAccessUser = async (authUser) => hasPermission(authUser, groups.User, access.ViewAny)
export const canCreateUser = async (authUser) => hasPermission(authUser, groups.User, access.Create)
export const canUpdateUser = async (authUser, user) => hasPermission(authUser, groups.User, access.Update)
export const canActiveInactiveUser = async (authUser, user) => {
    if (user?.is_default) {
        return false
    }
    if (user?.deleted_at) {
        return await hasPermission(
            authUser,
            groups.User,
            access.Restore
        )
    }
    else {
        return await hasPermission(
            authUser,
            groups.User,
            access.Delete
        )
    }
}
export const canDeleteUser = async (authUser, user) => {
    if (user?.is_default) {
        return false
    }
    return await hasPermission(
        authUser,
        groups.User,
        access.ForceDelete
    )
}

export const canAccessGenre = async (authUser) => hasPermission(authUser, groups.Genre, access.View)
export const canViewGenre = async (authUser, genre) => hasPermission(authUser, groups.Genre, access.ViewAny)
export const canCreateGenre = async (authUser, genre) => hasPermission(authUser, groups.Genre, access.Create)
export const canUpdateGenre = async (authUser, genre) => hasPermission(authUser, groups.Genre, access.Update)
export const canDeleteGenre = async (authUser, genre) => hasPermission(authUser, groups.Genre, access.Delete)

export const canAccessAudience = async (authUser) => hasPermission(authUser, groups.Audience, access.View)
export const canViewAudience = async (authUser, audience) => hasPermission(authUser, groups.Audience, access.ViewAny)
export const canCreateAudience = async (authUser, audience) => hasPermission(authUser, groups.Audience, access.Create)
export const canUpdateAudience = async (authUser, audience) => hasPermission(authUser, groups.Audience, access.Update)
export const canDeleteAudience = async (authUser, audience) => hasPermission(authUser, groups.Audience, access.Delete)

export const canAccessLanguage = async (authUser) => hasPermission(authUser, groups.Language, access.View)
export const canViewLanguage = async (authUser, language) => hasPermission(authUser, groups.Language, access.ViewAny)
export const canCreateLanguage = async (authUser, language) => hasPermission(authUser, groups.Language, access.Create)
export const canUpdateLanguage = async (authUser, language) => hasPermission(authUser, groups.Language, access.Update)
export const canDeleteLanguage = async (authUser, language) => hasPermission(authUser, groups.Language, access.Delete)

export const canAccessStoryType = async (authUser) => hasPermission(authUser, groups.StoryType, access.View)
export const canViewStoryType = async (authUser, storyType) => hasPermission(authUser, groups.StoryType, access.ViewAny)
export const canCreateStoryType = async (authUser, storyType) => hasPermission(authUser, groups.StoryType, access.Create)
export const canUpdateStoryType = async (authUser, storyType) => hasPermission(authUser, groups.StoryType, access.Update)
export const canDeleteStoryType = async (authUser, storyType) => hasPermission(authUser, groups.StoryType, access.Delete)

export const canAccessIllustrationType = async (authUser) => hasPermission(authUser, groups.IllustrationType, access.View)
export const canViewIllustrationType = async (authUser, illustrationType) => hasPermission(authUser, groups.IllustrationType, access.ViewAny)
export const canCreateIllustrationType = async (authUser, illustrationType) => hasPermission(authUser, groups.IllustrationType, access.Create)
export const canUpdateIllustrationType = async (authUser, illustrationType) => hasPermission(authUser, groups.IllustrationType, access.Update)
export const canDeleteIllustrationType = async (authUser, illustrationType) => hasPermission(authUser, groups.IllustrationType, access.Delete)

export const canAccessAiBrain = async (authUser) => hasPermission(authUser, groups.AiBrain, access.View)
export const canViewAiBrain = async (authUser, aiBrain) => hasPermission(authUser, groups.AiBrain, access.ViewAny)
export const canCreateAiBrain = async (authUser, aiBrain) => hasPermission(authUser, groups.AiBrain, access.Create)
export const canUpdateAiBrain = async (authUser, aiBrain) => hasPermission(authUser, groups.AiBrain, access.Update)
export const canDeleteAiBrain = async (authUser, aiBrain) => hasPermission(authUser, groups.AiBrain, access.Delete)

export const canAccessAiPrompt = async (authUser) => hasPermission(authUser, groups.AiPrompt, access.View)
export const canViewAiPrompt = async (authUser, aiPrompt) => hasPermission(authUser, groups.AiPrompt, access.ViewAny)
export const canCreateAiPrompt = async (authUser, aiPrompt) => hasPermission(authUser, groups.AiPrompt, access.Create)
export const canUpdateAiPrompt = async (authUser, aiPrompt) => hasPermission(authUser, groups.AiPrompt, access.Update)
export const canDeleteAiPrompt = async (authUser, aiPrompt) => hasPermission(authUser, groups.AiPrompt, access.Delete)

export const canAccessActivityLog = async (authUser) => authUser?.is_super_admin
export const canDeleteActivityLog = async (authUser) => authUser?.is_super_admin

export const canAccessQueueMonitor = (authUser) => authUser?.is_super_admin
export const canAccessLogViewer = (authUser) => authUser?.is_super_admin

export const canAccessSetting = (authUser) => authUser?.is_super_admin
