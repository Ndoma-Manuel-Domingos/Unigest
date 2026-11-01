<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'department_id',
        'name',
        'code',
        'description',
        'type',
        'year_number',
        'semester_number',
        'credits',
        'theoretical_hours',
        'practical_hours',
        'laboratory_hours',
        'total_hours',
        'requires_prerequisite',
        'is_active',
    ];

    protected $casts = [
        'requires_prerequisite' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(
            University::class,
            'tenant_id'
        );
    }

    public function curriculumSubjects(): HasMany
    {
        return $this->hasMany(CurriculumSubject::class);
    }
}
