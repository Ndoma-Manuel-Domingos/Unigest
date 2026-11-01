<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CurriculumSubjectPrerequisite extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'curriculum_subject_id',
        'prerequisite_curriculum_subject_id',
    ];

    public function curriculumSubject(): BelongsTo
    {
        return $this->belongsTo(
            CurriculumSubject::class,
            'curriculum_subject_id'
        );
    }

    public function prerequisiteSubject(): BelongsTo
    {
        return $this->belongsTo(
            CurriculumSubject::class,
            'prerequisite_curriculum_subject_id'
        );
    }
}
