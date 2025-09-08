<?php

namespace App\Policies;

use App\Models\Autor;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class AutorPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): Response
    {
        return Response::allow();
    }

    public function view(User $user, Autor $autor): Response
    {
        return Response::allow();
    }

    public function create(User $user): Response
    {
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Apenas administradores podem criar autores.');
    }

    public function update(User $user, Autor $autor): Response
    {
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Apenas administradores podem atualizar autores.');
    }

    public function delete(User $user, Autor $autor): Response
    {
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Apenas administradores podem deletar autores.');
    }

    public function restore(User $user, Autor $autor): Response
    {
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Apenas administradores podem restaurar autores.');
    }

    public function forceDelete(User $user, Autor $autor): Response
    {
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Apenas administradores podem deletar permanentemente autores.');
    }
}
