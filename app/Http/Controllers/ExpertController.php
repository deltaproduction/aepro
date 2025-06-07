<?php

namespace App\Http\Controllers;

use App\Models\Appeal;
use Illuminate\Http\Request;

use App\Models\Contest;
use App\Models\ContestMember;
use App\Models\ContestOption;
use App\Models\Level;
use App\Models\Place;
use App\Models\Grade;
use App\Models\Expert;
use PhpOption\Option;

class ExpertController extends Controller
{
    public function showContestCheck($contest_id) {
        $contest = Contest::findOrFail($contest_id);
        $expert = Expert::where('email', auth()->user()->email)->where('contest_id', $contest_id)->first();

        return view('checker.contest', [
            "contest_id" => $contest_id,
            "contest_title" => $contest->title,
            "at" => $contest->application_type,
            "status" => $expert->expert_status,
            "contest_members" => $expert->contestMembers,
            "c_member" => null,
            "expert_id" => $expert->id
        ]);
    }

    public function showUserContestCheck($contest_id, $contest_member_id) {
        $contest = Contest::findOrFail($contest_id);
        $expert = Expert::where('email', auth()->user()->email)->first();

        $contestMember = ContestMember::findOrFail($contest_member_id);
        $optionId = $contestMember->option_id;
        $option = ContestOption::findOrFail($optionId);

        if ($contestMember->expert_id == $expert->id && $contestMember->contest_id == $contest_id) {
            return view('checker.contest', [
                "contest_id" => $contest_id,
                "contest_title" => $contest->title,
                "at" => $contest->application_type,
                "status" => $expert->expert_status,
                "contest_members" => $expert->contestMembers,
                "c_member" => $contestMember,
                "scans" => $contestMember->scans()->get(),
                "expert_id" => $expert->id,
                "tasks" => $option->tasks()
                    ->join('tasks', 'task_prototypes.task_id', '=', 'tasks.id')
                    ->select('tasks.id', 'number', 'task_text', 'task_answer', 'max_rate')
                    ->orderBy('number')
                    ->get()
            ]);
        } else {
            abort(404);
        }
    }

    public function agree(Request $request) {
        $validatedData = $request->validate([
            'agree_accept' => 'required|string',
            'contest_id' => 'required|numeric',
            'expert_id' => 'required|numeric'
        ]);

        if ($validatedData["agree_accept"]) {
            $expert = Expert::where("email", auth()->user()->email)->where("contest_id", $validatedData["contest_id"])->first();

            $expert->expert_status = 1;
            $expert->save();
        }

        return redirect()->back();
    }

    public function saveGrades(Request $request) {
        $validatedData = $request->validate([
            'contest_id' => 'required|numeric',
            'contest_member_id' => 'required|numeric'
        ]);

        $contest = Contest::findOrFail($validatedData['contest_id']);
        $contestMember = ContestMember::findOrFail($validatedData['contest_member_id']);
        $expert = Expert::findOrFail($contestMember->expert_id)->where('email', auth()->user()->email);

        $grades = !$contestMember->grades()->count();

        if ($contestMember->contest_id == $validatedData['contest_id']) {
            $optionId = $contestMember->option_id;
            $option = ContestOption::findOrFail($optionId);

            $tasks = $option->tasks()->select("task_id")->get();

            if ($expert->exists()) {
                foreach ($tasks as $task) {
                    $score = $request["task_{$task->task_id}"];

                    if ($grades) {
                        Grade::create([
                            'contest_member_id' => $contestMember->id,
                            'task_id' => $task->task_id,
                            'score' => $score,
                            'final_score' => $score
                        ]);
                    } else {
                        $grade = Grade::where('contest_member_id', $contestMember->id)
                            ->where('task_id', $task->task_id)->first();

                        $grade->score = $score;
                        $grade->final_score = $score;
                        $grade->save();
                    }
                }

            } else if (auth()->id() === $contest->creator_id and $contestMember->appeal()->exists()) {
                foreach ($tasks as $task) {
                    $score = $request["task_{$task->task_id}"];

                    if ($grades) {
                        Grade::create([
                            'contest_member_id' => $contestMember->id,
                            'task_id' => $task->task_id,
                            'score' => $score,
                            'final_score' => $score
                        ]);
                    } else {
                        $grade = Grade::where('contest_member_id', $contestMember->id)
                            ->where('task_id', $task->task_id)->first();

                        $grade->final_score = $score;
                        $grade->save();
                    }
                }

                $appeal = $contestMember->appeal()->first();
                $appeal->changed = true;
                $appeal->save();
            }
        }

        return redirect()->back();
    }

    public function requestNewWork(Request $request) {
        $validatedData = $request->validate([
            'contest_id' => 'required|numeric',
            'expert_id' => 'required|numeric'
        ]);

        $expert = Expert::findOrFail($validatedData['expert_id']);

        if ($expert !== null) {
            $newContestMember = ContestMember::where("absence", 0)
                ->where("not_finished", 0)
                ->join('experts', 'experts.level_id', '=', 'contest_members.level_id')
                ->where("contest_members.contest_id", $validatedData['contest_id'])
                ->where("contest_members.expert_id", null)
                ->where('experts.email', auth()->user()->email)
                ->select('contest_members.id', 'contest_members.expert_id', 'contest_members.absence', 'contest_members.not_finished')
                ->first();

            if ($newContestMember) {
                $newContestMember->expert_id = $expert->id;
                $newContestMember->save();
            }
        }
        return redirect()->back();
    }

    public function refuseToWork(Request $request) {
        $validatedData = $request->validate([
            'contest_id' => 'required|numeric',
            'contest_member_id' => 'required|numeric'
        ]);

        $contestMember = ContestMember::findOrFail($validatedData['contest_member_id']);
        $expert = Expert::findOrFail($contestMember->expert_id)->where('email', auth()->user()->email);

        if ($expert !== null) {
            $contestMember->grades()->delete();

            $contestMember->expert_id = 0;
            $contestMember->save();
        }

        return redirect()->route('contestCheck.show', [
            "contest_id" => $contestMember->contest_id
        ]);
    }
}
