<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubjectEnrollment extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'enrollment_id',
        'curriculum_subject_id',
        'enrollment_date',
        'status',
        'final_grade',
        'observations',
        'registered_by',
    ];

    protected function casts(): array
    {
        return [
            'enrollment_date' => 'date',
            'final_grade' => 'decimal:2',
        ];
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function curriculumSubject(): BelongsTo
    {
        return $this->belongsTo(CurriculumSubject::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(AssessmentGrade::class);
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }
}
