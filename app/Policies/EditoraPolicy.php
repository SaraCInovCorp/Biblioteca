<?php

namespace App\Policies;

use App\Models\Editora;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class EditoraPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Editora $editora): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Apenas administradores podem criar editoras.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Editora $editora): Response
    {
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Apenas administradores podem atualizar editoras.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Editora $editora): Response
    {
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Apenas administradores podem deletar editoras.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Editora $editora): Response
    {
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Apenas administradores podem restaurar editoras.');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Editora $editora): Response
    {
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Apenas administradores podem deletar permanentemente editoras.');
    }
}
