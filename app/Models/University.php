<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class University extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'short_name',
        'slug',
        'email',
        'phone',
        'website',
        'tax_number',
        'address',
        'city',
        'province',
        'country',
        'logo',
        'status', // pending trial active suspended expired cancelled
        'activated_at',
        'suspended_at',
    ];

    protected $casts = [
        'activated_at' => 'datetime',
        'suspended_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (University $university) {
            if (empty($university->uuid)) {
                $university->uuid = (string) Str::uuid();
            }
        });
    }

    public function academicYears(): HasMany
    {
        return $this->hasMany(AcademicYear::class, 'tenant_id');
    }

    public function academicClasses(): HasMany
    {
        return $this->hasMany(AcademicClass::class, 'tenant_id');
    }

}
