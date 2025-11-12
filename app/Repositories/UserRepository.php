<?php

namespace App\Repositories;

use App\DTO\User\FilterUserDTO;
use App\DTO\User\UpdateUserProfilePhotoDTO;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    public function __construct(public User $model) {}

    public function getAll()
    {
        return $this->model->all();
    }

    public function getAllByEnterprise(array $relations = [])
    {
        $query = $this->model->query();

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

    public function updateProfilePhoto(int $userID, UpdateUserProfilePhotoDTO $dto): ?object
    {
        $user = $this->findById($userID);
        if (! $user) {
            return null;
        }

        if ($dto->hasDeleteOperation() && ! $dto->hasAddOperation()) {
            return $this->handleDeleteOperation($user, $dto->photo_delete_id);
        }

        if ($dto->hasAddOperation() && ! $dto->hasDeleteOperation()) {
            return $this->handleAddOperation($user, $dto->photo_add_id);
        }

        if ($dto->hasBothOperations()) {
            return $this->handleBothOperations($user, $dto->photo_add_id, $dto->photo_delete_id);
        }

        return $user;
    }

    private function handleAddOperation(object $user, int $addID): object
    {
        $user->update(['image_id' => $addID]);

        return $user;
    }

    private function handleDeleteOperation(object $user, int $deleteID): object
    {
        if ($user->image_id === $deleteID) {
            $user->update(['image_id' => null]);
        }
        $this->deleteImage($deleteID);

        return $user;
    }

    private function handleBothOperations(object $user, int $addID, int $deleteID): object
    {
        if ($user->image_id === $deleteID) {
            $user->update(['image_id' => $addID]);
        }
        $this->deleteImage($deleteID);

        return $user;
    }

    private function deleteImage(int $imageID): void
    {
        DB::table('images')->where('id', $imageID)->delete();
    }
}
