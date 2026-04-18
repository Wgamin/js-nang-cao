<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'weekday',
        'start_time',
        'end_time',
    ];

    public function classes()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }
}
