<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    protected $fillable = ['class_id', 'start_time', 'end_time', 'room', 'note'];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time'   => 'datetime',
        ];
    }

    /** Lịch thuộc về lớp nào */
    public function studyClass(): BelongsTo
    {
        return $this->belongsTo(StudyClass::class, 'class_id');
    }

    /** Danh sách điểm danh của buổi học */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
