<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\AdminAd;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdminAdPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AdminAd');
    }

    public function view(AuthUser $authUser, AdminAd $adminAd): bool
    {
        return $authUser->can('View:AdminAd');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AdminAd');
    }

    public function update(AuthUser $authUser, AdminAd $adminAd): bool
    {
        return $authUser->can('Update:AdminAd');
    }

    public function delete(AuthUser $authUser, AdminAd $adminAd): bool
    {
        return $authUser->can('Delete:AdminAd');
    }

    public function restore(AuthUser $authUser, AdminAd $adminAd): bool
    {
        return $authUser->can('Restore:AdminAd');
    }

    public function forceDelete(AuthUser $authUser, AdminAd $adminAd): bool
    {
        return $authUser->can('ForceDelete:AdminAd');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AdminAd');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AdminAd');
    }

    public function replicate(AuthUser $authUser, AdminAd $adminAd): bool
    {
        return $authUser->can('Replicate:AdminAd');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AdminAd');
    }

}