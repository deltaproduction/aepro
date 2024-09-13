<?php

namespace App\Http\Controllers\Contest;

use App\Http\Controllers\Controller;

use App\Models\Task;
use App\Models\Place;
use App\Models\Level;
use App\Models\Contest;
use App\Models\Auditorium;
use App\Models\TaskPrototype;
use App\Models\ContestOption;
use App\Models\ContestMember;
use App\Http\Controllers\Contest\GenerateFiles;

use App\Jobs\GenerateOptionsPDFs;
use App\Jobs\GenerateProtocolsPDFs;
use App\Jobs\GenerateAuditoriumsPDFs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ContestPaperController extends Controller
{
    public function cartesianProduct($arrays) {
        $result = [[]];

        foreach ($arrays as $array) {
            $newResult = [];
            foreach ($result as $combination) {
                foreach ($array as $element) {
                    $newResult[] = array_merge($combination, [$element]);
                }
            }
            $result = $newResult;
        }

        return $result;
    }

    public function generateOptions($level, $contest_id) {
        ContestOption::where('level_id', $level->id)->delete();


        $tasks = Task::where("level_id", $level->id)->orderBy("number")->get();
        $optionTasks = [];

        foreach ($tasks as $task) {
            $taskPrototypes = TaskPrototype::where("level_id", $level->id)
                ->where("task_id", $task->id)
                ->select("id")
                ->pluck("id")
                ->toArray();

            $optionTasks[] = $taskPrototypes;
        }

        $options = $this->cartesianProduct($optionTasks);

        $optionsList = [];

        foreach ($options as $option) {
            $variantNumber = ContestOption::generateUniqueVariantNumber();

            $contestOption = ContestOption::create([
                "contest_id" => $contest_id,
                "level_id" => $level->id,
                "variant_number" => $variantNumber
            ]);

            $optionsList[$variantNumber] = $option;

            $contestOption->tasks()->attach($option);
        }

        return $optionsList;
    }

    public function generateContestPapers($levelId, $optionsList, $contest_id) {
        GenerateOptionsPDFs::dispatch($levelId, $optionsList, $contest_id);
    }

    public function generateAuditoriumsPapers($place, $contest_id) {
        $auditoriums = Auditorium::where("place_id", $place->id)->get();
        GenerateAuditoriumsPDFs::dispatch($auditoriums, $contest_id);
    }

    public function generateProtocols($place, $contest_id) {
        $auditoriums = Auditorium::where('place_id', $place->id)->get();

        GenerateProtocolsPDFs::dispatch($auditoriums, $contest_id);
    }

    public function setOptionsToContestMembers($contest) {
        $levels = Level::where('contest_id', $contest->id)->get();

        foreach ($levels as $level) {
            $options = ContestOption::where('contest_id', $contest->id)->where('level_id', $level->id)->pluck("id")->toArray();
            $options_count = count($options);
            $contest_members = ContestMember::where('contest_id', $contest->id)->where('level_id', $level->id)->pluck("id")->toArray();

            $option_index = 0;
            foreach ($contest_members as $id) {
                $contest_member = ContestMember::findOrFail($id);
                $contest_member->option_id = $options[$option_index];
                $contest_member->save();

                $option_index += 1;
                $option_index %= $options_count;
            }
        }
    }

    public function startGeneration(Request $request)
    {
        $validatedData = $request->validate([
            'contest_id' => 'required|integer|exists:contests,id'
        ]);

        $contest = Contest::findOrFail($validatedData['contest_id']);

        $contest->options_status = 0;
        $contest->auditoriums_status = 0;
        $contest->protocols_status = 0;
        $contest->save();

        $levels = Level::where("contest_id", $contest->id)->get();
        $places = Place::where("contest_id", $contest->id)->get();

        foreach ($levels as $level) {
            $options = $this->generateOptions($level, $contest->id, $contest->id);
            $this->generateContestPapers($level->id, $options, $contest->id);
        }

        $this->setOptionsToContestMembers($contest);

        foreach ($places as $place) {
            $this->generateAuditoriumsPapers($place, $contest->id);
        }

        $at = $contest->application_type;

        if ($contest->creator_id == auth()->id()) {
            $places = Place::where('contest_id', $contest->id)->get();
        
            foreach ($places as $place) {
                $this->generateProtocols($place, $contest->id);
            }

        } else {
            abort(404);
        }
    }
}
