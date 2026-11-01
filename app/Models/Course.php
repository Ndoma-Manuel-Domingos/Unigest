<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'faculty_id',
        'department_id',
        'name',
        'code',
        'acronym',
        'degree_type',
        'duration_years',
        'duration_semesters',
        'study_regime',
        'description',
        'is_accredited',
        'accreditation_number',
        'accreditation_date',
        'accreditation_expiry',
        'is_active',
    ];

    protected $casts = [
        'is_accredited' => 'boolean',
        'is_active' => 'boolean',
        'accreditation_date' => 'date',
        'accreditation_expiry' => 'date',
    ];

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class, 'tenant_id');
    }

    public function curricula(): HasMany
    {
        return $this->hasMany(Curriculum::class);
    }

    public function academicClasses(): HasMany
    {
        return $this->hasMany(AcademicClass::class);
    }

    public function activeCurricula(): HasMany
    {
        return $this->hasMany(Curriculum::class)
            ->where('is_active', true);
    }

    public function feeTariffs()
    {
        return $this->hasMany(FeeTariff::class);
    }

    public function enrollments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Enrollment::class);
    }
}
