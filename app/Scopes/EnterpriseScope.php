<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class EnterpriseScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  Builder<Model>  $builder
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (Auth::check()) {
            $enterpriseId = Auth::user()->enterprise_id;

            $builder->where(
                $model->getTable().'.enterprise_id',
                $enterpriseId
            );
        }
    }
}
