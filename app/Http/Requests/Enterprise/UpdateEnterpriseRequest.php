<?php

namespace App\Http\Requests\Enterprise;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEnterpriseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:enterprises,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'cpf' => 'nullable|digits:11',
            'cnpj' => 'nullable|digits:14',
            'cep' => 'nullable|string|max:8',
            'state' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'neighborhood' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'numberAddress' => 'nullable|string|max:15',
            'complement' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'O ID da empresa é obrigatório.',
            'id.exists' => 'O ID informado não existe.',
            'name.required' => 'O nome da empresa é obrigatório.',
            'name.string' => 'O nome da empresa deve ser texto.',
            'name.max' => 'O nome da empresa não pode ter mais que 255 caracteres.',
            'email.email' => 'O e-mail informado não é válido.',
            'email.max' => 'O e-mail não pode ter mais que 255 caracteres.',
            'phone.max' => 'O telefone não pode ter mais que 20 caracteres.',
            'cpf.digits' => 'O CPF deve conter exatamente 11 dígitos.',
            'cnpj.digits' => 'O CNPJ deve conter exatamente 14 dígitos.',
            'cep.max' => 'O CEP não pode ter mais que 8 caracteres.',
            'state.string' => 'O Estado deve ser uma string',
            'city.max' => 'A cidade não pode ter mais que 100 caracteres.',
            'neighborhood.max' => 'O bairro não pode ter mais que 100 caracteres.',
            'address.max' => 'O logradouro não pode ter mais que 255 caracteres.',
            'numberAddress.max' => 'O número do endereço não pode ter mais que 15 caracteres.',
            'complement.max' => 'O complemento não pode ter mais que 255 caracteres.',
        ];
    }
}
