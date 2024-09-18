<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Contest;
use App\Models\ContestMember;
use App\Models\Level;
use App\Models\Place;
use App\Models\Expert;

class ExpertController extends Controller
{
    public function showContestCheck($contest_id) {
        $contest = Contest::findOrFail($contest_id);
        $contestMember = ContestMember::where('contest_id', $contest_id)->first();

        if ($contestMember) {
            $level = Level::findOrFail($contestMember->level_id);
            $place = Place::findOrFail($contestMember->place_id);
            $expert = Expert::where('email', auth()->user()->email)->first();

            return view('checker.contest', [
                "contest_id" => $contest_id,
                "level_id" => $level->id,
                "contest_title" => $contest->title,
                "level_title" => $level->title,
                "at" => $contest->application_type,
                "status" => $expert->expert_status
            ]);
        } else {
            abort(404);
        } 
    }

    public function agree(Request $request) {
        $validatedData = $request->validate([
            'agree_accept' => 'required|string'
        ]);

        if ($validatedData["agree_accept"]) {
            $expert = Expert::where("email", auth()->user()->email)->first();

            $expert->expert_status = 1;
            $expert->save();
        }

        return redirect()->back();
    }

}
