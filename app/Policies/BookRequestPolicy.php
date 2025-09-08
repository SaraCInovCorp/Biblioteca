<?php

namespace App\Policies;

use App\Models\BookRequest;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookRequestPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): Response
    {
        // Admin vê todas as requisições, cidadão vê as suas
        return $user->isAdmin() || $user->isCidadao()
            ? Response::allow()
            : Response::deny('Acesso negado.');
    }

    public function view(User $user, BookRequest $bookRequest): Response
    {
        // Admin vê qualquer requisição, cidadão só suas
        if ($user->isAdmin()) {
            return Response::allow();
        }

        return $bookRequest->user_id === $user->id
            ? Response::allow()
            : Response::deny('Você não pode ver esta requisição.');
    }

    public function create(User $user): Response
    {
        // Apenas cidadãos podem criar requisições (admins podem criar para outros)
        return ($user->isCidadao() || $user->isAdmin())
            ? Response::allow()
            : Response::deny('Você não pode criar requisições.');
    }

    public function update(User $user, BookRequest $bookRequest): Response
    {
        // Apenas admin pode atualizar requisições
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Apenas administradores podem alterar requisições.');
    }

    public function delete(User $user, BookRequest $bookRequest): Response
    {
        // Apenas admin pode deletar requisições
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Apenas administradores podem deletar requisições.');
    }
}
