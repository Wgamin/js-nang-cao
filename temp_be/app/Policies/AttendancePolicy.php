<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Schedule;

class AttendancePolicy
{
    /**
     * Giáo viên chỉ được điểm danh cho lớp mình dạy.
     */
    public function takeAttendance(User $user, Schedule $schedule): bool
    {
        return $user->id === $schedule->studyClass->teacher_id;
    }
}
