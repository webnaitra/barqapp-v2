<?php

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdvertiserPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Advertiser');
    }

    public function view(AuthUser $authUser): bool
    {
        return $authUser->can('View:Advertiser');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Advertiser');
    }

    public function update(AuthUser $authUser): bool
    {
        return $authUser->can('Update:Advertiser');
    }

    public function delete(AuthUser $authUser): bool
    {
        return $authUser->can('Delete:Advertiser');
    }

    public function restore(AuthUser $authUser): bool
    {
        return $authUser->can('Restore:Advertiser');
    }

    public function forceDelete(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDelete:Advertiser');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Advertiser');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Advertiser');
    }

    public function replicate(AuthUser $authUser): bool
    {
        return $authUser->can('Replicate:Advertiser');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Advertiser');
    }

}