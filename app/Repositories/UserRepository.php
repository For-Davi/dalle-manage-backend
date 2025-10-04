<?php

namespace App\Repositories;

use App\DTO\User\FilterUserDTO;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    public function __construct(public User $model) {}

    public function getAll()
    {
        return $this->model->all();
    }

    public function getAllByEnterprise($enterpriseId, array $relations = [])
    {
        $query = $this->model->where('enterprise_id', $enterpriseId);

        if (! empty($relations)) {
            $query->with($relations);
        }

        return $query->get();
    }

    public function getAllWithFilter(FilterUserDTO $filters)
    {
        $query = $this->model->where('enterprise_id', $filters->enterprise_id);

        if ($filters->name !== null) {
            $query->where('name', 'like', "%{$filters->name}%");
        }

        if ($filters->email !== null) {
            $query->where('email', 'like', "%{$filters->email}%");
        }

        if ($filters->active !== null) {
            $query->where('active', $filters->active);
        }

        if ($filters->department_id !== null) {
            $query->where('department_id', $filters->department_id);
        }

        if ($filters->role_id !== null) {
            $query->where('role_id', $filters->role_id);
        }

        return $query->get();
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function update($id, array $data)
    {
        $user = $this->findById($id);
        if ($user) {
            $user->update($data);

            return $user;
        }

        return null;
    }

    public function clearDepartment($departmentId)
    {
        $this->model->where('department_id', $departmentId)->update(['department_id' => null]);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function updateMember($id, array $data)
    {
        $user = $this->findById($id);
        if ($user) {
            if ($user->email !== $data['email']) {
                $existingEmail = $this->model->where('email', $data['email'])->first();
                if ($existingEmail) {
                    throw new \Exception('Email já registrado no sistema');
                }
            }
            $user->update($data);

            return $user;
        }

        return null;
    }

    public function updatePassword($id, array $data)
    {
        $user = $this->findById($id);
        if ($user) {
            $user->update($data);

            return $user;
        }

        return null;
    }

    public function newPassword($email, array $data)
    {
        $user = $this->findByEmail($email);
        if ($user) {
            $user->update($data);

            return $user;
        }

        return null;
    }

    private function updateInfoAccessLogin($userId)
    {
        DB::table('employees')->where('user_id', $userId)->update(['user_id' => null, 'has_login_access' => 0]);
    }

    public function delete($id, $deleteEmployee)
    {
        $user = $this->findById($id);
        if ($user) {
            if ($deleteEmployee) {
                $this->destroyEmployee($id);
            } else {
                $this->updateInfoAccessLogin($id);
            }

            return $user->delete();
        }

        return false;
    }

    private function destroyEmployee($userId)
    {
        DB::table('employees')->where('user_id', $userId)->delete();
    }

    public function updateProfilePhoto($userID, $deleteID, $addID)
    {
        $user = $this->findById($userID);
        if (! $user) {
            return null;
        }

        if ($addID && $deleteID === null) {
            $user->update(['image_id' => $addID]);

            return $user;
        }

        if ($addID && $deleteID) {

            if ($user->image_id === $deleteID) {
                $user->update(['image_id' => $addID]);
            }

            DB::table('images')->where('id', $deleteID)->delete();

            return $user;
        }

        if (! $addID && $deleteID) {
            if ($user->image_id === $deleteID) {
                $user->update(['image_id' => null]);
            }
            DB::table('images')->where('id', $deleteID)->delete();

            return $user;
        }

        return $user;
    }
}
