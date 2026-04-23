<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;

trait BelongsToCurrentClient
{
    /**
     * Boot the trait.
     */
    protected static function bootBelongsToCurrentClient()
    {
        static::addGlobalScope('current_client', function (Builder $builder) {
            try {
                $clientId = session('current_client_id');
                
                if ($clientId && method_exists(static::class, 'filterByClient')) {
                    static::filterByClient($builder, $clientId);
                }
            } catch (\Exception $e) {
                // Fail silently - this prevents errors during testing or when client is not set
            }
        });
    }

    /**
     * Override the method to filter by client.
     * This should be implemented in the using model.
     */
    protected static function filterByClient(Builder $builder, $clientId)
    {
        // Override in the using model
        // Example: $builder->where('client_id', $clientId);
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
