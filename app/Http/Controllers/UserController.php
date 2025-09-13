<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show(Request $request, User $user = null)
    {
        $authUser = $request->user();
        $isAdmin = $authUser->isAdmin();

        // Cidadão vê só próprio cadastro direto
        if (!$isAdmin) {
            $user = $authUser;
        } else {
            // Admin pode fazer busca via query `q` quando não passa $user na rota
            if (is_null($user)) {
                if ($request->filled('q')) {
                    $search = $request->input('q');

                    // Busque o usuário por nome ou id
                    $user = User::where('id', $search)
                        ->orWhere('name', 'like', "%{$search}%")
                        ->first();

                    if (!$user) {
                        // Se não encontrou, retorna view com mensagem e formulário só
                        return view('users.show', [
                            'user' => null,
                            'historico' => null,
                            'isAdmin' => true,
                            'searchTerm' => $search,
                            'message' => 'Nenhum usuário encontrado com esse termo.',
                        ]);
                    }
                } else {
                    // Sem usuário e sem busca (apenas formulário)
                    return view('users.show', [
                        'user' => null,
                        'historico' => null,
                        'isAdmin' => true,
                        'searchTerm' => '',
                    ]);
                }
            }
        }

        // Agora carregue o histórico só se $user existir
        $historico = null;
        if ($user) {
            $query = $user->requisicoes()->with('items.livro')->orderByDesc('data_inicio');
            $historico = $query->paginate(10);
        }

        return view('users.show', [
            'user' => $user,
            'historico' => $historico,
            'isAdmin' => $isAdmin,
            'searchTerm' => $request->input('q', ''),
            'message' => null,
        ]);
    }


}
