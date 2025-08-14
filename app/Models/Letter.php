<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * App\Models\Letter
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string $status
 * @property string|null $recipient_name
 * @property string|null $recipient_address
 * @property string|null $reference_number
 * @property int|null $template_id
 * @property int $created_by
 * @property int|null $assigned_reviewer
 * @property int|null $assigned_signer
 * @property \Illuminate\Support\Carbon|null $submitted_at
 * @property \Illuminate\Support\Carbon|null $reviewed_at
 * @property \Illuminate\Support\Carbon|null $signed_at
 * @property string|null $rejection_reason
 * @property string|null $review_notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read User $creator
 * @property-read User|null $reviewer
 * @property-read User|null $signer
 * @property-read LetterTemplate|null $template
 * @property-read DigitalSignature|null $digitalSignature
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AuditLog> $auditLogs
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|Letter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Letter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Letter query()
 * @method static \Illuminate\Database\Eloquent\Builder|Letter whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Letter whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Letter whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Letter whereRecipientName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Letter whereRecipientAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Letter whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Letter whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Letter whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Letter whereAssignedReviewer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Letter whereAssignedSigner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Letter whereSubmittedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Letter whereReviewedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Letter whereSignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Letter whereRejectionReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Letter whereReviewNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Letter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Letter whereUpdatedAt($value)
 * @method static \Database\Factories\LetterFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class Letter extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'content',
        'status',
        'recipient_name',
        'recipient_address',
        'reference_number',
        'template_id',
        'created_by',
        'assigned_reviewer',
        'assigned_signer',
        'submitted_at',
        'reviewed_at',
        'signed_at',
        'rejection_reason',
        'review_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'signed_at' => 'datetime',
    ];

    /**
     * Get the user who created this letter.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user assigned to review this letter.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_reviewer');
    }

    /**
     * Get the user assigned to sign this letter.
     */
    public function signer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_signer');
    }

    /**
     * Get the template used for this letter.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(LetterTemplate::class);
    }

    /**
     * Get the digital signature for this letter.
     */
    public function digitalSignature(): HasOne
    {
        return $this->hasOne(DigitalSignature::class);
    }

    /**
     * Get all audit logs for this letter.
     */
    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    /**
     * Generate a unique reference number for the letter.
     */
    public function generateReferenceNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $sequence = str_pad((string) ($this->id ?? 1), 4, '0', STR_PAD_LEFT);
        
        return "LTR-{$year}{$month}-{$sequence}";
    }

    /**
     * Check if the letter can be edited by the given user.
     */
    public function canBeEditedBy(User $user): bool
    {
        return $this->status === 'draft' && 
               ($this->created_by === $user->id || $user->role === 'admin');
    }

    /**
     * Check if the letter can be submitted for review.
     */
    public function canBeSubmitted(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if the letter can be approved by the given user.
     */
    public function canBeApprovedBy(User $user): bool
    {
        return in_array($this->status, ['submitted', 'under_review']) && 
               ($user->can_review || $user->role === 'manager' || $user->role === 'admin');
    }

    /**
     * Check if the letter can be signed by the given user.
     */
    public function canBeSignedBy(User $user): bool
    {
        return $this->status === 'approved' && 
               ($user->can_sign || $user->role === 'boss' || $user->role === 'admin') &&
               $this->assigned_signer === $user->id;
    }
}