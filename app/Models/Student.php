<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'guardian_id',
        'name',
        'dob',
        'gender',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'dob' => 'date',
        ];
    }

    public function guardian()
    {
        return $this->belongsTo(Guardian::class);
    }

    public function classes()
    {
        return $this->belongsToMany(Classes::class, 'enrollments')
            ->withPivot(['actual_price', 'enrolled_at'])
            ->withTimestamps();
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}
