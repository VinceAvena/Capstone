<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * MODEL: a person who logs in. The "role" column decides which part of
 * TindaPresyo the person can use.
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_INSPECTOR = 'inspector';
    public const ROLE_VENDOR = 'vendor';
    public const ROLE_SHOPPER = 'shopper';

    public const ROLES = [
        self::ROLE_ADMIN => 'Market Administrator',
        self::ROLE_INSPECTOR => 'Market Inspector',
        self::ROLE_VENDOR => 'Vendor',
        self::ROLE_SHOPPER => 'Shopper',
    ];

    protected $fillable = ['name', 'email', 'password', 'role', 'phone', 'is_active'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',   // passwords are hashed automatically when saved
            'is_active' => 'boolean',
        ];
    }

    // ---- Role helpers -------------------------------------------------

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isInspector(): bool
    {
        return $this->role === self::ROLE_INSPECTOR;
    }

    public function isVendor(): bool
    {
        return $this->role === self::ROLE_VENDOR;
    }

    public function isShopper(): bool
    {
        return $this->role === self::ROLE_SHOPPER;
    }

    /** Market administration staff: administrator or inspector. */
    public function isStaff(): bool
    {
        return $this->isAdmin() || $this->isInspector();
    }

    public function roleLabel(): string
    {
        return self::ROLES[$this->role] ?? ucfirst($this->role);
    }

    // ---- Relationships ------------------------------------------------

    /** Stalls owned by this user (vendors only). */
    public function stalls(): HasMany
    {
        return $this->hasMany(Stall::class);
    }

    /** Complaints this user filed (shoppers only). */
    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'shopper_id');
    }

    public function evaluation(): HasOne
    {
        return $this->hasOne(Evaluation::class);
    }
}
