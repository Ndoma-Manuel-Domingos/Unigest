<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentScholarship extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'scholarship_type_id',
        'student_id',
        'academic_year_id',
        'approved_by',
        'start_date',
        'end_date',
        'status',
        'approved_value',
        'approved_at',
        'application_reason',
        'decision_notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_value' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function scholarshipType()
    {
        return $this->belongsTo(ScholarshipType::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isValid(): bool
    {
        return $this->status === 'approved'
            && $this->start_date->lte(today())
            && (!$this->end_date || $this->end_date->gte(today()));
    }

    public function calculateDiscount(float $amount): float
    {
        if (!$this->isValid()) {
            return 0;
        }

        $type = $this->scholarshipType;

        if ($type->type === 'percentage') {
            $discount = $amount * ((float) $type->value / 100);
        } else {
            $discount = (float) $this->approved_value;
        }

        if ($type->maximum_amount !== null) {
            $discount = min($discount, (float) $type->maximum_amount);
        }

        return round(min($discount, $amount), 2);
    }
}
