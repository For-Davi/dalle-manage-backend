<?php

namespace App\Http\Requests\DalleAdm\Seller;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSellerPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'currentPassword' => 'required|string|min:8',
            'newPassword' => 'required|string|min:8',
        ];
    }

    public function messages(): array
    {
        return [
            'currentPassword.required' => 'Deve ser informado a senha atual',
            'currentPassword.string' => 'A senha atual deve ser uma string',
            'currentPassword.min' => 'A senha atual não pode ter menos de 8 caracteres',
            'newPassword.required' => 'Deve ser informado a nova senha',
            'newPassword.string' => 'A nova senha deve ser uma string',
            'newPassword.min' => 'A nova senha não pode ter menos de 8 caracteres',
        ];
    }
}
