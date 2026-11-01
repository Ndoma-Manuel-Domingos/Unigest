<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CurriculumSubject extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'curriculum_id',
        'subject_id',
        'year_number',
        'semester_number',
        'type',
        'credits',
        'theoretical_hours',
        'practical_hours',
        'laboratory_hours',
        'total_hours',
        'order_number',
        'is_active',
    ];

    protected $casts = [
        'year_number' => 'integer',
        'semester_number' => 'integer',
        'credits' => 'integer',
        'theoretical_hours' => 'integer',
        'practical_hours' => 'integer',
        'laboratory_hours' => 'integer',
        'total_hours' => 'integer',
        'order_number' => 'integer',
        'is_active' => 'boolean',
    ];

    public function curriculum(): BelongsTo
    {
        return $this->belongsTo(Curriculum::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function prerequisites(): HasMany
    {
        return $this->hasMany(
            CurriculumSubjectPrerequisite::class,
            'curriculum_subject_id'
        );
    }

    public function requiredBy(): HasMany
    {
        return $this->hasMany(
            CurriculumSubjectPrerequisite::class,
            'prerequisite_curriculum_subject_id'
        );
    }

    public function calculateTotalHours(): int
    {
        return $this->theoretical_hours
            + $this->practical_hours
            + $this->laboratory_hours;
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(SubjectAssessment::class);
    }

    public function schedules()
    {
        return $this->hasMany(ClassSchedule::class);
    }


    public function sessions(): HasMany
    {
        return $this->hasMany(ClassSession::class);
    }

    public function subjectEnrollments(): HasMany
    {
        return $this->hasMany(SubjectEnrollment::class);
    }
}
