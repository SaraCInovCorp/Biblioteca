<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Jetstream\Http\Livewire\UpdatePasswordForm;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('password can be updated', function () {
    $this->actingAs($user = User::factory()->create([
        'password' => Hash::make('Senha12#'), // senha inicial
    ]));

    Livewire::test(UpdatePasswordForm::class)
        ->set('state', [
            'current_password' => 'Senha12#',   // senha correta
            'password' => 'Senha12*',           // nova senha
            'password_confirmation' => 'Senha12*',
        ])
        ->call('updatePassword')
        ->assertHasNoErrors();

    expect(Hash::check('Senha12*', $user->fresh()->password))->toBeTrue();
});

test('current password must be correct', function () {
    $this->actingAs($user = User::factory()->create([
        'password' => Hash::make('Senha12#'),
    ]));

    Livewire::test(UpdatePasswordForm::class)
        ->set('state', [
            'current_password' => 'senhaErrada',
            'password' => 'Senha12*',
            'password_confirmation' => 'Senha12*',
        ])
        ->call('updatePassword')
        ->assertHasErrors(['current_password']);

    expect(Hash::check('Senha12#', $user->fresh()->password))->toBeTrue();
});

test('new passwords must match', function () {
    $this->actingAs($user = User::factory()->create([
        'password' => Hash::make('Senha12#'),
    ]));

    Livewire::test(UpdatePasswordForm::class)
        ->set('state', [
            'current_password' => 'Senha12#',
            'password' => 'Senha12*',
            'password_confirmation' => 'SenhaErrada!',
        ])
        ->call('updatePassword')
        ->assertHasErrors(['password']);

    expect(Hash::check('Senha12#', $user->fresh()->password))->toBeTrue();
});
