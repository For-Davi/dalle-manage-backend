<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class DeleteUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'userID' => 'required|exists:users,id',
            'deleteEmployee' => 'required|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'userID.required' => 'O ID do usuário é obrigatório.',
            'userID.exists' => 'O usuário informado não existe.',
            'deleteEmployee.required' => 'O campo do checkbox é obrigatório',
            'deleteEmployee.in' => 'O valor do campo do checkbox deve ser 1 ou 0',
        ];
    }

    public function validationData()
    {
        return array_merge($this->all(), $this->route()->parameters());
    }
}
