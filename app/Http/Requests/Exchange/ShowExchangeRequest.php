<?php

namespace App\Http\Requests\Exchange;

use Illuminate\Foundation\Http\FormRequest;

class ShowExchangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'exchangeID' => 'required|exists:exchanges,id',
        ];
    }

    public function messages(): array
    {
        return [
            'exchangeID.required' => 'O ID do estorno é obrigatório',
            'exchangeID.exists' => 'O ID do estorno informado não existe',
        ];
    }

    public function validationData()
    {
        return array_merge($this->all(), $this->route()->parameters());
    }
}
