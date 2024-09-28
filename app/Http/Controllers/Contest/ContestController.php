<?php

namespace App\Http\Controllers\Contest;

use App\Http\Controllers\Controller;

use Barryvdh\DomPDF\Facade\Pdf as PDF;

use App\Models\Contest;
use App\Models\Place;
use App\Models\Level;
use App\Models\Task;
use App\Models\User;
use App\Models\Expert;
use App\Models\School;
use App\Models\Appeal;
use App\Models\Auditorium;
use App\Models\ContestOption;
use App\Models\ContestMember;
use App\Models\TaskPrototype;

use App\Http\Controllers\Contest\GenerateFiles;
use App\Http\Controllers\Contest\GetSeatsCounts;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

use ZipArchive;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;


class ContestController extends Controller
{
    public function getCities(Request $request) {
        $region = $request->input("region");

        $cities = School::where('region_id', $region)
                ->select('city_id', 'city')
                ->distinct()
                ->get();

        return response()->json([
            'cities' => [
                $cities
            ]
        ]);
    }

    public function getAppeals(Request $request) {
        $contest_id = $request->input("contest_id");
        $level_id = $request->input("level_id");
        $place_id = $request->input("place_id");
        $considered = $request->input("considered");

        $contest = Contest::findOrFail($contest_id);

        if ($contest->creator_id == Auth::user()->id) {
            $appeals = Appeal::join('contest_members', 'contest_members.id', '=', 'appeals.contest_member_id')
                ->join('users', 'users.id', '=', 'contest_members.user_id')
                ->join('levels', 'levels.id', '=', 'contest_members.level_id')
                ->join('places', 'places.id', '=', 'contest_members.place_id')
                ->where('contest_members.contest_id', $contest_id)
                ->select('appeals.id', 'users.first_name', 'users.last_name', 'users.middle_name', 'levels.title as level', 'places.title as place');

            if ($level_id != 0)
                $appeals = $appeals->where('level_id', $level_id);

            if ($place_id != 0)
                $appeals = $appeals->where('place_id', $place_id);

            if ($considered == 1)
                $appeals = $appeals->where('changed', 1);

            else if ($considered == 2)
                $appeals = $appeals->where('changed', 0);

            return response()->json([
                'appeals' => [
                    $appeals->get()
                ]
            ]);
        }

        return response()->json([
            'message' => [
                'There were not found this contest on this account.'
            ]
        ]);
    }

    public function getResults(Request $request) {
        $contest_id = $request->input("contest_id");
        $level_id = $request->input("level_id");
        $place_id = $request->input("place_id");

        $contest = Contest::findOrFail($contest_id);

        if ($contest->creator_id == Auth::user()->id) {
            $contestMembers = ContestMember::where("contest_members.contest_id", $contest->id)
                ->join('users', 'users.id', '=', 'contest_members.user_id')
                ->join('levels', 'levels.id', '=', 'contest_members.level_id')
                ->leftJoin('schools', 'contest_members.school_id', '=', 'schools.s_id')
                ->select('contest_members.id', 'last_name', 'school_name', 'short_title', 'first_name', 'middle_name', 'levels.title')
                ->withSum('grades', 'final_score')
                ->orderBy('grades_sum_final_score', 'desc');

            if ($level_id != 0)
                $contestMembers = $contestMembers->where('contest_members.level_id', $level_id);

            if ($place_id != 0)
                $contestMembers = $contestMembers->where('contest_members.place_id', $place_id);

            return response()->json([
                'results' => [
                    $contestMembers->get()
                ]
            ]);
        }

        return response()->json([
            'message' => [
                'There were not found this contest on this account.'
            ]
        ]);
    }

    public function getSchools(Request $request) {
        $city_id = $request->input("city_id");

        $cities = School::where('city_id', $city_id)
                ->select('short_title', 's_id')
                ->distinct()
                ->get();

        return response()->json([
            'schools' => [
                $cities
            ]
        ]);
    }

    public function getContest(Request $request)
    {
        $validatedData = $request->validate([
            'contest_code' => 'required|numeric|min:0|max:9999999|exists:contests,contest_code'
        ]);

        $contest = Contest::where('contest_code', $validatedData['contest_code']);
        $userId = auth()->id();

        if ($contest->exists()) {
            $contest = $contest->first();
            if ($contest->application_type == 1) {
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
                    'message' => 'Application is closed.'
                ], 403);
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
            'contest_place' => 'required|numeric|exists:places,id',
            'school_id' => 'numeric|exists:schools,s_id',
            'school_name' => 'string'
        ]);

        $contest = Contest::where('contest_code', $validatedData['code']);

        if ($contest->exists()) {
            $contest = $contest->first();
            if ($contest->application_type == 1) {
                $level = Level::findOrFail($validatedData['contest_level']);
                $place = Place::findOrFail($validatedData['contest_place']);

                if ($level->contest_id == $contest->id && $place->contest_id == $contest->id) {
                    if ($contest->getFreePlacesCount($place->id, $level->id)) {
                        $regNumber = ContestMember::generateUniqueRegNumber();

                        $data = [
                            'user_id' => auth()->id(),
                            'contest_id' => $contest->id,
                            'level_id' => $level->id,
                            'place_id' => $place->id,
                            'reg_number' => $regNumber,
                            'expert_id' => 0
                        ];

                        if (isset($validatedData["school_name"]))
                            $data["school_name"] = $validatedData["school_name"];

                        if (isset($validatedData["school_id"]))
                            $data["school_id"] = $validatedData["school_id"];

                        ContestMember::create($data);

                        return response()->json([
                            'redirect' => route('contestMemberCheck.show', ['contest_id' => $contest->id])
                        ]);
                    } else {
                        return response()->json([
                            'message' => 'There are no free places.'
                        ], 403);
                    }
                }

                return response()->json([
                    'message' => 'Place/level not found.'
                ], 404);
            } else {
                return response()->json([
                    'message' => 'Application is closed.'
                ], 403);
            }

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
                'contest_code' => $contestCode,
                'application_type' => 0
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

        $places = Place::where('contest_id', $contest_id)->select('id', 'title', 'locality', 'address');
        $levels = Level::where('contest_id', $contest_id);

        $appeals = Appeal::join('contest_members', 'contest_members.id', '=', 'appeals.contest_member_id')
            ->join('users', 'users.id', '=', 'contest_members.user_id')
            ->join('levels', 'levels.id', '=', 'contest_members.level_id')
            ->join('places', 'places.id', '=', 'contest_members.place_id')
            ->where('contest_members.contest_id', $contest_id)
//            ->where('place_id', $firstPlace->id)
//            ->where('level_id', $firstLevel->id)
            ->select('appeals.id', 'users.first_name', 'users.last_name', 'users.middle_name', 'levels.title as level', 'places.title as place', 'places.contest_id')
            ->get();

        $experts = DB::table('experts')
            ->join('levels', 'experts.level_id', '=', 'levels.id')
            ->select('name', 'email', 'title')
            ->where('experts.contest_id', $contest_id)
            ->get();

        $checkedCount = ContestMember::has("grades")->where("contest_id", $contest_id)->count();
        $notCheckedCount = ContestMember::doesntHave("grades")->where("contest_id", $contest_id)->count();

        return view('creator.contest', [
            "title" => $title,
            "contest_code" => $contest_code,
            "contest_id" => $contest_id,
            "places" => $places->get(),
            "levels" => $levels->get(),
            "experts" => $experts,
            "at" => $contest->application_type,
            "published" => $contest->publish,
            "options_status" => $contest->options_status,
            "auditoriums_status" => $contest->auditoriums_status,
            "protocols_status" => $contest->protocols_status,
            "checked_count" => $checkedCount,
            "not_checked_count" => $notCheckedCount,
            "appeals" => $appeals
        ]);
    }

    public function showRating($contest_id) {
        $contest = Contest::findOrFail($contest_id);
        $levels = Level::where("contest_id", $contest_id);
        $places = Place::where("contest_id", $contest_id);

        $contestMembers = ContestMember::where("contest_members.contest_id", $contest->id)
            //->where("contest_members.level_id", $level->id)
        ;

        $contestMembers = $contestMembers
            ->join('users', 'users.id', '=', 'contest_members.user_id')
            ->join('levels', 'levels.id', '=', 'contest_members.level_id')
            ->leftJoin('schools', 'contest_members.school_id', '=', 'schools.s_id')
            ->select('contest_members.id', 'last_name', 'school_name', 'short_title', 'first_name', 'middle_name', 'levels.title')
            ->withSum('grades', 'final_score')
            ->orderBy('grades_sum_final_score', 'desc');

        if ($levels->exists() and $places->exists()) {
            return view('creator.rating', [
                "contest_id" => $contest_id,
                "contest_title" => $contest->title,
                "levels" => $levels->get(),
                "places" => $places->get(),
                "contest_members" => $contestMembers->get(),
                "contest_members_count" => $contestMembers->count()
            ]);
        }
    }

    public function showAppeal($contest_id, $appeal_id) {
        $appeal = Appeal::findOrFail($appeal_id);
        $contest = Contest::findOrFail($contest_id);
        $contestMember = ContestMember::findOrFail($appeal->contest_member_id);
        $user = User::findOrFail($contestMember->user_id);

        $optionId = $contestMember->option_id;
        $option = ContestOption::findOrFail($optionId);

        return view('creator.appeal', [
            "contest_id" => $contest_id,
            "appeal_id" => $appeal_id,
            "contest_title" => $contest->title,
            "appeal_title" => "{$user->last_name} {$user->first_name} {$user->middle_name}",
            "email" => $appeal->email,
            "phone" => $appeal->phone,
            "text" => $appeal->text,
            "scans" => $contestMember->scans()->get(),
            "tasks" => $option->tasks()
                ->join('tasks', 'task_prototypes.task_id', '=', 'tasks.id')
                ->select('tasks.id', 'number', 'task_text', 'task_answer', 'max_rate')
                ->orderBy('number')
                ->get(),
            "c_member" => $contestMember
        ]);
    }

    public function showContestMemberCheck($contest_id)
    {
        $contest = Contest::findOrFail($contest_id);
        $contestMember = ContestMember::where('contest_id', $contest_id)->where('user_id', auth()->id());

        if ($contestMember->exists()) {
            $contestMember = $contestMember->first();
            $level = Level::findOrFail($contestMember->level_id);
            $place = Place::findOrFail($contestMember->place_id);

            $auditorium = Auditorium::find($contestMember->auditorium_id);
            $option = ContestOption::find($contestMember->option_id);

            $appeal = $contestMember->appeal();

            return view('member.contest', [
                "title" => $contest->title,
                "contest_code" => $contest->contest_code,
                "level" => $level->title,
                "address" => $place->address,
                "place" => $place->title,
                "locality" => $place->locality,
                "scans" => $contestMember->scans()->get(),
                "reg_number" => strval($contestMember->reg_number),
                "auditorium" => $auditorium ? $auditorium->title : null,
                "option" => $option ? $option->variant_number : null,
                "seat" => $contestMember->seat,
                "c_member_id" => $contestMember->id,
                "end_time" => $contestMember->end_time,
                "absence" => $contestMember->absence,
                "blanks" => $contestMember->blanks,
                "tasks" => $contestMember->tasks,
                "grades" => $contestMember->grades()->join('tasks', 'tasks.id', '=', 'grades.task_id')->select("number", "final_score", "max_rate")->orderBy("number")->get(),
                "not_finished" => $contestMember->not_finished,
                "at" => $contestMember->application_type,
                "publish" => $contest->publish,
                "appeal" => $appeal->exists() ? $appeal->first() : null,
                "appeal_allowed" => $level->appeal
            ]);
        } else {
            abort(404);
        }
    }

    public function showPlace($contest_id, $place_id)
    {
        $place = Place::findOrFail($place_id);
        $contest = Contest::findOrFail($contest_id);
        $levels = Level::where('contest_id', $contest_id)->select('id', 'title')->get();

        if ($place->contest_id == $contest_id) {
            $title = $place->title;
            $locality = $place->locality;
            $address = $place->address;

            $auditoriums = Auditorium::where('contest_id', $contest_id)->where('place_id', $place_id)->get();

            $members = DB::table('contest_members')
                ->join('places', 'contest_members.place_id', '=', 'places.id')
                ->join('users', 'contest_members.user_id', '=', 'users.id')
                ->where('contest_members.contest_id', $contest_id)
                ->where('contest_members.place_id', $place_id)
                ->select('first_name', 'last_name', 'middle_name', 'email')
                ->get();

            return view('creator.place', [
                "title" => $title,
                "locality" => $locality,
                "address" => $address,
                "contest_id" => $contest_id,
                "contest_title" => $contest->title,
                "place_id" => $place_id,
                "auditoriums" => $auditoriums,
                "members" => $members,
                "levels" => $levels,
                "places_count" => $place->getPlacesCount(),
                "at" => $contest->application_type
            ]);
        } else {
            abort(404);
        }
    }

    public function showAuditorium($contest_id, $place_id, $auditorium_id)
    {
        $place = Place::findOrFail($place_id);
        $contest = Contest::findOrFail($contest_id);
        $auditorium = Auditorium::findOrFail($auditorium_id);

        $members = DB::table('contest_members')
            ->join('places', 'contest_members.place_id', '=', 'places.id')
            ->join('users', 'contest_members.user_id', '=', 'users.id')
            ->where('contest_members.contest_id', $contest_id)
            ->where('contest_members.place_id', $place_id)
            ->where('contest_members.auditorium_id', $auditorium_id)
            ->select('first_name', 'last_name', 'middle_name', 'email', 'contest_members.seat')
            ->orderBy('seat')
            ->get();

        if ($auditorium->contest_id == $contest_id && $auditorium->place_id == $place_id) {
            return view('creator.auditorium', [
                "title" => $auditorium->title,
                "contest_id" => $contest_id,
                "place_id" => $place_id,
                "contest_title" => $contest->title,
                "place_title" => $place->title,
                "members" => $members,
                "auditorium_id" => $auditorium->id,
                "showProtocolDownloadButton" => boolval(File::exists(storage_path("app/private/protocols/{$auditorium->id}.pdf"))),
                "showPapersDownloadButton" => boolval(File::exists(storage_path("app/private/papers/pdfs/{$auditorium->id}.pdf"))),
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
                "tasks_count" => $tasks->count(),
                "appeal" => $level->appeal
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

        $taskPrototypes = TaskPrototype::where('contest_id', $contest_id)
            ->where('level_id', $level_id)
            ->where('task_id', $task_id)
            ->select('id', 'prototype_number')
            ->get();

        if ($task->contest_id == $contest_id && $task->level_id == $level_id){
            $task_number = $task->number;
            $task_max_rate = $task->max_rate;

            return view('creator.task', [
                "task_number" => $task_number,
                "task_max_rate" => $task_max_rate,
                "contest_id" => $contest_id,
                "level_id" => $level_id,
                "task_id" => $task_id,
                "contest_title" => $contest->title,
                "level_title" => $level->title,
                "task_prototypes" => $taskPrototypes
            ]);
        } else {
            abort(404);
        };
    }

    public function getPrototypeData(Request $request) {
        $task_id = $request->input("task_id");
        $contest_id = $request->input("contest_id");
        $level_id = $request->input("level_id");
        $prototype_id = $request->input("tp_id");

        $taskPrototype = TaskPrototype::where('contest_id', $contest_id)
            ->where('level_id', $level_id)
            ->where('task_id', $task_id)
            ->where('id', $prototype_id)
            ->select('id', 'task_text', 'task_answer', 'prototype_number')
            ->first();

        return response()->json([
            'task_prototype' => [
                $taskPrototype
            ]
        ]);
    }

    public function showNotification($contest_id)
    {
        $user = auth()->user();
        $contest = Contest::findOrFail($contest_id);
        $contestCreator = User::findOrFail($contest->creator_id);
        $contestMember = ContestMember::where('contest_id', $contest_id)->where('user_id', auth()->id())->first();
        $level = Level::findOrFail($contestMember->level_id);
        $place = Place::findOrFail($contestMember->place_id);

        if ($contestMember->school_id) {
            $school = School::where("s_id", $contestMember->school_id)->select('short_title');
            if ($school->exists()) {
                $school = $school->first()->short_title;
            } else {
                $school = null;
            }
        } else {
            $school = null;
        }


        $data = [
            'title' => $contest->title,
            'first_name' => $user->first_name,
            'middle_name' => $user->middle_name,
            'last_name' => $user->last_name,
            'reg_number' => $contestMember->reg_number,
            'level_title' => $level->title,
            'school_title' => $school,
            'place_title' => $place->title,
            'locality' => $place->locality,
            'address' => $place->address,
            'contest_title' => $contest->title,
            'org_email' => $contestCreator->email,
            'inter' => storage_path('fonts/inter.ttf'),
        ];

        $pdf = PDF::loadView('pdf.notification', $data);
        $pdf->setPaper('A4', 'portrait');


        return $pdf->stream("Уведомление_{$contestMember->reg_number}.pdf", [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="document.pdf"',
        ]);
    }

    public function showOption($contest_id) {
        $user_id = auth()->id();
        $user = auth()->user();

        $contestMember = ContestMember::where('contest_id', $contest_id)->where('user_id', $user_id)->first();


        return $pdf->stream("Вариант_{$contestMember->reg_number}.pdf", [
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
                $ppiNumber = Place::generateUniquePPINumber();

                $place = Place::create([
                    'title' => $validatedData['place_title'],
                    'locality' => $validatedData['place_locality'],
                    'address' => $validatedData['place_address'],
                    'contest_id' => $validatedData['contest_id'],
                    'ppi_number' => $ppiNumber
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
            if (!Level::where('title', $validatedData['level_title'])->where('contest_id')->exists()) {
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
            'auditorium_columns' => 'required|numeric|min:1',
            'level' => 'integer'
        ]);

        $place_contestId = Place::findOrFail($validatedData['place_id'])->contest_id;

        if ($place_contestId == $validatedData['contest_id']) {
            $contest = Contest::findOrFail($validatedData['contest_id']);

            if ($contest->creator_id == auth()->id()) {
                if (!Auditorium::where('title', $validatedData['auditorium_title'])->where('place_id', $validatedData['place_id'])->exists()) {
                    $a = Auditorium::create([
                        'title' => $validatedData['auditorium_title'],
                        'contest_id' => $validatedData['contest_id'],
                        'place_id' => $validatedData['place_id'],
                        'level_id' => $validatedData['level'],
                        'rows' => $validatedData['auditorium_rows'],
                        'columns' => $validatedData['auditorium_columns']
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

    public function newPrototype(Request $request)
    {
        $validatedData = $request->validate([
            'contest_id' => 'required|integer|exists:contests,id',
            'level_id' => 'required|integer|exists:levels,id',
            'task_id' => 'required|integer|exists:tasks,id'
        ]);

        $contest = Contest::findOrFail($validatedData['contest_id']);
        $level = Level::findOrFail($validatedData['level_id']);
        $task = Task::findOrFail($validatedData['task_id']);

        $creatorId = $contest->creator_id;

        if ($creatorId == auth()->id()) {
            if ($task->level_id == $level->id && $task->contest_id == $contest->id) {
                $prototypeNumber = TaskPrototype::generateUniquePrototypeNumber();

                $taskPrototype = TaskPrototype::create([
                    'prototype_number' => $prototypeNumber,
                    'task_id' => $task->id,
                    'level_id' => $level->id,
                    'contest_id' => $contest->id
                ]);

                return response()->json([
                    'prototype_number' => $prototypeNumber,
                    'tp_id' => $taskPrototype->id
                ], 200);

            } else {
                return response()->json([
                    'message' => 'There is no provided task id.'
                ], 409);
            }

        } else {
            return response()->json([
                'message' => 'There is no provided contest id.'
            ], 500);
        }
    }

    public function setTaskPrototypeData(Request $request)
    {
        $validatedData = $request->validate([
            'contest_id' => 'required|integer|exists:contests,id',
            'level_id' => 'required|integer|exists:levels,id',
            'task_id' => 'required|integer|exists:tasks,id',
            'tp_id' => 'required|integer|exists:task_prototypes,id',
            'task_text' => 'string|nullable',
            'task_answer' => 'string|nullable'
        ]);

        $contest = Contest::findOrFail($validatedData['contest_id']);
        $level = Level::findOrFail($validatedData['level_id']);
        $task = Task::findOrFail($validatedData['task_id']);
        $taskPrototype = TaskPrototype::findOrFail($validatedData['tp_id']);

        if ($taskPrototype->level_id == $level->id && $taskPrototype->contest_id == $contest->id) {
            if ($contest->creator_id == auth()->id()) {
                $taskPrototype->task_text = $validatedData['task_text'];
                $taskPrototype->task_answer = $validatedData['task_answer'];
                $taskPrototype->save();

                return response()->json([
                    'tp_id' => $taskPrototype->id,
                    'task_text' => $validatedData['task_text'],
                    'task_answer' => $validatedData['task_answer'],
                ], 200);

            } else {
                return response()->json([
                    'message' => 'There is no provided task id.'
                ], 401);
            }

        } else {
            return response()->json([
                'message' => 'There is no provided place id.'
            ], 500);
        }
    }

    public function publishResults(Request $request) {
        $validatedData = $request->validate([
            'contest_id' => 'required|integer|exists:contests,id'
        ]);

        $contest = Contest::findOrFail($validatedData['contest_id']);

        if ($contest->creator_id == auth()->id() && $contest->getPlacesCount()) {
            $contest->publish = true;
            $contest->save();

        } else {
            abort(404);
        }
    }

    public function startApply(Request $request)
    {
        $validatedData = $request->validate([
            'contest_id' => 'required|integer|exists:contests,id'
        ]);

        $contest = Contest::findOrFail($validatedData['contest_id']);
        $levels = Level::where('contest_id', $contest->id)->get();
        $places = Place::where('contest_id', $contest->id)->get();

        if ($contest->creator_id == auth()->id() && $contest->getPlacesCount()) {
            if (!$levels->isEmpty() && !$places->isEmpty()) {
                $contest->application_type = 1;
                $contest->save();
            } else {
                return response()->json([
                    'message' => 'Cannot start applications.',
                ], 403);
            }
        } else {
            abort(404);
        }
    }

    public function stopApply(Request $request)
    {
        $validatedData = $request->validate([
            'contest_id' => 'required|integer|exists:contests,id'
        ]);

        $contest = Contest::findOrFail($validatedData['contest_id']);
        $at = $contest->application_type;

        if ($contest->creator_id == auth()->id()) {
            $contest->application_type = 2;
            $contest->save();

        } else {
            abort(404);
        }
    }

    public function stopAppeals(Request $request)
    {
        $validatedData = $request->validate([
            'level_id' => 'required|integer|exists:contests,id'
        ]);

        $levelAffliance = Level::where("levels.id", $validatedData['level_id'])
            ->join('contests', 'levels.contest_id', '=', 'contests.id')
            ->where("creator_id", auth()->id());

        if ($levelAffliance->exists()) {
            $levelAffliance = $levelAffliance->first();
            $levelAffliance->appeal = 0;

            $levelAffliance->save();
        }
    }

    public function startAppeals(Request $request)
    {
        $validatedData = $request->validate([
            'level_id' => 'required|integer|exists:contests,id'
        ]);

        $levelAffliance = Level::where("levels.id", $validatedData['level_id'])
            ->join('contests', 'levels.contest_id', '=', 'contests.id')
            ->where("creator_id", auth()->id());

        if ($levelAffliance->exists()) {
            $levelAffliance = $levelAffliance->first();
            $levelAffliance->appeal = 1;

            $levelAffliance->save();
        }
    }

    public function setSeatsToContestMembersOnPlace($place_id) {
        $place = Place::findOrFail($place_id);

        $auditoriums = [];
        $auditoriums_raw = Auditorium::where("place_id", $place_id)->select('id', 'title', 'rows', 'level_id', 'columns')->get()->toArray();

        foreach ($auditoriums_raw as $auditorium) {
            $id = $auditorium["id"];
            $level_id = $auditorium["level_id"];
            $rows = $auditorium["rows"];
            $columns = $auditorium["columns"];

            $auditoriums[$id] = [$level_id, $rows * $columns];

        }

        $members = [];
        $members_raw = ContestMember::where("place_id", $place_id)->select('id', 'school_id', 'level_id')->get()->toArray();

        foreach ($members_raw as $member) {
            $id = $member["id"];
            $level_id = $member["level_id"];
            $school_id = $member["school_id"];

            $members[$id] = [$school_id, $level_id];
        }

        $service = new GetSeatsCounts();
        return $service->execute($auditoriums, $members);
    }

    public function setSeatsToContestMembers($contest) {
        $service = new GetSeatsCounts();
        $places = Place::where('contest_id', $contest->id)->get();

        foreach ($places as $place) {
            $place_id = $place->id;
            $seatsCounts = $this->setSeatsToContestMembersOnPlace($place_id);

            foreach ($seatsCounts as $level => $auditoriums) {
                foreach ($auditoriums as $auditorium_id => $schools) {
                    $auditorium = Auditorium::findOrFail($auditorium_id);
                    $rows = $auditorium->rows;
                    $columns = $auditorium->columns;

                    $membersBySchools = [];

                    foreach ($schools as $school_id => $count) {
                        $schoolId = $school_id ? $school_id : null;
                        $members = ContestMember::where("place_id", $place_id)->where("school_id", $schoolId)->where("seat", null)->select("id")->take($count)->get()->toArray();


                        $membersBySchools[$schoolId ? $schoolId : ""] = $members;
                    }


                    $service->setMembersToPlaces($schools, $columns, $rows, $membersBySchools, $auditorium_id);
                }
            }
        }
    }

    public function endApply(Request $request)
    {
        $validatedData = $request->validate([
            'contest_id' => 'required|integer|exists:contests,id'
        ]);

        $contest = Contest::findOrFail($validatedData['contest_id']);
        $at = $contest->application_type;

        if ($contest->creator_id == auth()->id()) {
            $contest->application_type = 3;
            $contest->save();

            return $this->setSeatsToContestMembers($contest);
        } else {
            abort(404);
        }
    }

    public function endTour(Request $request)
    {
        $validatedData = $request->validate([
            'contest_id' => 'required|integer|exists:contests,id'
        ]);

        $contest = Contest::findOrFail($validatedData['contest_id']);
        $at = $contest->application_type;

        if ($contest->creator_id == auth()->id()) {
            $contest->application_type = 4;

            $service = new GenerateFiles();
            $service->generateAllPPIFiles($contest);

            $contest->save();

        } else {
            abort(404);
        }
    }

    public function startChecking(Request $request) {
        $validatedData = $request->validate([
            'contest_id' => 'required|integer|exists:contests,id'
        ]);

        $contest = Contest::findOrFail($validatedData['contest_id']);
        $at = $contest->application_type;

        if ($contest->creator_id == auth()->id()) {
            $contest->application_type = 5;

            $contest->save();

        } else {
            abort(404);
        }
    }


    public function createArchivePassport($exist, $doesnt_exist, $contest_id, $place_id) {
        $passport_title = "Паспорт_архива_{$contest_id}_{$place_id}.txt";
        $e_count = count($exist);
        $de_count = count($doesnt_exist);

        $ne_files_text = $de_count ? "Нехвающих файлов: {$de_count}" : "Нехватающих файлов нет.";
        $files_list = implode("\n", array_map(function ($item, $index) {
                            return "\t" . ($index + 1) . ". " . $item;
                        }, $exist, array_keys($exist)));

        $ne_files_list = $de_count ? "Список нехватающих файлов:\n\n" . implode("\n", array_map(function ($item, $index) {
                            return "\t" . ($index + 1) . ". " . $item;
                        }, $doesnt_exist, array_keys($doesnt_exist))) : "";

        $instruction = $de_count ? "Необходимо запустить генерацию файлов заново, так как некоторых файлов не хватает. Для этого перейдите на страницу испытания и нажмите на кнопку \"Запустить генерацию файлов\" в разделе \"Управление испытанием\"." : "";

        Storage::disk('local')->put("private/" . $passport_title, "Паспорт архива\n\n\nКоличество файлов: {$e_count}\n{$ne_files_text}\n\nСписок файлов:\n\n{$files_list}\n\n{$ne_files_list}\n{$instruction}");

        return $passport_title;
    }

    public function getPapersArchive($contest_id, $place_id) {
        $place = Place::findOrFail($place_id);
        $contest = Contest::findOrFail($contest_id);
        $zipFileName = "Работы_{$place->ppi_number}_{$contest->contest_code}.zip";

        $zip = new ZipArchive;
        $zipPath = storage_path($zipFileName);


        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            $auditoriums = Auditorium::where('contest_id', $contest_id)->where('place_id', $place_id)->select('id', 'title')->get();

            $exist = [];
            $doesnt_exist = [];

            foreach ($auditoriums as $auditorium) {
                $path = "app/private/papers/pdfs/{$auditorium->id}.pdf";
                $newTitle = "Работы_Аудитория_{$auditorium->title}.pdf";
                if (File::exists(storage_path($path))) {
                    $zip->addFile(storage_path($path), $newTitle);
                    $exist[] = $newTitle;
                } else {
                    $doesnt_exist[] = $newTitle;
                }
            }

            $passport_title = $this->createArchivePassport($exist, $doesnt_exist, $contest_id, $place_id);
            $passport_path = "app/private/{$passport_title}";
            $zip->addFile(storage_path($passport_path), basename($passport_path));

            File::delete("app/private/" . $passport_title);

            $zip->close();
        } else {
            return response()->json(['error' => 'Не удалось создать ZIP-архив'], 500);
        }

        return Response::download($zipPath)->deleteFileAfterSend(true);

    }

    public function getProtocolsArchive($contest_id, $place_id) {
        $place = Place::findOrFail($place_id);
        $contest = Contest::findOrFail($contest_id);
        $zipFileName = "Протоколы_{$place->ppi_number}_{$contest->contest_code}.zip";

        $zip = new ZipArchive;
        $zipPath = storage_path($zipFileName);


        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            $auditoriums = Auditorium::where('contest_id', $contest_id)->where('place_id', $place_id)->select('id', 'title')->get();

            $exist = [];
            $doesnt_exist = [];

            foreach ($auditoriums as $auditorium) {
                $path = "app/private/protocols/{$auditorium->id}.pdf";
                $newTitle = "Протокол_Аудитория_{$auditorium->title}.pdf";
                if (File::exists(storage_path($path))) {
                    $zip->addFile(storage_path($path), $newTitle);
                    $exist[] = $newTitle;
                } else {
                    $doesnt_exist[] = $newTitle;
                }
            }

            $passport_title = $this->createArchivePassport($exist, $doesnt_exist, $contest_id, $place_id);
            $passport_path = "app/private/{$passport_title}";
            $zip->addFile(storage_path($passport_path), basename($passport_path));

            File::delete("app/private/" . $passport_title);

            $zip->close();
        } else {
            return response()->json(['error' => 'Не удалось создать ZIP-архив'], 500);
        }

        return Response::download($zipPath)->deleteFileAfterSend(true);

    }

    public function getPPIFilesArchive($contest_id) {
        $contest = Contest::findOrFail($contest_id);
        $zipFileName = "Файлы_ППИ_{$contest->contest_code}.zip";

        $zip = new ZipArchive;
        $zipPath = storage_path($zipFileName);


        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            $places = Place::where('contest_id', $contest_id)->get();

            $exist = [];
            $doesnt_exist = [];

            foreach ($places as $place) {
                $newTitle = "PPI_{$place->ppi_number}.PPI";
                $path = "app/private/ppis/" . $newTitle;

                if (File::exists(storage_path($path))) {
                    $zip->addFile(storage_path($path), $newTitle);
                    $exist[] = $newTitle;
                } else {
                    $doesnt_exist[] = $newTitle;
                }
            }

            $zip->close();
        } else {
            return response()->json(['error' => 'Не удалось создать ZIP-архив'], 500);
        }

        return Response::download($zipPath)->deleteFileAfterSend(true);

    }

    public function getPPIFile($contest_id, $place_id) {
        $place = Place::findOrFail($place_id);

        $path = storage_path("app/private/ppis/PPI_{$place->ppi_number}.PPI");
        if (File::exists($path))
            return Response::download($path);

        abort(404);
    }


    public function getProtocol($contest_id, $place_id, $auditorium_id) {
        $auditorium = Auditorium::findOrFail($auditorium_id);
        $auditorium_title = $auditorium->title;
        $path = storage_path("app/private/protocols/{$auditorium_id}.pdf");
        if (File::exists($path))
            return Response::download($path, "Протокол_Аудитория_{$auditorium_title}.pdf");

        abort(404);
    }

    public function getPapers($contest_id, $place_id, $auditorium_id) {
        $auditorium = Auditorium::findOrFail($auditorium_id);
        $auditorium_title = $auditorium->title;
        $path = storage_path("app/private/papers/pdfs/{$auditorium_id}.pdf");

        if (File::exists($path))
            return Response::download($path, "Работы_Аудитория_{$auditorium_title}.pdf");

        abort(404);
    }

    public function sendFiles(Request $request) {
        $request->validate([
            'files.*' => 'required|file|max:65536',
        ]);

        $uploadedFiles = $request->file('files');
        $filePaths = [];

        $allScansCount = 0;
        $allContestMembersCount = 0;

        foreach ($uploadedFiles as $file) {
            $code = time();
            $fileName = $code . '_' . $file->getClientOriginalName();

            $filePath = storage_path('app/private/tmp/' . $fileName);
            $file->move(storage_path('app/private/tmp'), $fileName);

            $zip = new ZipArchive;
            $extractPath = storage_path('app/private/tmp/' . pathinfo($fileName, PATHINFO_FILENAME));

            if ($zip->open($filePath) === TRUE) {
                $indexFileDataRaw = $fileContent = $zip->getFromName("INDEX");
                $indexFileData = json_decode($indexFileDataRaw, true);

                $firstContestMember = ContestMember::where("reg_number", $indexFileData["0"]["REGNUMBER"])->first();
                $auditorium_id = $firstContestMember->auditorium_id;

                foreach ($indexFileData as $key => $data) {
                    if ($key == "INFO") {
                        $contestStartTime = $data["STARTTIME"];
                        $contestEndTime = $data["ENDTIME"];
                    } else if ($key == "SCANS") {
                        foreach ($data as $studentRegNumber => $scans) {
                            if ($studentRegNumber != "Протокол") {
                                $contestMember = ContestMember::where("reg_number", $studentRegNumber)->first();
                                $contestMember->scans()->delete();

                                foreach ($scans as $number => $scanPath) {
                                    $contestMember->scans()->create([
                                        'path' => "{$auditorium_id}_{$scanPath}",
                                        'page_number' => $number
                                    ]);

                                    $allScansCount += 1;
                                }

                                $contestMember->save();
                            }
                        }
                    } else {
                        $regNumber = $data["REGNUMBER"];
                        $contestMember = ContestMember::where("reg_number", $regNumber)->first();

                        $variant = $data["VARIANT"];
                        $endTime = $data["ENDTIME"];
                        $absence = $data["ABSENCE"];
                        $blanks = $data["BLANKS"];
                        $tasks = $data["TASKS"];
                        $notFinished = $data["NOTFINISHED"];

                        $contestMember->end_time = $endTime;
                        $contestMember->absence = $absence;
                        $contestMember->tasks = $tasks;
                        $contestMember->blanks = $blanks;
                        $contestMember->not_finished = $notFinished;

                        $allContestMembersCount += 1;
                        $contestMember->save();
                    }
                }

                $zip->extractTo($extractPath);

                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $fileInfo = $zip->statIndex($i);
                    $fileNameInArchive = $fileInfo['name'];
                    $extension = pathinfo($fileNameInArchive, PATHINFO_EXTENSION);

                    if (strtolower($extension) === 'png') {
                        $fileName = strval($auditorium_id) . "_" . basename($fileNameInArchive);

                        $destinationPath = storage_path('app/private/scans/' . $fileName);
                        rename($extractPath . "/" . $fileNameInArchive, $destinationPath);

                        $extractedPaths[] = 'storage/scans/' . $fileName;
                    }
                }

                $zip->close();
            } else {
                return response()->json(['error' => 'Ошибка при разархивировании ' . $fileName], 500);
            }

            unlink($filePath);
            $this->deleteDirectory($extractPath);
        }

        return response()->json(['allContestMembersCount' => $allContestMembersCount, 'allScansCount' => $allScansCount]);
    }

    public function sendAppeal(Request $request) {
        $validated = $request->validate([
            'email' => 'required|email',
            'phone' => 'required|string',
            'appeal_text' => 'required|string',
            'c_member' => 'required|numeric|exists:contest_members,id'
        ]);

        $contestMember = ContestMember::findOrFail($validated["c_member"]);

        if (!$contestMember->appeal()->exists()) {
            $appeal = new Appeal([
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'text' => $validated['appeal_text']
            ]);

            $contestMember->appeal()->save($appeal);
        }

        return redirect()->back();
    }

    public function deleteDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            $path = $dir . DIRECTORY_SEPARATOR . $item;

            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                unlink($path);
            }
        }

        // Удаляем саму директорию
        rmdir($dir);
    }
}


