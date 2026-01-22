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
            'codes.*' => 'string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'codes.present' => 'A lista de códigos deve ser enviada.',
            'codes.array' => 'O formato dos códigos deve ser um array.',
            'codes.*.string' => 'Cada código deve ser um texto.',
        ];
    }
}
