<?php

namespace App\Http\Controllers;

use App\Models\WorkHours;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Make sure to import Auth for user authentication
use Illuminate\Support\Facades\DB; // Import DB for database operations

class WorkHoursController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month');
        $userId = Auth::id(); // Get the current user ID

        if ($month) {
            // Format the month to store and retrieve records
            $startDate = \Carbon\Carbon::createFromFormat('Y-m', $month)->startOfMonth();
            $endDate = \Carbon\Carbon::createFromFormat('Y-m', $month)->endOfMonth();

            // Check if records for the selected month already exist for the user
            $workhours = WorkHours::where('user_id', $userId) // Filter by user_id
                ->whereBetween('date', [$startDate, $endDate])
                ->get();

            if ($workhours->isEmpty()) {
                // Populate the table with default values for each day in the month
                $daysInMonth = $startDate->daysInMonth;

                for ($day = 1; $day <= $daysInMonth; $day++) {
                    WorkHours::create([
                        'user_id' => $userId, // Include the user_id
                        'date' => $startDate->copy()->day($day),
                        'start_time' => '08:00',
                        'break_time' => '01:00',
                        'end_time' => '17:30',
                        'description' => '',
                        'overtime_minutes' => 0, // Inicijalizuj na 0
                    ]);
                }

                // Reload workhours after inserting default values
                $workhours = WorkHours::where('user_id', $userId) // Filter again by user_id
                    ->whereBetween('date', [$startDate, $endDate])
                    ->get();
            }

            // Izračunavanje prekovremenih sati za svaki radni sat
            foreach ($workhours as $workhour) {
                $workhour->overtime_minutes = $workhour->calculateOvertime();
                $workhour->save(); // Sačuvaj izračunate prekovremene sate
            }
            $totalOvertimeMinutes = WorkHours::where('user_id', $userId)->sum('overtime_minutes');


            return view('workhours', compact('workhours', 'totalOvertimeMinutes'));
        }

        // Handle the case where no month is selected (e.g., show all work hours for the current month)
        $currentMonthStart = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();

        $workhours = WorkHours::where('user_id', $userId) // Filter by user_id
            ->whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->get();

        // Izračunavanje prekovremenih sati za svaki radni sat
        foreach ($workhours as $workhour) {
            $workhour->overtime_minutes = $workhour->calculateOvertime();
            $workhour->save(); // Sačuvaj izračunate prekovremene sate
        }
        $totalOvertimeMinutes = WorkHours::where('user_id', $userId)->sum('overtime_minutes');

         // Izračunaj ukupan broj prekovremenih minuta

        return view('workhours', compact('workhours', 'totalOvertimeMinutes'));
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|string',
            'break_time' => 'required|string',
            'end_time' => 'required|string',
            'description' => 'nullable|string',
        ]);

        // Set the authenticated user ID
        $validatedData['user_id'] = Auth::id(); 

        // Create a new Workhour record
        $workHour = WorkHours::create($validatedData);

        // Izračunaj prekovremene sate i sačuvaj ih
        $workHour->overtime_minutes = $workHour->calculateOvertime();
        $workHour->save(); // Sačuvaj ažurirane prekovremene sate

        // Redirect back with a success message
        return redirect()->route('workhours')->with('success', 'Work hours saved successfully!');
    }

    public function getWorkHoursForMonth($month)
    {
        $startDate = date('Y-m-01', strtotime($month));
        $endDate = date('Y-m-t', strtotime($month));

        $workhours = DB::table('workhours')
            ->whereBetween('date', [$startDate, $endDate]) // Make sure to use the correct column name
            ->get();

        return $workhours;
    }

    public function update(Request $request, $id)
    {
        $workHour = WorkHours::findOrFail($id); // Find the record by ID

        // Validate and update the fields dynamically
        $data = $request->only(['start_time', 'break_time', 'end_time', 'description']);
        $workHour->update($data);

        // Izračunaj prekovremene sate i sačuvaj ih
        $workHour->overtime_minutes = $workHour->calculateOvertime();
        $workHour->save(); // Sačuvaj ažurirane prekovremene sate
        
        return response()->json(['message' => 'Work hour updated successfully']);
        
    }
    
}
