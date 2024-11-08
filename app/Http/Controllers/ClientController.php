<?php

namespace App\Http\Controllers;

use App\Models\Client; // Uveri se da je importovan model Client
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'hourly_rate' => 'required|integer|min:0',
        ]);

        Client::create([
            'name' => $request->name,
            'hourly_rate' => $request->hourly_rate,
        ]);

        return redirect()->back()->with('success', 'Success');
    }
}
