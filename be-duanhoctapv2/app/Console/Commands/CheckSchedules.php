<?php

namespace App\Console\Commands;

use App\Models\Schedule;
use Illuminate\Console\Command;

class CheckSchedules extends Command
{
    protected $signature = 'check:schedules';
    protected $description = 'Check schedules order';

    public function handle()
    {
        $schedules = Schedule::with('studyClass')
            ->orderBy('class_id')
            ->orderBy('start_time')
            ->get();

        $this->info("Danh sach lich hoc:");
        foreach ($schedules as $s) {
            $this->line($s->id . ' | Class: ' . ($s->studyClass->name ?? 'N/A') . ' | Buoi: ' . $s->attendance_count . ' | ' . $s->start_time);
        }
    }
}
