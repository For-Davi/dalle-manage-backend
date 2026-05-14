<?php

namespace App\Repositories\Base;

use App\Models\Notification;
use App\Models\ProductAdvanced;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

abstract class BaseRepository
{
    protected Model $model;

    protected int $cacheTtlHours = 6;

    protected int $indexTtlHours = 24;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    protected function modelKey(): string
    {
        return Str::snake(class_basename($this->model));
    }

    protected function enterprisePrefix(?int $enterpriseID = null): string
    {
        $id = $enterpriseID ?? Auth::user()->enterprise_id;

        return "{$this->modelKey()}:enterprise:{$id}";
    }

    protected function enterpriseCacheKey(string $suffix = 'all'): string
    {
        return $this->enterprisePrefix().':'.md5($suffix);
    }

    protected function remember(string $key, \Closure $callback, ?int $ttlHours = null): mixed
    {
        $ttl = $ttlHours ?? $this->cacheTtlHours;

        $parts = explode(':', $key);
        $prefix = implode(':', array_slice($parts, 0, 3));
        $indexKey = 'cache_index:'.$prefix;

        $redis = Redis::connection('cache');

        $redis->sadd($indexKey, $key);
        $redis->expire($indexKey, $this->indexTtlHours * 3600);

        return Cache::remember($key, now()->addHours($ttl), $callback);
    }

    public function getAllByEnterprise(
        array $relations = [],
        array $columns = ['*'],
        array $filters = []
    ) {
        $suffix = md5(serialize(compact('relations', 'columns', 'filters')));
        $key = $this->enterpriseCacheKey($suffix);

        return $this->remember($key, function () use ($relations, $columns, $filters) {
            return $this->model->query()
                ->when(! empty($relations), fn ($q) => $q->with($relations))
                ->when(! empty($filters), fn ($q) => $q->where($filters))
                ->get($columns);
        });
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data, array $relations = []): ?Model
    {
        $record = $this->model instanceof ProductAdvanced
            ? $this->findByProduct($id, $relations)
            : $this->findById($id, $relations);

        if (! $record) {
            return null;
        }

        $record->update($data);

        return $record;
    }

    public function getAll()
    {
        return $this->model->get();
    }

    public function getAllByUser(?int $userID = null, array $relations = [])
    {
        $userId = $userID ?? Auth::user()->id;
        $suffix = md5(serialize($relations));

        $key = "notification:user:{$userId}:{$suffix}";

        return $this->remember($key, function () use ($userId, $relations) {
            return $this->model->query()
                ->with($relations)
                ->where('user_id', $userId)
                ->when($this->model instanceof Notification, fn ($q) => $q->latest())
                ->get();
        });
    }

    public function findById(int $id, array $relations = []): ?Model
    {
        $suffix = md5(serialize($relations));
        $key = "{$this->enterprisePrefix()}:id_{$id}:{$suffix}";

        return $this->remember($key, function () use ($id, $relations) {
            return $this->model->newQuery()->with($relations)->find($id);
        });
    }

    public function findByIdWithoutCache(int $id, array $relations = []): ?Model
    {
        return $this->model->newQuery()->with($relations)->find($id);
    }

    public function findByCpf(string $cpf, array $relations = []): ?Model
    {
        $suffix = md5(serialize($relations));
        $key = "{$this->enterprisePrefix()}:cpf_{$cpf}:{$suffix}";

        return $this->remember($key, function () use ($cpf, $relations) {
            return $this->model->newQuery()->with($relations)->where('cpf', $cpf)->first();
        });
    }

    public function findByCnpj(string $cnpj, array $relations = []): ?Model
    {
        $suffix = md5(serialize($relations));
        $key = "{$this->enterprisePrefix()}:cnpj_{$cnpj}:{$suffix}";

        return $this->remember($key, function () use ($cnpj, $relations) {
            return $this->model->newQuery()->with($relations)->where('cnpj', $cnpj)->first();
        });
    }

    public function findByProduct(string $productID, array $relations = []): ?Model
    {
        $suffix = md5(serialize($relations));
        $key = "{$this->enterprisePrefix()}:prodid_{$productID}:{$suffix}";

        return $this->remember($key, function () use ($productID, $relations) {
            return $this->model->newQuery()->with($relations)->where('product_id', $productID)->first();
        });
    }
}
