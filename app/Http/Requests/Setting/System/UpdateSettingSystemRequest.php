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
            'hasCreditExpiredData' => 'required|in:0,1',
            'quantityCreditExpireDays' => 'required|numeric|min:1|max:30',
        ];
    }

    public function messages(): array
    {
        return [
            'sendNotificationStockCritical.required' => 'Deve ser requerido o campo do toggle de Notificações de Estoque crítico',
            'sendNotificationStockCritical.in' => 'O valor do toggle de Notificações de Estoque crítico deve ser 0 ou 1',
            'hasCreditExpiredData.required' => 'Deve ser requerido o campo do toggle de Expiração de crédito',
            'hasCreditExpiredData.in' => 'O valor do toggle de Expiração de crédito deve ser 0 ou 1',
            'quantityCreditExpireDays.required' => 'Deve ser informado a quantidade de dias para expiração do crédito',
            'quantityCreditExpireDays.numeric' => 'A quantidade de dias para expiração do crédito deve ser numérica',
            'quantityCreditExpireDays.min' => 'A quantidade mínima de dias para expiração do crédito é 1',
            'quantityCreditExpireDays.max' => 'A quantidade máxima de dias para expiração do crédito é 30',
        ];
    }
}
