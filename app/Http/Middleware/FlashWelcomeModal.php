<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

use Symfony\Component\HttpFoundation\Response;

class FlashWelcomeModal
{

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Redirect to home if the visited cookie isn't found
        if(!$request->hasCookie('portfolio_visited'))
        {
            session()->flash('show_welcome_modal', true);
        }

        return $next($request);
    }
}
