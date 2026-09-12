<?php
namespace App\Policies;

use App\Helpers\UserPermissionHelper;
use App\Models\StoryBookType;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class StoryBookTypePolicy
{
    public function before(User $authUser, string $ability): bool | null
    {
        if ($authUser->is_super_admin) {
            return true;
        }

        return null;
    }

    public function viewAny(User $authUser): Response
    {
        $module = UserPermissionHelper::MODULE_STORY_BOOK_TYPE;
        $access = UserPermissionHelper::ACCESS_VIEW_ANY;

        return $authUser->hasUserPermission($module, $access) ? Response::allow() : Response::deny();
    }

    public function view(User $authUser, StoryBookType $storyBookType): Response
    {
        $module = UserPermissionHelper::MODULE_STORY_BOOK_TYPE;
        $access = UserPermissionHelper::ACCESS_VIEW;

        return $authUser->hasUserPermission($module, $access) ? Response::allow() : Response::deny();
    }

    public function create(User $authUser): Response
    {
        $module = UserPermissionHelper::MODULE_STORY_BOOK_TYPE;
        $access = UserPermissionHelper::ACCESS_CREATE;

        return $authUser->hasUserPermission($module, $access) ? Response::allow() : Response::deny();
    }

    public function update(User $authUser, StoryBookType $storyBookType): Response
    {
        $module = UserPermissionHelper::MODULE_STORY_BOOK_TYPE;
        $access = UserPermissionHelper::ACCESS_UPDATE;

        if ($authUser->hasUserPermission($module, $access)) {
            return Response::allow();
        }

        return Response::deny();
    }

    public function delete(User $authUser, StoryBookType $storyBookType): Response
    {

        $module = UserPermissionHelper::MODULE_STORY_BOOK_TYPE;
        $access = UserPermissionHelper::ACCESS_DELETE;

        if ($authUser->hasUserPermission($module, $access)) {
            return Response::allow();
        }

        return Response::deny();
    }
}