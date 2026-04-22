<?php

namespace App\Http\Requests\DeliveryGuy;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDeliveryGuyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'deliveryGuyID' => 'required|exists:delivery_guys,id',
            'name' => 'required|string|min:1|max:100',
            'email' => 'nullable|email|max:100',
            'cpf' => 'nullable|numeric',
            'phone' => 'nullable|string|max:20',
            'vehicle' => 'required|string|max:30',
        ];
    }

    public function messages(): array
    {
        return [
            'deliveryGuyID.required' => 'O ID do entregador é obrigatório',
            'deliveryGuyID.exists' => 'O ID do entregador informado não existe',
            'name.required' => 'O nome do entregador é obrigatório.',
            'name.string' => 'O nome do entregador deve ser um texto.',
            'name.min' => 'O nome do entregador deve ter pelo menos 1 caractere.',
            'name.max' => 'O nome do entregador não pode exceder 100 caracteres.',
            'email.email' => 'O e-mail deve ser um e-mail válido.',
            'email.max' => 'O e-mail não pode exceder 100 caracteres.',
            'cpf.numeric' => 'O CPF deve conter apenas números.',
            'phone.string' => 'O telefone deve ser um texto.',
            'phone.max' => 'O telefone não pode exceder 20 caracteres.',
            'vehicle.required' => 'O veículo é obrigatório.',
            'vehicle.string' => 'O veículo deve ser um texto.',
            'vehicle.max' => 'O veículo não pode exceder 30 caracteres.',
        ];
    }
}
