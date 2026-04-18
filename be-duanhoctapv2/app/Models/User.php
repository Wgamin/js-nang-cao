<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'address', 'parent_id',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /** Giáo viên có nhiều lớp dạy */
    public function teachingClasses()
    {
        return $this->hasMany(StudyClass::class, 'teacher_id');
    }

    /** Học sinh thuộc nhiều lớp */
    public function enrolledClasses()
    {
        return $this->belongsToMany(StudyClass::class, 'class_user', 'user_id', 'class_id');
    }

    /** Phụ huynh của học sinh này */
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /** Danh sách con của phụ huynh */
    public function children()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    /** Lịch sử điểm danh của học sinh */
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }

    /** Học phí của học sinh */
    public function tuitions()
    {
        return $this->hasMany(Tuition::class, 'user_id');
    }
}
