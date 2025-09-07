<?php

namespace App\Actions\Fortify;

use Illuminate\Validation\Rules\Password;

trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>
     */
    protected function passwordRules(): array
    {
        return [
            'required',
            'string',
            Password::min(8)            // mínimo 8 caracteres
                ->mixedCase()           // pelo menos 1 maiúscula e 1 minúscula
                ->numbers()             // pelo menos 1 número
                ->symbols(),            // pelo menos 1 caractere especial
            'confirmed',
        ];
    }
}
