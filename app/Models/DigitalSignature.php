<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\DigitalSignature
 *
 * @property int $id
 * @property int $letter_id
 * @property int $signer_id
 * @property int $user_key_id
 * @property string $signature_data
 * @property string $content_hash
 * @property string $algorithm
 * @property \Illuminate\Support\Carbon $signed_at
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read Letter $letter
 * @property-read User $signer
 * @property-read UserKey $userKey
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|DigitalSignature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DigitalSignature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DigitalSignature query()
 * @method static \Illuminate\Database\Eloquent\Builder|DigitalSignature whereLetterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DigitalSignature whereSignerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DigitalSignature whereUserKeyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DigitalSignature whereSignatureData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DigitalSignature whereContentHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DigitalSignature whereAlgorithm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DigitalSignature whereSignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DigitalSignature whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DigitalSignature whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DigitalSignature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DigitalSignature whereUpdatedAt($value)
 * @method static \Database\Factories\DigitalSignatureFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class DigitalSignature extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'letter_id',
        'signer_id',
        'user_key_id',
        'signature_data',
        'content_hash',
        'algorithm',
        'signed_at',
        'ip_address',
        'user_agent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'signed_at' => 'datetime',
    ];

    /**
     * Get the letter that was signed.
     */
    public function letter(): BelongsTo
    {
        return $this->belongsTo(Letter::class);
    }

    /**
     * Get the user who signed the letter.
     */
    public function signer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signer_id');
    }

    /**
     * Get the user key used for signing.
     */
    public function userKey(): BelongsTo
    {
        return $this->belongsTo(UserKey::class);
    }
}