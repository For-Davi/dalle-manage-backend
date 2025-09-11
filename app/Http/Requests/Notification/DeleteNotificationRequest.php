<?php

namespace App\Http\Requests\Notification;

use Illuminate\Foundation\Http\FormRequest;

class DeleteNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'notificationID' => 'required|exists:notifications,id',
        ];
    }

    public function messages(): array
    {
        return [
            'notificationID.required' => 'O ID da notificação é obrigatória',
            'notificationID.exists' => 'O ID da notificação informada não existe',
        ];
    }

    public function validationData()
    {
        return array_merge($this->all(), $this->route()->parameters());
    }
}
