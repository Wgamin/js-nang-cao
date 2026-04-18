<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tuition extends Model
{
    protected $fillable = [
        'user_id', 'study_class_id', 'amount', 'amount_paid',
        'status', 'payment_date', 'note',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'amount'       => 'decimal:0',
            'amount_paid'  => 'decimal:0',
        ];
    }

    /** Học sinh */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Lớp học tương ứng */
    public function studyClass(): BelongsTo
    {
        return $this->belongsTo(StudyClass::class, 'study_class_id');
    }
}
