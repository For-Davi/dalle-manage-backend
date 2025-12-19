<?php

namespace App\DTO\Employee;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Auth;

class CreateEmployeeDTO extends BaseDTO
{
    public function __construct(
        public string $name,
        public readonly ?string $email,
        public readonly ?string $sex,
        public readonly ?string $phone,
        public readonly ?int $cpf,
        public readonly ?int $cnpj,
        public readonly ?string $state_registration,
        public readonly ?string $municipal_registration,
        public readonly ?string $date_birthday,
        public readonly ?int $cep,
        public readonly ?string $country,
        public readonly ?string $state,
        public readonly ?string $city,
        public readonly ?string $neighborhood,
        public readonly ?string $address,
        public readonly ?int $number,
        public readonly ?string $complement,
        public readonly int $has_login_access,
        public readonly ?string $description,
        public readonly string $enterprise_id,
        public readonly ?string $department_id,
        public readonly ?string $user_id,
    ) {}

    public static function fromRequest($data, $userID): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            sex: $data['sex'],
            phone: $data['phone'],
            cpf: $data['cpf'],
            cnpj: $data['cnpj'],
            state_registration: $data['stateRegistration'],
            municipal_registration: $data['municipalRegistration'],
            date_birthday: $data['dateBirthday'],
            cep: $data['cep'],
            country: $data['country'],
            state: $data['state'],
            city: $data['city'],
            neighborhood: $data['neighborhood'],
            address: $data['address'],
            number: $data['number'],
            complement: $data['complement'],
            description: $data['description'],
            has_login_access: $data['hasLoginAccess'],
            department_id: $data['departmentId'],
            user_id: $userID,
            enterprise_id: Auth::user()->enterprise_id,
        );
    }
}
