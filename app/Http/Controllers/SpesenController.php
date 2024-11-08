<?php

namespace App\Http\Controllers;

use App\Models\Spesen;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SpesenController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        $selectedMonth = $request->input('month', now()->format('Y-m'));

        // Filtriraj unose po selektovanom mesecu
        $spesen = Spesen::where('user_id', $userId)
            ->whereYear('datum', Carbon::parse($selectedMonth)->year)
            ->whereMonth('datum', Carbon::parse($selectedMonth)->month)
            ->get();

        // Izračunaj ukupne kilometre za filtrirani mesec
        $totalKilometers = $spesen->sum('kilometer');
        $totalParkgebuehr = $spesen->sum('parkgebuehr');
        $costPerKilometer = 0.70;
        $totalCost = ($totalKilometers * $costPerKilometer) + $totalParkgebuehr;
        


        return view('spesen', compact('spesen', 'selectedMonth', 'totalKilometers', 'totalCost', 'totalParkgebuehr'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'datum' => 'required|date',
            'standort' => 'required|string|max:255',
            'kilometer' => 'required|numeric|min:0',
            'parkgebuehr' => 'nullable|numeric',
        ]);

        // Postavite 'parkgebuehr' na 0 ako nije definisan
    $parkgebuehr = $request->input('parkgebuehr') ?? 0;

    Spesen::create([
        'user_id' => Auth::id(),
        'datum' => $request->datum,
        'standort' => $request->standort,
        'kilometer' => $request->kilometer,
        'parkgebuehr' => $parkgebuehr,
    ]);

        return redirect()->route('spesen')->with('success', 'Success');
    }

    public function update(Request $request, $id)
    {
        $spesen = Spesen::findOrFail($id);
        $spesen->update($request->only(['datum', 'standort', 'kilometer', 'parkgebuehr']));

        return response()->json(['success' => true]);
    }

    public function destroy($id)
{
    $spesen = Spesen::findOrFail($id);

    // Provera da li zapis pripada trenutnom korisniku
    if ($spesen->user_id !== Auth::id()) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $spesen->delete();

    return response()->json(['success' => true]);
}

}
