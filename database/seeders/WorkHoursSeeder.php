<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkHours;
use Carbon\Carbon;

class WorkHoursSeeder extends Seeder
{
    public function run()
    {
        $userId = 1; // Pretpostavljamo da imaš korisnika sa ID 1. Prilagodi po potrebi.
        $standardStartTime = '08:00';
        $standardBreakTime = '01:00';
        $standardEndTime = '17:30';
        $description = ''; // Prazan opis po defaultu

        // Get the current month
        $currentMonth = Carbon::now()->startOfMonth();
        $daysInMonth = $currentMonth->daysInMonth;

        // Loop through each day of the month
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = $currentMonth->copy()->addDays($day - 1);

            // Check if it's a weekend
            if ($date->isWeekend()) {
                // Set all values to zero for weekends
                WorkHours::create([
                    'user_id' => $userId,
                    'date' => $date->format('Y-m-d'),
                    'start_time' => '00:00',
                    'break_time' => '00:00',
                    'end_time' => '00:00',
                    'description' => $description,
                    'overtime_minutes' => 0, // Postavi prekovremene sate na 0 za vikende
                ]);
            } else {
                // Set standard work hours for weekdays
                $workHour = WorkHours::create([
                    'user_id' => $userId,
                    'date' => $date->format('Y-m-d'),
                    'start_time' => $standardStartTime,
                    'break_time' => $standardBreakTime,
                    'end_time' => $standardEndTime,
                    'description' => $description,
                ]);

                // Izračunaj i postavi prekovremene sate
                $workHour->overtime_minutes = $workHour->calculateOvertime();
                $workHour->save();
            }
        }
    }
}
