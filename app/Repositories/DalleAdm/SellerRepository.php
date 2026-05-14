<?php

namespace App\Repositories\DalleAdm;

use App\Models\DalleAdm\Seller;
use App\Repositories\Base\BaseRepository;

class SellerRepository extends BaseRepository
{
    public function __construct(Seller $model)
    {
        parent::__construct($model);
    }

    public function findByCode(string $code)
    {
        return $this->model->where('code', $code)->first();
    }

    public function findByCpfWithoutCache(string $cpf)
    {
        return $this->model->where('cpf', $cpf)->first();
    }

    public function findByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function newPassword($email, array $data)
    {
        $seller = $this->findByEmail($email);
        if ($seller) {
            $seller->update($data);

            return $seller;
        }

        return null;
    }
}
