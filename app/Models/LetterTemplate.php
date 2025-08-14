<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\LetterTemplate
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $content
 * @property array|null $placeholders
 * @property bool $is_active
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read User|null $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Letter> $letters
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|LetterTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LetterTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LetterTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|LetterTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LetterTemplate whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LetterTemplate whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LetterTemplate wherePlaceholders($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LetterTemplate whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LetterTemplate whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LetterTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LetterTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LetterTemplate active()
 * @method static \Database\Factories\LetterTemplateFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class LetterTemplate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'content',
        'placeholders',
        'is_active',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'placeholders' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user who created this template.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all letters using this template.
     */
    public function letters(): HasMany
    {
        return $this->hasMany(Letter::class, 'template_id');
    }

    /**
     * Scope a query to only include active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}