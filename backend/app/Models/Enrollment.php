<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_id',
        'enrolled_at',
        'agreed_fee',
        'status',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'agreed_fee' => 'decimal:2',
            'enrolled_at' => 'date',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classes()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
