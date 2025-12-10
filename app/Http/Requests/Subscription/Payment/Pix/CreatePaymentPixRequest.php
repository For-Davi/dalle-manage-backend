<?php

namespace App\Http\Requests\Subscription\Payment\Pix;

use Illuminate\Foundation\Http\FormRequest;

class CreatePaymentPixRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subscriptionID' => 'required|exists:subscriptions,id',
        ];
    }

    public function messages(): array
    {
        return [
            'subscriptionID.required' => 'O ID da assinatura é obrigatória.',
            'subscriptionID.exists' => 'O ID da assinatura informado não existe.',
        ];
    }
}
