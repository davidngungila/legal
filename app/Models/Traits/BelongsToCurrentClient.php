<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;

trait BelongsToCurrentClient
{
    /**
     * Per-table cache of whether the underlying table has a client_id column.
     * Shared across classes using the trait, keyed by connection + table name.
     */
    protected static $clientColumnCache = [];

    /**
     * Boot the trait.
     */
    protected static function bootBelongsToCurrentClient()
    {
        static::addGlobalScope('current_client', function (Builder $builder) {
            try {
                // Single source of truth is the session client. The container
                // binding is only a fallback for non-web contexts.
                $clientId = session('current_client_id')
                    ?: (App::bound('current_client_id') ? App::make('current_client_id') : null);
                $modelClass = static::class;

                // Never apply the filter to the User model for an authenticated
                // user's own profile/operations.
                if ($modelClass === \App\Models\User::class && auth()->check()) {
                    return;
                }

                // Without a client context (e.g. CLI commands, seeders) the
                // query stays unfiltered so tooling keeps working. Web requests
                // always have a client set by the SetCurrentClient middleware.
                if (!$clientId) {
                    return;
                }

                $model = $builder->getModel();

                if (static::hasClientIdColumn($model)) {
                    // Generic enforcement: any client-owned model is scoped by
                    // its own client_id column.
                    $builder->where($model->qualifyColumn('client_id'), $clientId);
                } elseif (method_exists($modelClass, 'filterByClient')) {
                    // Column-less special cases (User pivot, parent-derived
                    // records such as SupportTicketResponse) define their own
                    // factory-builder override.
                    static::filterByClient($builder, $clientId);
                }
            } catch (\Exception $e) {
                // Fail silently - this prevents errors during testing or when client is not set
            }
        });
    }

    /**
     * Whether the model's underlying table has a client_id column.
     */
    protected static function hasClientIdColumn($model)
    {
        $key = $model->getConnection()->getName() . '|' . $model->getTable();

        if (!isset(static::$clientColumnCache[$key])) {
            static::$clientColumnCache[$key] = collect(
                $model->getConnection()->getSchemaBuilder()->getColumnListing($model->getTable())
            )->contains('client_id');
        }

        return static::$clientColumnCache[$key];
    }

    /**
     * Override the method to filter by client for models without a client_id
     * column whose ownership is derived from a parent record.
     */
    protected static function filterByClient(Builder $builder, $clientId)
    {
        // Override in the using model
    }

    /**
     * Get records without current client filter.
     */
    public static function withoutClientFilter()
    {
        return static::withoutGlobalScope('current_client');
    }

    /**
     * Set the client for this model instance.
     */
    public function setClientId($clientId)
    {
        if (property_exists($this, 'client_id')) {
            $this->client_id = $clientId;
        }

        return $this;
    }
}