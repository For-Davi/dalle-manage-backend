<?php

namespace App\Http\Requests\Return;

use Illuminate\Foundation\Http\FormRequest;

class ShowReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'returnID' => 'required|exists:returns,id',
            'notDelivered' => 'nullable|in:1,0'
        ];
    }

    public function messages(): array
    {
        return [
            'returnID.required' => 'O ID da devolução é obrigatório',
            'returnID.exists' => 'O ID da devolução informada não existe',
            'notDelivered.in' => 'O campo filtrar não entregues deve ser verdadeiro (1) ou falso (0).',
        ];
    }

    public function validationData()
    {
        return array_merge($this->all(), $this->route()->parameters());
    }
}
