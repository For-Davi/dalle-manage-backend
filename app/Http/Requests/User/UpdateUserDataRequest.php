<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserDataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:30',
            'email' => 'required|string|email|max:50',
            'photoAdd' => 'nullable|file|mimes:jpg,jpeg,png,gif|max:3072',
            'photoDelete' => 'nullable|exists:images,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'O nome deve ser uma string',
            'name.min' => 'O nome não pode ter menos de 3 caracteres',
            'name.max' => 'O nome não pode ter mais de 30 caracteres',
            'email.string' => 'O e-mail deve ser uma string',
            'email.email' => 'O e-mail deve ser um endereço de e-mail válido',
            'email.max' => 'O e-mail não pode ter mais de 50 caracteres',
            'photoAdd.file' => 'O arquivo de imagem é inválido.',
            'photoAdd.mimes' => 'Apenas imagens JPG, JPEG, PNG ou GIF são permitidas.',
            'photoAdd.max' => 'O tamanho máximo da imagem é 3MB.',
            'photoDelete.exists' => 'O ID da foto informada não existe',
        ];
    }
}
