<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Models\Contest;

class CheckContestAffliation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $id = $request->route('contest_id');

        $contest = Contest::where('id', $id);

        if ($contest) {
            if ($contest->first()->creator_id == auth()->id()) {
                return $next($request);
            }
        }

        abort(404);
    }
}
