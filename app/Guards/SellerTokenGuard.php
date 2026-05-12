<?php

namespace App\Guards;

use App\Models\DalleAdm\SellerPersonalAccessToken;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;

class SellerTokenGuard implements Guard
{
    protected ?Authenticatable $user = null;

    public function check(): bool
    {
        return ! is_null($this->user());
    }

    public function guest(): bool
    {
        return ! $this->check();
    }

    public function user(): ?Authenticatable
    {
        if ($this->user !== null) {
            return $this->user;
        }

        $token = request()->bearerToken();

        if (! $token) {
            return null;
        }

        $accessToken = $this->resolveToken($token);

        if (! $accessToken) {
            return null;
        }

        if ($accessToken->expires_at && now()->gt($accessToken->expires_at)) {
            $accessToken->delete();

            return null;
        }

        $accessToken->forceFill(['last_used_at' => now()])->save();

        $this->user = $accessToken->tokenable;

        return $this->user;
    }

    protected function resolveToken(string $token): ?SellerPersonalAccessToken
    {
        if (! str_contains($token, '|')) {
            return SellerPersonalAccessToken::where('token', hash('sha256', $token))->first();
        }

        [$id, $plainToken] = explode('|', $token, 2);

        $instance = SellerPersonalAccessToken::find($id);

        if ($instance && hash_equals($instance->token, hash('sha256', $plainToken))) {
            return $instance;
        }

        return null;
    }

    public function id(): mixed
    {
        return $this->user()?->getAuthIdentifier();
    }

    public function validate(array $credentials = []): bool
    {
        return false;
    }

    public function hasUser(): bool
    {
        return ! is_null($this->user);
    }

    public function setUser(Authenticatable $user): static
    {
        $this->user = $user;

        return $this;
    }
}
