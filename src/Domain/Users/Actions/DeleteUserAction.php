<?php

namespace Domain\Users\Actions;

use App\Exceptions\FailedToDeleteObjectException;
use App\Exceptions\UnauthorizedToDeleteUserException;
use App\Models\User;

class DeleteUserAction
{
    /**
     * @throws \App\Exceptions\UnauthorizedToDeleteUserException
     * @throws \App\Exceptions\FailedToDeleteObjectException
     */
    public function execute(User $user, User $request_user): bool
    {
        $this->verifyCanDeleteUser($user, $request_user);
        // Delete user auth tokens
        $user->tokens()->delete();
        $user->refreshTokens()->delete();
        $was_deleted = $user->delete();
        if (!$was_deleted) {
            throw new FailedToDeleteObjectException(User::class);
        }
        return $was_deleted;
    }

    /**
     * Returns whether the user requesting the delete have the permission to delete
     * the specified user.
     *
     * @param User $user_to_delete
     * @param User $request_user
     * @throws \App\Exceptions\UnauthorizedToDeleteUserException
     */
    private function verifyCanDeleteUser(
        User $user_to_delete,
        User $request_user
    ): void {
        $is_same_user = $user_to_delete->id == $request_user->id;
        $is_admin = $request_user->tokenCan("admin");
        if (!($is_admin || $is_same_user)) {
            throw new UnauthorizedToDeleteUserException();
        }
    }
}
