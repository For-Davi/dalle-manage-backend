<?php

namespace App\Http\Requests\Product\Variant;

use Illuminate\Foundation\Http\FormRequest;

class CheckCodesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codes' => 'present|array',
            'codes.*' => 'string|max:255|distinct',
            'skus' => 'present|array',
            'skus.*' => 'string|max:255|distinct',
        ];
    }

    public function messages(): array
    {
        return [
            'codes.present' => 'A lista de códigos deve ser enviada.',
            'codes.array' => 'O formato dos códigos deve ser um array.',
            'codes.*.string' => 'Cada código deve ser um texto.',
            'codes.*.distinct' => 'Não é permitido enviar códigos duplicados.',
            'skus.present' => 'A lista de SKUs deve ser enviada.',
            'skus.array' => 'O formato dos SKUs deve ser um array.',
            'skus.*.string' => 'Cada SKU deve ser um texto.',
            'skus.*.distinct' => 'Não é permitido enviar SKUs duplicados.',
        ];
    }
}
