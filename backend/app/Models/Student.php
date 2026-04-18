<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_code',
        'full_name',
        'date_of_birth',
        'gender',
        'school_name',
        'school_grade',
        'guardian_id',
        'status',
        'note',
    ];

    public function guardian()
    {
        return $this->belongsTo(Guardian::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}
