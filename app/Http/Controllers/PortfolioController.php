<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

use App\Models\User;

class PortfolioController extends Controller
{

    public function switch ($role)
    {
        // Find the user for that role
        $user = User::where('role', $role)->firstOrFail();

        // Log them in
        auth()->login($user);

        // Retrieve the Dutch role title
        $userRole = auth()->user()->role->label();

        // Send them to the dashboard with the cookie
        return redirect()->route('dashboard')
            ->with('success', "Verwisseld naar een {$userRole} account");
    }
}
