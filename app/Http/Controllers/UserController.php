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
use App\Jobs\Email\SendWelcomeMailJob;
use App\Models\User;
use App\Repositories\EnterpriseRepository;
use App\Repositories\UserRepository;
use App\Services\UserService;
use App\Utils\ErrorLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class UserController extends BaseController
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
        return $this->safeExecute(function () use ($request) {
            $user = $this->service->login($request);
            $user->load(['enterprise', 'enterprise.subscription', 'image', 'role.permissions']);
            if ($user->image) {
                $user->image->url = asset($user->image->url);
            }
            $token = $this->configureToken($user);

            return response()->json([
                'user' => $user,
                'token' => $token,
                'enterprise_name' => $user->enterprise->name,
            ], 200);
        }, 'Erro ao logar com usuário', $request);
    }

    public function register(RegisterRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $user = $this->service->register($request);
            $user->load(['enterprise', 'image', 'role.permissions']);
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
        }, 'Erro ao registrar com usuário', $request);
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->stateless()
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            $user = User::where('email', $googleUser->email)->first();

            if (! $user) {
                DB::beginTransaction();
                $data = new Request([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'password' => Str::random(16),
                    'nameEnterprise' => 'Empresa de '.$googleUser->name,
                    'sellerCode' => null,
                    'google_id' => $googleUser->id,
                ]);
                $user = $this->service->register($data);
                DB::commit();
                dispatch(new SendWelcomeMailJob($user));
            }

            $token = $this->configureToken($user);

            return redirect(rtrim(config('app.url'), '/')."/auth?token={$token}");
        } catch (\Exception $e) {
            DB::rollBack();
            ErrorLogger::critical('Erro no Callback Google', $e);

            return redirect(rtrim(config('app.url'), '/').'/login?error');
        }
    }

    public function reset(ResetPasswordRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $result = $this->service->reset($request);

            return response()->json(['message' => $result], 200);
        }, 'Erro ao solicitar redefinição de senha', $request);
    }

    public function newPassword(NewPasswordRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->service->newPassword($request);

            return response()->json(['message' => 'Sua senha foi redefinida'], 200);
        }, 'Erro ao redefinir senha', $request);
    }

    public function updateData(UpdateUserDataRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $user = $this->service->updateData($request);
            $user->load(['enterprise', 'image']);
            if ($user->image) {
                $user->image->url = asset($user->image->url);
            }

            return response()->json(['user' => $user, 'message' => 'Dados atualizados']);
        }, 'Erro ao atualizar dados', $request);
    }

    public function updatePassword(UpdateUserPasswordRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->service->updatePassword($request);

            return response()->json(['message' => 'Senha atualizada']);
        }, 'Erro ao atualizar senha', $request);
    }

    public function index(Request $request)
    {
        return $this->safeExecute(function () {
            $users = $this->repository->getAllByEnterprise(['department', 'role']);

            return response()->json(['users' => UserListResource::collection($users)], 200);
        }, 'Erro ao listar membros da organização', $request);
    }

    public function filter(FilterUserRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $userFilterDTO = FilterUserDTO::fromRequest($request);
            $users = $this->repository->getAllWithFilter($userFilterDTO);

            return response()->json(['users' => UserListResource::collection($users)], 200);
        }, 'Erro ao filtrar usuários da organização', $request);
    }

    public function show(ShowUserRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $user = $this->repository->findById($request->route('userID'));

            return response()->json(['user' => $user], 200);
        }, 'Erro ao buscar usuário', $request);
    }

    public function store(CreateUserRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('user.create');
            $this->service->store($request);
            $users = $this->repository->getAllByEnterprise(['department', 'role']);

            return response()->json(['users' => UserListResource::collection($users), 'message' => 'Membro adicionado á sua organização'], 201);
        }, 'Erro ao registrar membro da organização', $request);
    }

    public function update(UpdateUserRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('user.update');
            $this->service->update($request);
            $users = $this->repository->getAllByEnterprise(['department', 'role']);

            return response()->json(['users' => UserListResource::collection($users), 'message' => 'Membro atualizado'], 200);
        }, 'Erro ao atualizar membro da organização', $request);
    }

    public function destroy(DeleteUserRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('user.delete');
            $this->repository->delete($request->route('userID'), $request->deleteEmployee);
            $users = $this->repository->getAllByEnterprise(['department', 'role']);

            return response()->json(['users' => UserListResource::collection($users), 'message' => 'Membro excluído'], 200);
        }, 'Erro ao excluir membro da organização', $request);
    }
}
