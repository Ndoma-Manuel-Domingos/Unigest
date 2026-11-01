<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'student_number',
        'nif',
        'document_type',
        'document_number',
        'name',
        'gender',
        'birth_date',
        'birth_place',
        'nationality',
        'marital_status',
        'email',
        'phone',
        'alternative_phone',
        'province',
        'municipality',
        'address',
        'guardian_name',
        'guardian_phone',
        'guardian_relationship',
        'photo',
        'observations',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function activeEnrollments(): HasMany
    {
        return $this->enrollments()->where('status', 'active');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasManyThrough(
            Payment::class,
            Invoice::class,
            'student_id',
            'invoice_id',
            'id',
            'id'
        );
    }

    public function subjectEnrollments()
    {
        return $this->hasManyThrough(
            SubjectEnrollment::class,
            Enrollment::class,
            'student_id',
            'enrollment_id'
        );
    }
}
