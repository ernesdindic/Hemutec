<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserProfileController extends Controller
{
    
    public function show($id, Request $request)
{
    $user = User::with('reports')->findOrFail($id);
    $selectedMonth = $request->input('month', now()->format('Y-m'));
    
    // Filtriraj izvještaje za odabrani mesec
    $reports = $user->reports()
                    ->whereYear('datum', \Carbon\Carbon::parse($selectedMonth)->year)
                    ->whereMonth('datum', \Carbon\Carbon::parse($selectedMonth)->month)
                    ->get();

    // Preuzmite izveštaje za aktuelni mesec
    /* $reports = $user->reports()->whereMonth('datum', now()->month)->get(); */

    return view('user.profile', compact('user', 'reports', 'selectedMonth'));
}
    public function updateFerien(Request $request)
    {
        $ferienData = $request->input('ferien');

        foreach ($ferienData as $userId => $ferien) {
            $user = User::find($userId);
            if ($user) {
                $user->ferien = $ferien; // Update the ferien days
                $user->save(); // Save the changes
            }
        }

        return redirect()->route('ferien')->with('success', 'Ferien updated successfully.');
    }

    public function showFerienManagement()
{
    $users = User::all(); // Get all users
    // Assuming you want to get a specific note
    $ferienNotes = DB::table('users')->select('ferien_notes')->first(); 

    return view('ferien', compact('users', 'ferienNotes'));
}

public function assignLicenseToUser($userName)
{
    // Pronađite korisnika prema imenu
    $user = User::where('name', $userName)->first();

    if ($user && is_null($user->licence)) {
        $user->licence = (string) Str::uuid(); // Generirajte jedinstveni UUID kao licencu
        $user->save();
        return redirect()->back()->with('status', 'Die Lizenz wurde erfolgreich dem Benutzer zugeteilt.');
    }

    return redirect()->back()->with('status', 'Der Benutzer hat bereits eine Lizenz oder existiert nicht.');
}

public function updateFerienNotes(Request $request)
{
    $notes = $request->input('ferien_notes');

    // Update the notes for all users (this may vary depending on your logic)
    DB::table('users')->update(['ferien_notes' => $notes]);

    // Redirect to the same view to show updated notes
    return redirect()->route('ferien.management')->with('success', 'Notes updated successfully.');
}

}
