<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $role
 * @property bool $can_sign
 * @property bool $can_review
 * @property bool $is_active
 * @property mixed $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Letter> $letters
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Letter> $reviewingLetters
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Letter> $signingLetters
 * @property-read \Illuminate\Database\Eloquent\Collection<int, LetterTemplate> $templates
 * @property-read UserKey|null $activeKey
 * @property-read \Illuminate\Database\Eloquent\Collection<int, UserKey> $keys
 * @property-read \Illuminate\Database\Eloquent\Collection<int, DigitalSignature> $signatures
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AuditLog> $auditLogs
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * 
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCanSign($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCanReview($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User active()
 * @method static \Illuminate\Database\Eloquent\Builder|User canSign()
 * @method static \Illuminate\Database\Eloquent\Builder|User canReview()
 * 
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'can_sign',
        'can_review',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
            'can_sign' => 'boolean',
            'can_review' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get all letters created by this user.
     */
    public function letters(): HasMany
    {
        return $this->hasMany(Letter::class, 'created_by');
    }

    /**
     * Get all letters assigned to this user for review.
     */
    public function reviewingLetters(): HasMany
    {
        return $this->hasMany(Letter::class, 'assigned_reviewer');
    }

    /**
     * Get all letters assigned to this user for signing.
     */
    public function signingLetters(): HasMany
    {
        return $this->hasMany(Letter::class, 'assigned_signer');
    }

    /**
     * Get all templates created by this user.
     */
    public function templates(): HasMany
    {
        return $this->hasMany(LetterTemplate::class, 'created_by');
    }

    /**
     * Get the active key pair for this user.
     */
    public function activeKey(): HasOne
    {
        return $this->hasOne(UserKey::class)->where('is_active', true)->latest();
    }

    /**
     * Get all key pairs for this user.
     */
    public function keys(): HasMany
    {
        return $this->hasMany(UserKey::class);
    }

    /**
     * Get all digital signatures made by this user.
     */
    public function signatures(): HasMany
    {
        return $this->hasMany(DigitalSignature::class, 'signer_id');
    }

    /**
     * Get all audit logs for actions performed by this user.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include users who can sign documents.
     */
    public function scopeCanSign($query)
    {
        return $query->where('can_sign', true);
    }

    /**
     * Scope a query to only include users who can review documents.
     */
    public function scopeCanReview($query)
    {
        return $query->where('can_review', true);
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a boss.
     */
    public function isBoss(): bool
    {
        return $this->role === 'boss';
    }

    /**
     * Check if user is a manager.
     */
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    /**
     * Check if user has signing privileges.
     */
    public function hasSigningPrivileges(): bool
    {
        return $this->can_sign || $this->role === 'boss' || $this->role === 'admin';
    }

    /**
     * Check if user has review privileges.
     */
    public function hasReviewPrivileges(): bool
    {
        return $this->can_review || $this->role === 'manager' || $this->role === 'admin';
    }
}