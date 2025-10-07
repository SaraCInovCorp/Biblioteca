<?php

namespace App\Actions\Jetstream;

use App\Models\User;
use Laravel\Jetstream\Contracts\DeletesUsers;
use Illuminate\Http\Request;

class DeleteUser implements DeletesUsers
{
    /**
     * Delete the given user.
     */
    public function delete(User $user): void
    {

        $ip = request()->ip();
        $browser = request()->header('User-Agent');
        $timestamp = now();

        activity()
            ->causedBy($user)
            ->withProperties([
                'module' => 'User Management',
                'object_id' => $user->id,
                'action' => 'Delete',
                'ip' => $ip,
                'browser' => $browser,
                'timestamp' => $timestamp->toDateTimeString(),
            ])
            ->event('user_deleted')
            ->log("Conta do usuário ID {$user->id} excluída permanentemente às {$timestamp->toDateTimeString()} com IP {$ip} e browser {$browser}.");

        $user->deleteProfilePhoto();
        $user->tokens->each->delete();
        $user->delete();
    }
}
