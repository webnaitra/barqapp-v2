<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SourceFeed;
use Illuminate\Auth\Access\HandlesAuthorization;

class SourceFeedPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SourceFeed');
    }

    public function view(AuthUser $authUser, SourceFeed $sourceFeed): bool
    {
        return $authUser->can('View:SourceFeed');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SourceFeed');
    }

    public function update(AuthUser $authUser, SourceFeed $sourceFeed): bool
    {
        return $authUser->can('Update:SourceFeed');
    }

    public function delete(AuthUser $authUser, SourceFeed $sourceFeed): bool
    {
        return $authUser->can('Delete:SourceFeed');
    }

    public function restore(AuthUser $authUser, SourceFeed $sourceFeed): bool
    {
        return $authUser->can('Restore:SourceFeed');
    }

    public function forceDelete(AuthUser $authUser, SourceFeed $sourceFeed): bool
    {
        return $authUser->can('ForceDelete:SourceFeed');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SourceFeed');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SourceFeed');
    }

    public function replicate(AuthUser $authUser, SourceFeed $sourceFeed): bool
    {
        return $authUser->can('Replicate:SourceFeed');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SourceFeed');
    }

}