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

#[Fillable(['first_name', 'last_name', 'email', 'phone', 'password', 'is_active', 'last_login_at', 'last_login_ip', 'current_client_id', 'employee_id', 'department', 'position', 'profile_photo', 'bio', 'location', 'job_title'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = ['name'];

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, BelongsToCurrentClient;

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Listen for when user is saved
        static::saved(function ($user) {
            static::ensureSuperAdminBelongsToOrvion($user);
        });
    }

    /**
     * Ensure the user is attached to Orvion if they are a super admin.
     */
    public static function ensureSuperAdminBelongsToOrvion($user)
    {
        if ($user->hasRole('super_admin')) {
            $orvion = \App\Models\Client::firstOrCreate(
                ['name' => 'Orvion'],
                [
                    'email' => 'info@orvion.com',
                    'phone' => '+1234567890',
                    'industry' => 'Technology',
                    'address' => '123 Tech Street',
                    'city' => 'San Francisco',
                    'country' => 'USA',
                    'contact_person' => 'Orvion Admin',
                    'contact_title' => 'Administrator',
                    'contact_email' => 'admin@orvion.com',
                    'contact_phone' => '+1234567890',
                    'status' => 'active',
                    'subscription_plan' => 'enterprise',
                ]
            );

            $user->clients()->syncWithoutDetaching([
                $orvion->id => [
                    'role' => 'admin',
                    'is_active' => true,
                    'joined_at' => now(),
                ]
            ]);
            
            // Set current_client_id to Orvion for super admin
            if ($user->current_client_id !== $orvion->id) {
                $user->current_client_id = $orvion->id;
                $user->save();
            }
        }
    }

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
     * The client that belongs to the user.
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'current_client_id');
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
     * Get the user's settings.
     */
    public function settings()
    {
        return $this->hasOne(UserSetting::class);
    }

    /**
     * Get the user's support tickets.
     */
    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    /**
     * Get the user's profile photo URL.
     */
    public function getProfilePhotoUrlAttribute(): ?string
    {
        return $this->profile_photo ? asset('storage/' . $this->profile_photo) : null;
    }

    /**
     * Get the user's full name.
     */
    public function getNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get users for the current client.
     */
    public static function forCurrentClient()
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return static::query();
        }

        return static::where(function ($q) use ($clientId) {
            $q->where('current_client_id', $clientId)
                ->orWhereHas('clients', function ($q2) use ($clientId) {
                    $q2->where('clients.id', $clientId);
                });
        });
    }

    /**
     * Active clients that already have registered employees.
     *
     * The current-client global scope on models is intentionally ignored so the
     * result is always accurate, regardless of the client active in the session.
     */
    public static function clientsWithEmployees()
    {
        $employeeClientIds = \App\Models\Employee::query()
            ->withoutGlobalScopes()
            ->whereHas('client', function ($q) {
                $q->where('status', 'active');
            })
            ->distinct()
            ->pluck('client_id');

        return Client::where('status', 'active')
            ->whereIn('id', $employeeClientIds)
            ->orderBy('name')
            ->get();
    }

    /**
     * Resolve the best default client context for this user.
     *
     * Prefers an active tenant client that already has registered employees so
     * listing pages such as /employees are not empty just because the account
     * got defaulted to the system placeholder client.
     */
    public function resolveDefaultClient(): ?Client
    {
        $tenantClients = static::clientsWithEmployees();

        if ($this->hasRole('super_admin')) {
            return $tenantClients->first()
                ?? Client::where('status', 'active')->orderBy('name')->first()
                ?? Client::orderBy('name')->first();
        }

        $assigned = $this->clients()
            ->wherePivot('is_active', true)
            ->where('clients.status', 'active')
            ->orderBy('name')
            ->get()
            ->keyBy('id');

        $assignedWithStaff = $tenantClients->first(function (Client $client) use ($assigned) {
            return $assigned->has($client->id);
        });

        return $assignedWithStaff
            ?? $assigned->first()
            ?? $tenantClients->first()
            ?? Client::where('status', 'active')->orderBy('name')->first();
    }
}
