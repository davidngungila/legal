<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Database\Eloquent\Builder;

class ClientAwareUserProvider extends EloquentUserProvider
{
    /**
     * Authentication must not be filtered by tenant client scope.
     */
    protected function newModelQuery($model = null): Builder
    {
        return parent::newModelQuery($model)->withoutGlobalScopes();
    }
}
