<?php

namespace App\Rules;

use App\Models\Aluno;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidCpf implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! Aluno::cpfValido(is_string($value) ? $value : null)) {
            $fail('Informe um CPF válido.');
        }
    }
}
