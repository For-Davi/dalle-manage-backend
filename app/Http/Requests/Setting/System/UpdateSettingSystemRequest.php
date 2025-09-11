<?php

namespace App\Http\Requests\Setting\System;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingSystemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sendNotificationStockCritical' => 'required|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'sendNotificationStockCritical.required' => 'Deve ser requerido o campo do toggle de Notificações de Estoque crítico',
            'sendNotificationStockCritical.in' => 'O valor do toggle de Notificações de Estoque crítico deve ser 0 ou 1',
        ];
    }
}
