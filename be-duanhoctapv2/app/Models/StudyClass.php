<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudyClass extends Model
{
    protected $table = 'classes';

    protected $fillable = ['name', 'teacher_id', 'subject_id', 'description', 'status'];

    protected function casts(): array
    {
        return [
            'status' => 'string',
        ];
    }

    /** Môn học của lớp */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /** Giáo viên phụ trách lớp */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /** Danh sách học sinh trong lớp */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'class_user', 'class_id', 'user_id');
    }

    /** Danh sách lịch học của lớp */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'class_id');
    }
}
