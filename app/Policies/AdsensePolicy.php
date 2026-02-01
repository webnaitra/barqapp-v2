<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Adsense;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdsensePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Adsense');
    }

    public function view(AuthUser $authUser, Adsense $adsense): bool
    {
        return $authUser->can('View:Adsense');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Adsense');
    }

    public function update(AuthUser $authUser, Adsense $adsense): bool
    {
        return $authUser->can('Update:Adsense');
    }

    public function delete(AuthUser $authUser, Adsense $adsense): bool
    {
        return $authUser->can('Delete:Adsense');
    }

    public function restore(AuthUser $authUser, Adsense $adsense): bool
    {
        return $authUser->can('Restore:Adsense');
    }

    public function forceDelete(AuthUser $authUser, Adsense $adsense): bool
    {
        return $authUser->can('ForceDelete:Adsense');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Adsense');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Adsense');
    }

    public function replicate(AuthUser $authUser, Adsense $adsense): bool
    {
        return $authUser->can('Replicate:Adsense');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Adsense');
    }

}