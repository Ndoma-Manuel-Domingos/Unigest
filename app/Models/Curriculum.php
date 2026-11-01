<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Curriculum extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'course_id',
        'name',
        'version',
        'effective_from',
        'effective_to',
        'description',
        'is_active',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
        'is_active' => 'boolean',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(CurriculumSubject::class);
    }

    public function activeSubjects(): HasMany
    {
        return $this->hasMany(CurriculumSubject::class)
            ->where('is_active', true);
    }

    public function academicClasses(): HasMany
    {
        return $this->hasMany(AcademicClass::class);
    }

    public function getTotalSubjectsAttribute(): int
    {
        return $this->subjects()->count();
    }

    public function getTotalCreditsAttribute(): int
    {
        return (int) $this->subjects()->sum('credits');
    }

    public function getTotalHoursAttribute(): int
    {
        return (int) $this->subjects()->sum('total_hours');
    }
}
