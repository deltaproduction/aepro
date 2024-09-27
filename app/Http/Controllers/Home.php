<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contest;
use App\Models\Expert;
use App\Models\ContestMember;

use Illuminate\Support\Facades\DB;

class Home extends Controller
{
    public function showHome()
    {
        $user_id = auth()->id();
        $user_email = auth()->user()->email;

        $contests = Contest::where('creator_id', $user_id)->get();

        $experts = Expert::with(['level', 'contest'])
            ->where('email', $user_email)
            ->get()
            ->groupBy('email')
            ->map(function ($groupedExperts) {
                $firstExpert = $groupedExperts->first();

                return [
                    'name' => $firstExpert->name,
                    'email' => $firstExpert->email,
                    'title' => $firstExpert->contest->title,
                    'levels' => $groupedExperts->map(function ($expert) {
                        return [
                            'title' => $expert->level->title
                        ];
                    })->unique('title'),
                    'contest_id' => $firstExpert->contest->id
                ];
            })->values();

        $particips = DB::table('contest_members')
            ->join('contests', 'contest_members.contest_id', '=', 'contests.id')
            ->join('levels', 'contest_members.level_id', '=', 'levels.id')
            ->join('places', 'contest_members.place_id', '=', 'places.id')
            ->select('levels.title as lt', 'contests.title as ct', 'places.title as pt', 'contest_members.contest_id')
            ->where('contest_members.user_id', $user_id)
            ->get();


        return view('home', [
            "contests" => $contests,
            "particips" => $particips,
            "experts" => $experts
        ]);
    }
}
