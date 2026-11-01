<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentGrade extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'subject_assessment_id',
        'subject_enrollment_id',
        'grade',
        'observations',
        'entered_by',
    ];

    protected function casts(): array
    {
        return [
            'grade' => 'decimal:2',
        ];
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(SubjectAssessment::class, 'subject_assessment_id');
    }

    public function subjectEnrollment(): BelongsTo
    {
        return $this->belongsTo(SubjectEnrollment::class);
    }

    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
    }
}
