<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\UserKey
 *
 * @property int $id
 * @property int $user_id
 * @property string $public_key
 * @property string $encrypted_private_key
 * @property string $key_fingerprint
 * @property \Illuminate\Support\Carbon $generated_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read User $user
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|UserKey newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserKey newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserKey query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserKey whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserKey wherePublicKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserKey whereEncryptedPrivateKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserKey whereKeyFingerprint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserKey whereGeneratedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserKey whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserKey whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserKey whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserKey whereUpdatedAt($value)
 * @method static \Database\Factories\UserKeyFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class UserKey extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'public_key',
        'encrypted_private_key',
        'key_fingerprint',
        'generated_at',
        'expires_at',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'generated_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns this key pair.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}