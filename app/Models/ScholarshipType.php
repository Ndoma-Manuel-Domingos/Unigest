<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScholarshipType extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'description',
        'type',
        'value',
        'maximum_amount',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'maximum_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function studentScholarships()
    {
        return $this->hasMany(StudentScholarship::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
