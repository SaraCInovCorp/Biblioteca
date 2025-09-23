<?php

namespace App\Policies;

use App\Models\BookRequestItem;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookRequestItemPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): Response
    {
        // Mesma lógica do BookRequest
        return $user->isAdmin() || $user->isCidadao()
            ? Response::allow()
            : Response::deny('Acesso negado.');
    }

    public function view(User $user, BookRequestItem $bookRequestItem): Response
    {
        if ($user->isAdmin()) {
            return Response::allow();
        }

        // cidadão só vê itens que pertencem à sua requisição
        return $bookRequestItem->bookRequest->user_id === $user->id
            ? Response::allow()
            : Response::deny('Você não pode ver este item.');
    }

    public function create(User $user): Response
    {
        // Normalmente criação de itens é pelo sistema, pode liberar admin e cidadão
        return $user->isAdmin() || $user->isCidadao()
            ? Response::allow()
            : Response::deny('Ação negada.');
    }

    public function update(User $user, BookRequestItem $bookRequestItem): Response
    {
        if ($user->isAdmin()) {
            return Response::allow();
        }
        if ($user->isCidadao() && $bookRequest->user_id === $user->id) {
            return Response::allow();
        }
        return Response::deny('Apenas administradores ou dono podem alterar requisições.');
    }

    public function delete(User $user, BookRequestItem $bookRequestItem): Response
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
