<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = ['schedule_id', 'student_id', 'is_present', 'status', 'note'];

    protected function casts(): array
    {
        return [
            'is_present' => 'boolean',
        ];
    }

    /** Buổi học tương ứng */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    /** Học sinh được điểm danh */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
