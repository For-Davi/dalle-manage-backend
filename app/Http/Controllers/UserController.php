<?php

namespace App\Http\Controllers;

use App\DTO\User\FilterUserDTO;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\DeleteUserRequest;
use App\Http\Requests\User\FilterUserRequest;
use App\Http\Requests\User\NewPasswordRequest;
use App\Http\Requests\User\ResetPasswordRequest;
use App\Http\Requests\User\ShowUserRequest;
use App\Http\Requests\User\UpdateUserDataRequest;
use App\Http\Requests\User\UpdateUserPasswordRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\User\UserListResource;
use App\Jobs\SendWelcomeMailJob;
use App\Repositories\EnterpriseRepository;
use App\Repositories\UserRepository;
use App\Services\UserService;
use App\Utils\ErrorLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController
{
    public function __construct(
        protected UserService $service,
        protected UserRepository $repository,
        protected EnterpriseRepository $enterpriseRepository
    ) {}

    private function configureToken($user)
    {
        $token = $user->createToken('my-app-token');
        $token->accessToken->update([
            'expires_at' => Carbon::now()->addHours(3),
        ]);

        return $token->plainTextToken;
    }

    public function login(LoginRequest $request)
    {
        try {
            $user = $this->service->login($request);
            $user->load(['enterprise', 'image']);

            if ($user->image) {
                $user->image->url = asset($user->image->url);
            }

            $token = $this->configureToken($user);

            return response()->json([
                'user' => $user,
                'token' => $token,
                'enterprise_name' => $user->enterprise->name,
            ], 200);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao logar com usuário:', $e, $request);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function register(RegisterRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = $this->service->register($request);

            if ($user) {
                DB::commit();
                $user->load(['enterprise', 'image']);

                if ($user->image) {
                    $user->image->url = asset($user->image->url);
                }

                $token = $this->configureToken($user);

                dispatch(new SendWelcomeMailJob($user));

                return response()->json([
                    'user' => $user,
                    'token' => $token,
                    'message' => 'Cadastro realizado com sucesso',
                    'enterprise_name' => $user->enterprise->name,
                ], 201);
            }

            throw new \Exception('Falha ao criar usuário');
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao registrar com usuário:', $e, $request);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function reset(ResetPasswordRequest $request)
    {
        try {
            $result = $this->service->reset($request);

            return response()->json(['message' => $result], 200);
        } catch (\Exception $e) {

            ErrorLogger::log('Erro ao solicitar redefinição de senha:', $e, $request);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function newPassword(NewPasswordRequest $request)
    {
        try {
            DB::beginTransaction();
            $user = $this->service->newPassword($request);

            if ($user) {
                DB::commit();

                return response()->json(['message' => 'Sua senha foi redefinida'], 200);
            }

            throw new \Exception('Falha ao redefinir senha');
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao redefinir senha', $e, $request);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function updateData(UpdateUserDataRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = $this->service->updateData($request);

            if ($user) {
                DB::commit();

                $user->load(['enterprise', 'image']);

                if ($user->image) {
                    $user->image->url = asset($user->image->url);
                }

                return response()->json(['user' => $user, 'message' => 'Dados atualizados']);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao atualizar dados', $e, $request);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function updatePassword(UpdateUserPasswordRequest $request)
    {
        try {
            DB::beginTransaction();

            $password = $this->service->updatePassword($request);

            if ($password) {
                DB::commit();

                return response()->json(['message' => 'Senha atualizada']);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao atualizar senha', $e, $request);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $users = $this->repository->getAllByEnterprise(['department', 'role']);

            return response()->json(['users' => UserListResource::collection($users)], 200);

        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao listar membros da organização:', $e, $request);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function filter(FilterUserRequest $request)
    {
        try {
            $userFilterDTO = FilterUserDTO::fromRequest([
                ...$request->only(['name', 'email', 'role', 'department', 'active']),
            ]);
            $users = $this->repository->getAllWithFilter($userFilterDTO);

            return response()->json(['users' => UserListResource::collection($users)], 200);

        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao filtrar usuários da organização:', $e, $request);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function show(ShowUserRequest $request)
    {
        try {
            $user = $this->repository->findById($request->route('userID'));

            return response()->json(['user' => $user], 200);

        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar usuário:', $e, $request);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(CreateUserRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = $this->service->store($request);

            if ($user) {
                DB::commit();
                $users = $this->repository->getAllByEnterprise(['department', 'role']);

                return response()->json(['users' => UserListResource::collection($users), 'message' => 'Membro adicionado á sua organização'], 201);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao registrar membro da organização:', $e, $request);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(UpdateUserRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = $this->service->update($request);

            if ($user) {
                DB::commit();

                $users = $this->repository->getAllByEnterprise(['department', 'role']);

                return response()->json(['users' => UserListResource::collection($users), 'message' => 'Membro atualizado'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao atualizar membro da organização:', $e, $request);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(DeleteUserRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = $this->repository->delete($request->route('userID'), $request->deleteEmployee);

            if ($user) {
                DB::commit();

                $users = $this->repository->getAllByEnterprise(['department', 'role']);

                return response()->json(['users' => UserListResource::collection($users), 'message' => 'Membro excluído'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao excluir membro da organização:', $e, $request);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
