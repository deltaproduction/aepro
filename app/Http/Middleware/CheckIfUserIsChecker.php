<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Models\Expert;


class CheckIfUserIsChecker
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $id = $request->route('contest_id');

        $expert = Expert::where('contest_id', $id)->where('email', auth()->user()->email)->first();

        if ($expert) {
            return $next($request);
        }

        abort(404);
    }
}
