<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class HabitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('habits')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                })
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.string' => 'Deve ser um texto.',
            'name.max' => 'O campo nome não pode ter mais de 255 caracteres.',
            'name.unique' => 'Você já possui um hábito com esse nome.',
        ];
    }
}