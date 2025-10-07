<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminRegisterController extends Controller
{
    public function create()
    {
        \Log::info('Entrou create admin-register'); 
        return view('auth.admin-register');
    }

    public function store(Request $request)
    {
        $actingAdmin = auth()->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin', 
        ]);

        activity()
            ->causedBy($actingAdmin)
            ->performedOn($admin)
            ->event('registered')
            ->useLog('auth')
            ->withProperties([
                'ip' => request()->ip(),
                'browser' => request()->header('User-Agent'),
            ])
            ->log('Administrador registrado no sistema');

        return redirect()->route('dashboard')->with('success', 'Administrador criado com sucesso!');
    }
}
