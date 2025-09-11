<?php

namespace App\Services;

use App\DTO\Employee\StartEmployeeDTO;
use App\DTO\Enterprise\EnterpriseStartDTO;
use App\DTO\Role\RoleStartDTO;
use App\DTO\Setting\Appearance\CreateSettingAppearanceDTO;
use App\DTO\Setting\System\CreateSettingSystemDTO;
use App\DTO\User\CreateUserDTO;
use App\DTO\User\UpdateProfileDataDTO;
use App\DTO\User\UpdateProfilePasswordDTO;
use App\DTO\User\UpdateUserDTO;
use App\DTO\User\UserStartDTO;
use App\Helpers\UserHelper;
use App\Jobs\SendInviteUserEmailJob;
use App\Jobs\SendResetPasswordEmail;
use App\Models\PasswordResetToken;
use App\Repositories\EmployeeRepository;
use App\Repositories\EnterpriseRepository;
use App\Repositories\RoleRepository;
use App\Repositories\SettingAppearanceRepository;
use App\Repositories\SettingSystemRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function __construct(
        protected UserRepository $repository,
        protected EnterpriseRepository $enterpriseRepository,
        protected RoleRepository $roleRepository,
        protected EmployeeRepository $employeeRepository,
        protected SettingAppearanceRepository $settingAppearanceRepository,
        protected SettingSystemRepository $settingSystemRepository
    ) {}

    public function login($request)
    {
        $user = $this->repository->findByEmail($request->email);

        $this->hasUser($user);
        UserHelper::checkPassword($user, $request->password);
        UserHelper::checkUserActive($user);
        UserHelper::clearTokenReset($user);

        return $user;
    }

    private function hasUser($user)
    {
        if (! $user) {
            throw ValidationException::withMessages([
                'email' => ['Credenciais não constam em nosso registro.'],
            ]);
        }
    }

    private function createUser($userDTO)
    {
        return $this->repository->create($userDTO);
    }

    private function updateUser($userId, $userDTO)
    {
        return $this->repository->update($userId, $userDTO);
    }

    private function createEnterprise($enterpriseDTO)
    {
        return $this->enterpriseRepository->create($enterpriseDTO);
    }

    private function createSettingAppearance($enterpriseID)
    {
        $settingAppearanceDTO = CreateSettingAppearanceDTO::fromRequest(['enterpriseID' => $enterpriseID]);

        $this->settingAppearanceRepository->create($settingAppearanceDTO->toArray());
    }

    private function createSettingSystem($enterpriseID)
    {
        $settingSystemDTO = CreateSettingSystemDTO::fromRequest(['enterpriseID' => $enterpriseID]);

        $this->settingSystemRepository->create($settingSystemDTO->toArray());
    }

    private function createEmployee($employeeDTO)
    {
        return $this->employeeRepository->create($employeeDTO);
    }

    private function startRole($roleDTO)
    {
        return $this->roleRepository->create($roleDTO);
    }

    public function updateProfileData($userId, $profileDataDTO) {}

    public function register($request)
    {
        $enterpriseDTO = EnterpriseStartDTO::fromRequest($request->only(['nameEnterprise']));
        $enterprise = $this->createEnterprise($enterpriseDTO->toArray());

        $this->createSettingAppearance($enterprise->id);
        $this->createSettingSystem($enterprise->id);

        $roleDTO = RoleStartDTO::fromRequest(['enterprise_id' => $enterprise->id]);
        $role = $this->startRole($roleDTO->toArray());

        $userDTO = UserStartDTO::fromRequest([
            ...$request->only(['name', 'password', 'email']),
            'enterpriseID' => $enterprise->id,
            'roleID' => $role->id,
        ]);

        return $this->createUser($userDTO->toArray());
    }

    public function reset($request)
    {
        $user = $this->repository->findByEmail($request->input('email'));

        if ($user) {
            $token = app('auth.password.broker')->createToken($user);
            $this->setTypePassword($user->email, 'reset');
            SendResetPasswordEmail::dispatch($user, $token);
        }

        return 'Caso o e-mail esteja em nosso cadastro, você receberá as instruções para redefinição de senha.';
    }

    public function newPassword($request)
    {
        $register = PasswordResetToken::where('token', $request->input('token'))
            ->first();

        if (! $register) {
            return response()->json(['error' => 'Token inválido.'], 400);
        }

        if ($register->type === 'reset') {

            $isExpired = Carbon::parse($register->created_at)->addMinutes(30)->isPast();

            if ($isExpired) {
                throw ValidationException::withMessages([
                    'token' => ['Token expirado'],
                ]);
            }

        }

        $data = ['password' => Hash::make($request->input('password'))];
        $result = $this->repository->newPassword($register->email, $data);

        $register->delete();

        return $result;
    }

    public function store($request)
    {
        $userDTO = CreateUserDTO::fromRequest([
            ...$request->only(['name', 'password', 'email', 'roleId', 'departmentId']),
            'enterprise_id' => $request->get('enterprise_id'),
        ]);

        $user = $this->createUser($userDTO->toArray());

        $admin = $request->user();
        $enterprise = $this->enterpriseRepository->findById($request->get('enterprise_id'));
        $token = app('auth.password.broker')->createToken($user);

        $this->setTypePassword($user->email, 'invite');

        SendInviteUserEmailJob::dispatch($user, $admin, $enterprise, $token);

        if ($request->createEmployee) {
            $employeeDTO = StartEmployeeDTO::fromRequest([
                ...$request->only([
                    'name',
                    'email',
                    'departmentId',
                ]),
                'userId' => $user->id,
                'hasLoginAccess' => 1,
                'enterpriseId' => $request->get('enterprise_id'),
            ]);

            $this->createEmployee($employeeDTO->toArray());
        }

        return true;
    }

    private function setTypePassword($email, $type)
    {
        $resetRecord = PasswordResetToken::where('email', $email)
            ->latest()
            ->first();
        if ($resetRecord) {
            $resetRecord->update(['type' => 'reset']);
        }
    }

    public function update($request)
    {
        $userDTO = UpdateUserDTO::fromRequest([
            ...$request->only(['name', 'email', 'roleId', 'departmentId', 'active']),
        ]);

        return $this->updateUser($request->id, $userDTO->toArray());
    }

    public function updateDataProfile($request)
    {

        UserHelper::existsEmail(
            $request->user(),
            $request->email,
        );

        $profileDataDTO = UpdateProfileDataDTO::fromRequest(
            $request->only(['name', 'email']),
        );

        return $this->repository->updateProfileData($request->user()->id, $profileDataDTO->toArray());
    }

    public function updatePasswordProfile($request)
    {
        UserHelper::isPasswordEqual(
            $request->user(),
            $request->current_password
        );

        $profilePasswordDTO = UpdateProfilePasswordDTO::fromRequest([
            'new_password' => Hash::make($request->new_password),
        ]);

        return $this->repository->updateProfilePassword($request->user()->id, $profilePasswordDTO->toArray());
    }
}
