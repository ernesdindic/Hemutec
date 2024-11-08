<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\WorkHoursController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\SpesenController;
use App\Models\User;
use Illuminate\Http\Request;


// Dashboard route
Route::get('/dashboard', [ReportController::class, 'index'])->name('dashboard');

// Authentication routes
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// Resource routes for reports
Route::resource('reports', ReportController::class);

// User Reports
Route::get('/user-reports', [ReportController::class, 'userReports'])->name('user-reports');

//Workhours
Route::get('/workhours', [WorkHoursController::class, 'index'])->name('workhours');
//Route::get('/workhours', [WorkHoursController::class, 'index']);

// In web.php
Route::get('/workhours/{month}', [WorkHoursController::class, 'getWorkHoursForMonth'])->name('workhours.month');

Route::put('/workhours/{id}', [WorkHoursController::class, 'update']);


Route::get('/user-profile/{id}', [UserProfileController::class, 'show'])->name('user-profile');

Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');

// Route to display the Ferien management view
Route::get('/ferien', function () {
    $users = User::all(); // Fetch all users from the database
    return view('ferien', compact('users')); // Pass users to the view
})->name('ferien');

// Route to handle updating users' Ferien days
Route::post('/update-ferien', [UserProfileController::class, 'updateFerien'])->name('update.ferien');

// Route for the Ferien management view
Route::get('/ferien', [UserProfileController::class, 'showFerienManagement'])->name('ferien.management');

// Route for updating notes
Route::post('/update/ferien/notes', [UserProfileController::class, 'updateFerienNotes'])->name('update.ferien.notes');

/// Route to display the Ferien management view
Route::get('/ferien', [UserProfileController::class, 'showFerienManagement'])->name('ferien.management');

// Route to handle updating users' Ferien days
Route::post('/update-ferien', function (Request $request) {
    $ferienData = $request->input('ferien');
    
    foreach ($ferienData as $userId => $ferien) {
        $user = User::find($userId);
        if ($user) {
            $user->ferien = $ferien; // Update the ferien days
            $user->save(); // Save the changes
        }
    }

    return redirect()->route('ferien.management')->with('success', 'Ferien updated successfully!'); // Redirect back
})->name('update.ferien');

// Route for updating notes
Route::post('/update/ferien/notes', [UserProfileController::class, 'updateFerienNotes'])->name('update.ferien.notes');

//Route::get('/spesen', [SpesenController::class, 'showSpesen'])->name('spesen');

// web.php
Route::post('/spesen/store', [SpesenController::class, 'store'])->name('spesen.store');
// web.php
Route::get('/spesen', [SpesenController::class, 'index'])->name('spesen');

Route::put('/spesen/{id}', [SpesenController::class, 'update'])->name('spesen.update');
Route::delete('/spesen/{id}', [SpesenController::class, 'destroy'])->name('spesen.destroy');

Route::get('/assign-license/{userName}', [UserProfileController::class, 'assignLicenseToUser'])->name('assign.license');

















require __DIR__.'/auth.php';
