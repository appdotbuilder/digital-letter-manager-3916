<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\AuditLog
 *
 * @property int $id
 * @property string $event_type
 * @property string $description
 * @property array|null $event_data
 * @property int|null $user_id
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string $auditable_type
 * @property int $auditable_id
 * @property \Illuminate\Support\Carbon $performed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read User|null $user
 * @property-read Model|\Eloquent $auditable
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereEventType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereEventData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereAuditableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereAuditableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog wherePerformedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereUpdatedAt($value)
 * @method static \Database\Factories\AuditLogFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class AuditLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'event_type',
        'description',
        'event_data',
        'user_id',
        'ip_address',
        'user_agent',
        'auditable_type',
        'auditable_id',
        'performed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'event_data' => 'array',
        'performed_at' => 'datetime',
    ];

    /**
     * Get the user who performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the auditable model.
     */
    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }
}