<?php

namespace App\Http\Requests\Return;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:returns,id',
            'saleID' => 'required|exists:sales,id',
            'status' => 'required|string|in:Ativa,Cancelada'
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'O ID da devolução é obrigatório',
            'id.exists' => 'O ID da devolução informada não existe',
            'saleID.required' => 'O ID da venda é obrigatório',
            'saleID.exists' => 'O ID da venda informada não existe',
            'status.required' => 'O status da devolução é obrigatório',
            'status.string' => 'O status da devolução deve ser uma string',
            'status.in' => 'Insira um status de devolução válido',
        ];
    }
}
