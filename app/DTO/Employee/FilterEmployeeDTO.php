<?php

namespace App\DTO\Employee;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Auth;

class FilterEmployeeDTO extends BaseDTO
{
    public function __construct(
        public readonly ?string $name,
        public readonly ?string $email,
        public readonly ?string $sex,
        public readonly ?int $cpf,
        public readonly ?int $cnpj,
        public readonly ?int $active,
        public readonly ?int $has_login_access,
        public readonly ?int $department_id,
        public readonly ?int $enterprise_id,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'] !== '' ? $data['name'] : null,
            email: $data['email'] !== '' ? $data['email'] : null,
            cpf: $data['cpf'] !== '' ? $data['cpf'] : null,
            cnpj: $data['cnpj'] !== '' ? $data['cnpj'] : null,
            sex: $data['sex'],
            active: $data['active'],
            has_login_access: $data['hasLoginAccess'],
            department_id: $data['department'],
            enterprise_id: Auth::user()->enterprise_id
        );
    }
}
