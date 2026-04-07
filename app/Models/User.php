<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Traits\BelongsToCurrentClient;

#[Fillable(['first_name', 'last_name', 'email', 'phone', 'password', 'is_active', 'last_login_at', 'last_login_ip'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The roles that belong to user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * The permissions that belong to user.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * The clients that belong to user.
     */
    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class)
            ->withPivot(['role', 'is_active', 'joined_at'])
            ->withTimestamps();
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole($role): bool
    {
        return $this->roles()->where('name', $role)->exists();
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission($permission): bool
    {
        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permission) {
                $query->where('name', $permission);
            })
            ->exists();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Filter users by current client.
     */
    protected static function filterByClient(\Illuminate\Database\Eloquent\Builder $builder, $clientId)
    {
        $builder->whereHas('clients', function ($query) use ($clientId) {
            $query->where('clients.id', $clientId)
                  ->where('client_user.is_active', true);
        });
    }

    /**
     * Get users for the current client.
     */
    public static function forCurrentClient()
    {
        $clientId = app('current_client_id');
        if (!$clientId) {
            return static::query();
        }

        return static::whereHas('clients', function ($query) use ($clientId) {
            $query->where('clients.id', $clientId)
                  ->where('client_user.is_active', true);
        });
    }
}
