<?php

namespace App\Http\Controllers\Contest;

use App\Http\Controllers\Controller;

use Barryvdh\DomPDF\Facade\Pdf as PDF;

use App\Models\Contest;
use App\Models\Place;
use App\Models\Level;
use App\Models\Task;
use App\Models\Expert;
use App\Models\Auditorium;
use App\Models\ContestMember;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;


class ContestController extends Controller
{
    public function getContest(Request $request)
    {
        $validatedData = $request->validate([
            'contest_code' => 'required|numeric|min:0|max:9999999|exists:contests,contest_code'
        ]);

        $contest = Contest::where('contest_code', $validatedData['contest_code'])->first();
        $userId = auth()->id();

        if ($contest) {
            if (!ContestMember::where('user_id', $userId)->where('contest_id', $contest->id)->exists()) {
                $levels = Level::where('contest_id', $contest->id)->select('id', 'title')->get();
                $places = Place::where('contest_id', $contest->id)->select('id', 'title', 'locality')->get();

                return response()->json([
                    'contest_code' => $validatedData['contest_code'],
                    'levels' => $levels,
                    'places' => $places
                ]);
            } else {
                return response()->json([
                    'message' => 'There is already same participation.'
                ], 409);
            }

        } else {
            return response()->json([
                'message' => 'Contest not found.'
            ], 404);
        }
    }

    public function newParticip(Request $request)
    {
        $validatedData = $request->validate([
            'code' => 'required|numeric|min:0|max:9999999|exists:contests,contest_code',
            'contest_level' => 'required|numeric|exists:levels,id',
            'contest_place' => 'required|numeric|exists:places,id'
        ]);

        $contest = Contest::where('contest_code', $validatedData['code'])->first();
        $userId = auth()->id();

        if ($contest) {
            $level = Level::findOrFail($validatedData['contest_level']);
            $place = Place::findOrFail($validatedData['contest_place']);

            if ($level->contest_id == $contest->id && $place->contest_id == $contest->id) {
                $regNumber = ContestMember::generateUniqueRegNumber();

                ContestMember::create([
                    'user_id' => auth()->id(),
                    'contest_id' => $contest->id,
                    'level_id' => $level->id,
                    'place_id' => $place->id,
                    'reg_number' => $regNumber
                ]);

                return response()->json([
                    'redirect' => route('contestCheck.show', ['contest_id' => $contest->id])
                ]);
            }

            return response()->json([
                'message' => 'Place/level not found.'
            ], 404);

        } else {
            return response()->json([
                'message' => 'Contest not found.'
            ], 404);
        }
    }

    public function newContest(Request $request)
    {
        $validatedData = $request->validate([
            'contest_title' => 'required|string|min:1|max:255'
        ]);

        if (!Contest::where('title', $validatedData['contest_title'])->exists()) {
            $contestCode = Contest::generateUniqueContestCode();

            $contest = Contest::create([
                'creator_id' => auth()->id(),
                'title' => $validatedData['contest_title'],
                'contest_code' => $contestCode
            ]);

            return response()->json([
                'redirect' => route('contests.show', ['contest_id' => $contest->id])
            ]);
        } else {
            return response()->json([
                'message' => 'There is a contest with the same title.'
            ], 409);
        }
    }

    public function showContest($contest_id)
    {
        $contest = Contest::findOrFail($contest_id);

        $title = $contest->title;
        $contest_code = $contest->contest_code;

        if (!$contest) {
            abort(404);
        }

        $places = Place::where('contest_id', $contest_id)->select('id', 'title', 'locality', 'address')->get();
        $levels = Level::where('contest_id', $contest_id)->get();
        $experts = Expert::where('contest_id', $contest_id)->get();


        $experts = DB::table('experts')
            ->join('levels', 'experts.level_id', '=', 'levels.id')
            ->select('name', 'email', 'title', )
            ->where('experts.contest_id', $contest_id)
            ->get();
            

        return view('creator.contest', [
            "title" => $title,
            "contest_code" => $contest_code,
            "contest_id" => $contest_id,
            "places" => $places,
            "levels" => $levels,
            "experts" => $experts
        ]);
    }

    public function showContestCheck($contest_id)
    {
        $contest = Contest::findOrFail($contest_id);
        $contestMember = ContestMember::where('contest_id', $contest_id)->first();

        if ($contestMember) {
            $level = Level::findOrFail($contestMember->level_id);
            $place = Place::findOrFail($contestMember->place_id);

            return view('member.contest', [
                "title" => $contest->title,
                "contest_code" => $contest->contest_code,
                "level" => $level->title,
                "address" => $place->address,
                "place" => $place->title,
                "locality" => $place->locality,
                "reg_number" => $contestMember->reg_number
            ]);
        } else {
            abort(404);
        }
    }

    public function showPlace($contest_id, $place_id)
    {
        $place = Place::findOrFail($place_id);
        $contest = Contest::findOrFail($contest_id);

        if ($place->contest_id == $contest_id) {
            $title = $place->title;
            $locality = $place->locality;
            $address = $place->address;

            $auditoriums = Auditorium::where('contest_id', $contest_id)->where('place_id', $place_id)->get();

            return view('creator.place', [
                "title" => $title,
                "locality" => $locality,
                "address" => $address,
                "contest_id" => $contest_id,
                "contest_title" => $contest->title,
                "place_id" => $place_id,
                "auditoriums" => $auditoriums
            ]);
        } else {
            abort(404);
        }
    }

    public function showLevel($contest_id, $level_id)
    {
        $level = Level::findOrFail($level_id);
        $contest = Contest::findOrFail($contest_id);

        if ($level->contest_id == $contest_id){
            $title = $level->title;
            $pattern = $level->pattern;

            $tasks = Task::where('contest_id', $contest_id)->where('level_id', $level_id);

            return view('creator.level', [
                "title" => $title,
                "contest_id" => $contest_id,
                "level_id" => $level_id,
                "pattern" => $pattern,
                "contest_title" => $contest->title,
                "tasks" => $tasks->get(),
                "tasks_count" => $tasks->count()
            ]);
        } else {
            abort(404);
        };
    }

    public function showTask($contest_id, $level_id, $task_id)
    {
        $task = Task::findOrFail($task_id);
        $contest = Contest::findOrFail($contest_id);
        $level = Level::findOrFail($level_id);

        if ($task->contest_id == $contest_id && $task->level_id == $level_id){
            $task_number = $task->number;
            $task_max_rate = $task->max_rate;

            return view('creator.task', [
                "task_number" => $task_number,
                "task_max_rate" => $task_max_rate,
                "contest_id" => $contest_id,
                "level_id" => $level_id,
                "task_number" => $task->number,
                "contest_title" => $contest->title,
                "level_title" => $level->title
            ]);
        } else {
            abort(404);
        };
    }

    public function showNotification($contest_id)
    {
        $contest = Contest::findOrFail($contest_id);
        $contestMember = ContestMember::where('contest_id', $contest_id)->first();

        $data = [
            'title' => $contest->title,
            'reg_number' => $contestMember->reg_number,
            'inter' => storage_path('fonts/inter.ttf')
        ];

        $pdf = PDF::loadView('pdf.sample', $data);
        $pdf->setPaper('A4', 'portrait');


        return $pdf->stream('document.pdf', [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="document.pdf"',
        ]);
    }

    public function newPlace(Request $request)
    {
        $validatedData = $request->validate([
            'contest_id' => 'required|integer|exists:contests,id',
            'place_title' => 'required|string|max:255',
            'place_locality' => 'required|string|max:255',
            'place_address' => 'nullable|string|max:255'
        ]);

        $contest = Contest::findOrFail($validatedData['contest_id']);
        $creatorId = $contest->creator_id;

        if ($creatorId == auth()->id()) {
            if (!Place::where('title', $validatedData['place_title'])->exists()) {
                $place = Place::create([
                    'title' => $validatedData['place_title'],
                    'locality' => $validatedData['place_locality'],
                    'address' => $validatedData['place_address'],
                    'contest_id' => $validatedData['contest_id']
                ]);

                return response()->json([
                    'redirect' => route('places.show', [
                        'contest_id' => $contest->id,
                        'place_id' => $place->id
                    ])
                ]);
            } else {
                return response()->json([
                    'message' => 'There is a place with the same title.'
                ], 409);
            }

        } else {
            return response()->json([
                'message' => 'There is no provided contest id.'
            ], 500);
        }
    }

    public function newLevel(Request $request)
    {
        $validatedData = $request->validate([
            'contest_id' => 'required|integer|exists:contests,id',
            'level_title' => 'required|string|max:255'
        ]);

        $contest = Contest::findOrFail($validatedData['contest_id']);
        $creatorId = $contest->creator_id;

        if ($creatorId == auth()->id()) {
            if (!Level::where('title', $validatedData['level_title'])->where('contest_id', )->exists()) {
                $level = Level::create([
                    'title' => $validatedData['level_title'],
                    'contest_id' => $validatedData['contest_id']
                ]);

                return response()->json([
                    'redirect' => route('level.show', [
                        'contest_id' => $contest->id,
                        'level_id' => $level->id
                    ])
                ]);
            } else {
                return response()->json([
                    'message' => 'There is a level with the same title.'
                ], 409);
            }

        } else {
            return response()->json([
                'message' => 'There is no provided contest id.'
            ], 500);
        }
    }

    public function newAuditorium(Request $request)
    {
        $validatedData = $request->validate([
            'contest_id' => 'required|integer|exists:contests,id',
            'place_id' => 'required|integer|exists:places,id',
            'auditorium_title' => 'required|string|max:255',
            'auditorium_rows' => 'required|numeric|min:1',
            'auditorium_columns' => 'required|numeric|min:1'
        ]);

        $place_contestId = Place::findOrFail($validatedData['place_id'])->contest_id;

        if ($place_contestId == $validatedData['contest_id']) {
            $contest = Contest::findOrFail($validatedData['contest_id']);

            if ($contest->creator_id == auth()->id()) {
                if (!Auditorium::where('title', $validatedData['auditorium_title'])->exists()) {
                    $level = Auditorium::create([
                        'title' => $validatedData['auditorium_title'],
                        'contest_id' => $validatedData['contest_id'],
                        'place_id' => $validatedData['place_id'],
                        'rows' => $validatedData['auditorium_rows'],
                        'columns' => $validatedData['auditorium_columns'],
                    ]);

                    return response()->json([
                        'reload' => true
                    ]);
                }

                return response()->json([
                    'message' => 'There is an auditorium with the same title.'
                ], 409);
            } else {
                return response()->json([
                    'message' => 'There is no provided contest id.'
                ], 401);
            }

        } else {
            return response()->json([
                'message' => 'There is no provided place id.'
            ], 500);
        }


    }

    public function setPattern(Request $request)
    {
        $validatedData = $request->validate([
            'contest_id' => 'required|integer|exists:contests,id',
            'level_id' => 'required|integer|exists:levels,id',
            'level_pattern' => 'required|string'
        ]);

        $level = Level::findOrFail($validatedData['level_id']);

        if ($level->contest_id == $validatedData['contest_id']) {
            $contest = Contest::findOrFail($validatedData['contest_id']);

            if ($contest->creator_id == auth()->id()) {
                $level->pattern = $validatedData['level_pattern'];
                $level->save();

            } else {
                return response()->json([
                    'message' => 'There is no provided contest id.'
                ], 401);
            }

        } else {
            return response()->json([
                'message' => 'There is no provided place id.'
            ], 500);
        }
    }

    public function newTask(Request $request)
    {
        $validatedData = $request->validate([
            'contest_id' => 'required|integer|exists:contests,id',
            'level_id' => 'required|integer|exists:levels,id',
            'task_max_rate' => 'required|numeric|min:1'
        ]);

        $level = Level::findOrFail($validatedData['level_id']);

        if ($level->contest_id == $validatedData['contest_id']) {
            $contest = Contest::findOrFail($validatedData['contest_id']);

            if ($contest->creator_id == auth()->id()) {
                $tasks = Task::where('contest_id', $level->contest_id)->where('level_id', $level->id);

                $task = Task::create([
                    'number' => $tasks->count() + 1,
                    'max_rate' => $validatedData['task_max_rate'],
                    'level_id' => $validatedData['level_id'],
                    'contest_id' => $validatedData['contest_id']
                ]);

                return response()->json([
                    'redirect' => route('task.show', [
                        "contest_id" => $task->contest_id,
                        "level_id" => $task->level_id,
                        "task_id" => $task->id
                    ])
                ]);

            } else {
                return response()->json([
                    'message' => 'There is no provided contest id.'
                ], 401);
            }

        } else {
            return response()->json([
                'message' => 'There is no provided place id.'
            ], 500);
        }


    }

    public function newExpert(Request $request)
    {
        $validatedData = $request->validate([
            'contest_id' => 'required|integer|exists:contests,id',
            'expert_name' => 'required|string',
            'expert_email' => 'required|email|max:255',
            'expert_level' => 'required|integer|exists:levels,id'
        ]);

        $contest = Contest::findOrFail($validatedData['contest_id']);
        $creatorId = $contest->creator_id;

        if ($creatorId == auth()->id()) {
            if (!Expert::where('email', $validatedData['expert_email'])
            ->where('contest_id', $validatedData['contest_id'])
            ->where('level_id', $validatedData['expert_level'])->exists()) {
                $expert = Expert::create([
                    'name' => $validatedData['expert_name'],
                    'email' => $validatedData['expert_email'],
                    'contest_id' => $validatedData['contest_id'],
                    'level_id' => $validatedData['expert_level']
                ]);

                $level = Level::findOrFail($validatedData['expert_level']);

                return response()->json([
                    'name' => $validatedData['expert_name'],
                    'email' => $validatedData['expert_email'],
                    'contest_id' => $validatedData['contest_id'],
                    'level' => $level->title
                ], 200);

            } else {
                return response()->json([
                    'message' => 'There is an expert with same data.'
                ], 409);
            }

        } else {
            return response()->json([
                'message' => 'There is no provided contest id.'
            ], 500);
        }
    }


}
