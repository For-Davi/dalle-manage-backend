<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class EnterpriseScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (Auth::check()) {
            $enterpriseId = Auth::user()->enterprise_id;
            $builder->where($model->getTable().'.enterprise_id', $enterpriseId);
        }
    }
}
