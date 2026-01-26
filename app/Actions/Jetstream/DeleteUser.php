<?php

namespace App\Actions\Jetstream;

use App\Models\User;
use Laravel\Jetstream\Contracts\DeletesUsers;

class DeleteUser implements DeletesUsers
{
    /**
     * Delete the given user.
     */
    public function delete(User $user): void
    {
        $user->deleteProfilePhoto();

        // Guard against missing Sanctum `tokens` relation when the package
        // has been removed — ensure the user is still deleted.
        if (method_exists($user, 'tokens')) {
            $user->tokens->each->delete();
        }

        $user->delete();
    }
}
