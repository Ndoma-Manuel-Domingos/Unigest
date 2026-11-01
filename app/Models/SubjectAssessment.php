<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubjectAssessment extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'academic_class_id',
        'curriculum_subject_id',
        'title',
        'type',
        'assessment_date',
        'weight',
        'max_grade',
        'description',
        'is_published',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'assessment_date' => 'date',
            'weight' => 'decimal:2',
            'max_grade' => 'decimal:2',
            'is_published' => 'boolean',
        ];
    }

    public function academicClass(): BelongsTo
    {
        return $this->belongsTo(AcademicClass::class);
    }

    public function curriculumSubject(): BelongsTo
    {
        return $this->belongsTo(CurriculumSubject::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(AssessmentGrade::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
