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
        return $user->isAdmin() || $user->isCidadao()
            ? Response::allow()
            : Response::deny('Acesso negado.');
    }

    public function view(User $user, BookRequest $bookRequest): Response
    {
        if ($user->isAdmin()) {
            return Response::allow();
        }

        return $bookRequest->user_id === $user->id
            ? Response::allow()
            : Response::deny('Você não pode ver esta requisição.');
    }

    public function create(User $user): Response
    {
        return ($user->isCidadao() || $user->isAdmin())
            ? Response::allow()
            : Response::deny('Você não pode criar requisições.');
    }

    public function update(User $user, BookRequest $bookRequest): Response
    {
        if ($user->isAdmin()) {
            return Response::allow();
        }
        if ($user->isCidadao() && $bookRequest->user_id === $user->id) {
            return Response::allow();
        }
        return Response::deny('Apenas administradores ou dono podem alterar requisições.');
    }

    public function delete(User $user, BookRequest $bookRequest): Response
    {
        $now = now();
        if ($now->lt($bookRequest->data_inicio)) {
            if ($user->isAdmin() || $user->id === $bookRequest->user_id) {
                return Response::allow();
            }
        }
        return Response::deny('Somente antes da data de início a requisição pode ser cancelada.');
    }
}
