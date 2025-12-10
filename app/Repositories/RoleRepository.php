<?php

namespace App\Repositories;

use App\Models\Role;
use App\Repositories\Base\BaseRepository;

class RoleRepository extends BaseRepository
{
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }

    public function delete($id)
    {
        $role = $this->findById($id);
        if ($role) {

            return $role->delete();
        }

        return false;
    }
}
