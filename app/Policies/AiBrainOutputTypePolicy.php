<?php
namespace App\Policies;

use App\Helpers\UserPermissionHelper;
use App\Models\AiBrainOutputType;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AiBrainOutputTypePolicy
{
    public function before(User $authUser, string $ability): bool | null
    {
        if (in_array($ability, ['create', 'update', 'delete'], true)) {
            return false;
        }

        if ($authUser->is_super_admin) {
            return true;
        }

        return null;
    }

    public function viewAny(User $authUser): Response
    {
        $module = UserPermissionHelper::MODULE_AI_BRAIN_OUTPUT_TYPE;
        $access = UserPermissionHelper::ACCESS_VIEW_ANY;

        return $authUser->hasUserPermission($module, $access) ? Response::allow() : Response::deny();
    }

    public function view(User $authUser, AiBrainOutputType $aiBrainOutputType): Response
    {
        $module = UserPermissionHelper::MODULE_AI_BRAIN_OUTPUT_TYPE;
        $access = UserPermissionHelper::ACCESS_VIEW;

        return $authUser->hasUserPermission($module, $access) ? Response::allow() : Response::deny();
    }

    public function create(User $authUser): Response
    {
        return Response::deny();
    }

    public function update(User $authUser, AiBrainOutputType $aiBrainOutputType): Response
    {
        return Response::deny();
    }

    public function delete(User $authUser, AiBrainOutputType $aiBrainOutputType): Response
    {
        return Response::deny();
    }
}