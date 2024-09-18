<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Models\ContestMember;


class CheckIfUserIsMember
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $id = $request->route('contest_id');

        $contestMember = ContestMember::where('contest_id', $id)->where('user_id', auth()->id())->first();

        if ($contestMember) {
            return $next($request);
        }

        abort(404);
    }
}
