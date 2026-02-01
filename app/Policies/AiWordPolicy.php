<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\AiWord;
use Illuminate\Auth\Access\HandlesAuthorization;

class AiWordPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AiWord');
    }

    public function view(AuthUser $authUser, AiWord $aiWord): bool
    {
        return $authUser->can('View:AiWord');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AiWord');
    }

    public function update(AuthUser $authUser, AiWord $aiWord): bool
    {
        return $authUser->can('Update:AiWord');
    }

    public function delete(AuthUser $authUser, AiWord $aiWord): bool
    {
        return $authUser->can('Delete:AiWord');
    }

    public function restore(AuthUser $authUser, AiWord $aiWord): bool
    {
        return $authUser->can('Restore:AiWord');
    }

    public function forceDelete(AuthUser $authUser, AiWord $aiWord): bool
    {
        return $authUser->can('ForceDelete:AiWord');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AiWord');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AiWord');
    }

    public function replicate(AuthUser $authUser, AiWord $aiWord): bool
    {
        return $authUser->can('Replicate:AiWord');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AiWord');
    }

}