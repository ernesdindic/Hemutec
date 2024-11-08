<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Client;
use App\Models\User;
use App\Models\Workhours;
use App\Models\Spesen;


class ReportController extends Controller
{
    public function index(Request $request)
{
    
    if (!auth()->check()) {
        return redirect()->route('login'); // Preusmeri na login
    }
    // Get the selected month from the request, defaulting to the current month
    $selectedMonth = $request->input('month', Carbon::now()->format('Y-m'));

    // Debugging: Log the selected month
    \Log::info('Selected Month:', [$selectedMonth]);

    // Get reports for the selected month
    // Dobijte izveštaje za selektovani mesec i trenutnog korisnika
    $reports = Report::where('user_id', auth()->id()) // Dodajte filtriranje po korisniku
                     ->whereYear('datum', Carbon::parse($selectedMonth)->year)
                     ->whereMonth('datum', Carbon::parse($selectedMonth)->month)
                     ->orderBy('created_at', 'desc') // Sort by date in descending order
                     ->get();

    // Calculate total time for the selected month excluding "pauza"
    $totalTime = $reports->where('tip_posla', '!=', 'pauza')->sum('vrijeme_rada');


    // If no reports found, set total time to 0
    if (!$totalTime) {
        $totalTime = 0;
    }

    // Dodajte ovaj kod da dobijete sve klijente
    $clients = Client::all();

    // Calculate total productive time for the selected month
    $totalProductiveTime = $reports->where('tip_posla', 'produktiv')->sum('vrijeme_rada');
    if (!$totalProductiveTime) {
        $totalProductiveTime = 0;
    }

    // Calculate total non-productive time for the selected month
    $totalNeProductiveTime = $reports->where('tip_posla', 'neproduktivan')->sum('vrijeme_rada');
    if (!$totalNeProductiveTime) {
        $totalNeProductiveTime = 0;
    }

    // Calculate total InternProductive time for the selected month
    $totalInternProductiveTime = $reports->where('tip_posla', 'interno produktivan')->sum('vrijeme_rada');
    if (!$totalInternProductiveTime) {
        $totalInternProductiveTime = 0;
    }

    // Calculate total PhoneProductive time for the selected month
    $totalPhoneProductiveTime = $reports->where('tip_posla', 'telefonsko produktivan')->sum('vrijeme_rada');
    if (!$totalPhoneProductiveTime) {
        $totalPhoneProductiveTime = 0;
    }

    // Calculate total PhoneInProductive time for the selected month
    $totalPhoneInProductiveTime = $reports->where('tip_posla', 'telefonsko neproduktivan')->sum('vrijeme_rada');
    if (!$totalPhoneInProductiveTime) {
        $totalPhoneInProductiveTime = 0;
    }

     // Calculate total Pause time for the selected month
     $totalPauseTime = $reports->where('tip_posla', 'pauza')->sum('vrijeme_rada');
     if (!$totalPauseTime) {
         $totalPauseTime = 0;
     }

     // Calculate total Weiterbildung time for the selected month
     $totalWeiterbildungTime = $reports->where('tip_posla', 'weiterbildung')->sum('vrijeme_rada');
     if (!$totalWeiterbildungTime) {
         $totalWeiterbildungTime = 0;
     }

      // Calculate total Anderes time for the selected month
      $totalAnderesTime = $reports->where('tip_posla', 'anderes')->sum('vrijeme_rada');
      if (!$totalAnderesTime) {
          $totalAnderesTime = 0;
      }

      // Calculate total E-Mail time for the selected month
      $totalEmailTime = $reports->where('tip_posla', 'e-mails')->sum('vrijeme_rada');
      if (!$totalEmailTime) {
          $totalEmailTime = 0;
      }

      // Calculate total Hemutec procesi time for the selected month
      $totalHemutecProcesiTime = $reports->where('tip_posla', 'hemutec procesi')->sum('vrijeme_rada');
      if (!$totalHemutecProcesiTime) {
          $totalHemutecProcesiTime = 0;
      }

      // Calculate total Fahrt time for the selected month
      $totalFahrtTime = $reports->where('tip_posla', 'fahrt')->sum('vrijeme_rada');
      if (!$totalFahrtTime) {
          $totalFahrtTime = 0;
      }

    // Calculate total profit based on hourly rates for each client only for productive tasks
    $totalProductivProfit = 0;

    // Get unique client names from productive reports
    $productiveReports = $reports->whereIn('tip_posla', ['produktiv', 'telefonsko produktivan']);
    $clientNames = $productiveReports->pluck('ime_stranke')->unique();

    foreach ($clientNames as $clientName) {
        $client = Client::where('name', $clientName)->first();
        if ($client) {
            // Calculate profit for each client
            $clientTotalTime = $productiveReports->where('ime_stranke', $clientName)->sum('vrijeme_rada');
            $totalProductivProfit += $clientTotalTime * $client->hourly_rate; // Using hourly_rate from the client
        }
    }

    // Calculate total profit based on hourly rates for each client only for productive tasks
    $totalInternProductivProfit = 0;

    // Get unique client names from productive reports
    $productiveReports = $reports->where('tip_posla', 'interno produktivan');
    $clientNames = $productiveReports->pluck('ime_stranke')->unique();

    foreach ($clientNames as $clientName) {
        $client = Client::where('name', $clientName)->first();
        if ($client) {
            // Calculate profit for each client
            $clientTotalTime = $productiveReports->where('ime_stranke', $clientName)->sum('vrijeme_rada');
            $totalInternProductivProfit += $clientTotalTime * $client->hourly_rate; // Using hourly_rate from the client
        }
    }
            

        // Pass the data to the view
        return view('dashboard', compact('reports', 'totalTime', 'totalProductiveTime', 'totalNeProductiveTime', 'selectedMonth', 'totalProductivProfit', 'totalInternProductivProfit', 'clients', 'totalInternProductiveTime', 'totalPhoneProductiveTime', 'totalPhoneInProductiveTime', 'totalPauseTime', 'totalWeiterbildungTime', 'totalAnderesTime', 'totalEmailTime', 'totalHemutecProcesiTime', 'totalFahrtTime'));
    
    }

    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'datum' => 'nullable|date',
            'vrijeme_rada' => 'nullable|string',
            'ime_stranke' => 'nullable|string',
            'tip_posla' => 'nullable|in:produktiv,neproduktivan,interni posao,interno produktivan,telefonsko produktivan,telefonsko neproduktivan,pauza,weiterbildung,anderes,e-mails,hemutec procesi,fahrt', // Add new job types here
            'opis_rada' => 'nullable|string',  // Validate opis_rada
        ]);

        // Fetch the hourly rate based on the selected client
        $client = Client::where('name', $request->input('ime_stranke'))->first();
        $hourlyRate = $client ? $client->hourly_rate : 0;

        // Checkbox handling for selectline
        $selectline = $request->input('selectline') === 'on';

        // Create a new report
        Report::create([
            'datum' => $request->input('datum'),
            'vrijeme_rada' => $request->input('vrijeme_rada'),
            'ime_stranke' => $request->input('ime_stranke'),
            'opis_rada' => $request->input('opis_rada'),  // Add opis_rada
            'tip_posla' => $request->input('tip_posla'),
            'selectline' => $selectline,
            'user_id' => auth()->id(), // Dodajte ID trenutnog korisnika
            // If you want to store profit, you can calculate it here:
            // 'profit' => $hourlyRate * $request->input('vrijeme_rada'), // Ensure vrijeme_rada is in the right format
        ]);

        return response()->json(['message' => 'Report created successfully']);
    }

    public function update(Request $request, $id)
    {
        // Log the incoming request data for debugging
        \Log::info('Updating report:', $request->all());
    
        // Validate the incoming data
        $request->validate([
            'selectline' => 'nullable|boolean', // Allow selectline to be nullable
            'opis_rada' => 'nullable|string', // Keep as nullable
            'vrijeme_rada' => 'nullable|string', // Keep as nullable
        ]);
    
        // Find the report by ID and update the necessary fields
        $report = Report::findOrFail($id);
        
        // Update selectline only if it's provided
        if ($request->has('selectline')) {
            $report->selectline = $request->input('selectline');
        }
    
        // Update opis_rada only if provided (allowing for null to clear the field)
        if ($request->has('opis_rada')) {
            $report->opis_rada = $request->input('opis_rada');
        }

        // Update opis_rada only if provided (allowing for null to clear the field)
        if ($request->has('vrijeme_rada')) {
            $report->vrijeme_rada = $request->input('vrijeme_rada');
        }
        
        $report->save();
    
        return response()->json(['message' => 'Report updated successfully']);
    }

    public function destroy($id)
{
    // Find the report by ID and ensure the user is authorized to delete it
    $report = Report::where('id', $id)
                    ->where('user_id', auth()->id()) // Ensure only the report owner can delete it
                    ->first();

    if ($report) {
        $report->delete(); // Delete the report
        return response()->json(['message' => 'Report deleted successfully']);
    }

    return response()->json(['message' => 'Report not found or you are not authorized to delete it'], 404);
}


    public function edit($id)
    {
        $report = Report::findOrFail($id);
        return response()->json($report);
    }


    
    /* public function userReports()
{
    $users = User::all(); // ili logika za filtriranje korisnika
    return view('user_reports', compact('users'));
} */

public function userReports(Request $request)
{
    // Get the selected month from the request, defaulting to the current month
    $selectedMonth = $request->input('month', Carbon::now()->format('Y-m'));

    $costPerKilometer = 0.70;

    // Get all users with their reports for the selected month
    $users = User::with(['reports' => function ($query) use ($selectedMonth) {
        $query->whereYear('datum', Carbon::parse($selectedMonth)->year)
              ->whereMonth('datum', Carbon::parse($selectedMonth)->month);
    }, 'workhours'])->get();

    $profits = []; // Initialize an array to store profits

    foreach ($users as $user) {
        // Calculate total overtime minutes for each user
        $user->totalOvertimeMinutes = $user->workhours->sum('overtime_minutes');

        // Get reports for this user in the selected month
        $reports = $user->reports;

        // Izračunaj ukupni trošak vožnje za ovaj mjesec
        $monthlySpesen = Spesen::where('user_id', $user->id)
            ->whereYear('datum', Carbon::parse($selectedMonth)->year)
            ->whereMonth('datum', Carbon::parse($selectedMonth)->month)
            ->sum('kilometer');

        $monthlyParkgebuehr = Spesen::where('user_id', $user->id)
            ->whereYear('datum', Carbon::parse($selectedMonth)->year)
            ->whereMonth('datum', Carbon::parse($selectedMonth)->month)
            ->sum('parkgebuehr');

        $user->totalCost = ($monthlySpesen * $costPerKilometer) + $monthlyParkgebuehr;

        // Calculate profit for each user
        $totalProductivProfit = 0;
        $productiveReports = $reports->whereIn('tip_posla', ['produktiv', 'telefonsko produktivan']);
        $clientNames = $productiveReports->pluck('ime_stranke')->unique();

        foreach ($clientNames as $clientName) {
            $client = Client::where('name', $clientName)->first();
            if ($client) {
                // Calculate profit for each client
                $clientTotalTime = $productiveReports->where('ime_stranke', $clientName)->sum('vrijeme_rada');
                $totalProductivProfit += $clientTotalTime * $client->hourly_rate;
            }
        }

        // Store the total profit in a custom property for each user
        $user->totalProductivProfit = $totalProductivProfit;

        // Store profits in the profits array
        $profits[] = [
            'user' => $user,
            'profit' => $totalProductivProfit, // Correctly reference the totalProductivProfit
        ];

        $internprofit = []; // Initialize an array to store internprofit

        // Calculate profit for each user
        $totalInternProductivProfit = 0;
        $internProductiveReports = $reports->where('tip_posla', 'interno produktivan');
        $clientNames = $internProductiveReports->pluck('ime_stranke')->unique();

        foreach ($clientNames as $clientName) {
            $client = Client::where('name', $clientName)->first();
            if ($client) {
                // Calculate profit for each client
                $clientTotalTime = $internProductiveReports->where('ime_stranke', $clientName)->sum('vrijeme_rada');
                $totalInternProductivProfit += $clientTotalTime * $client->hourly_rate;
            }
        }

        // Store the total profit in a custom property for each user
        $user->totalInternProductivProfit = $totalInternProductivProfit;

        // Store internprofit in the internprofit array
        $internprofit[] = [
            'user' => $user,
            'internprofit' => $totalInternProductivProfit, // Correctly reference the totalProductivProfit
        ];
    }

    // Find the user with the highest profit
    $highestProfitUser = collect($profits)->sortByDesc('profit')->first()['user'] ?? null; // Use collect to sort

    // Pass data to the view
    return view('user_reports', compact('users', 'selectedMonth', 'highestProfitUser'));
}














}
