<?php

namespace App\Http\Controllers\Contest;

use App\Models\Task;
use App\Models\Level;
use App\Models\Place;
use App\Models\Contest;
use App\Models\Auditorium;
use App\Models\ContestOption;
use App\Models\TaskPrototype;
use App\Models\ContestMember;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Support\Facades\DB;

use Symfony\Component\Process\Process;


class GenerateFiles
{
    public function generateProtocol($auditorium) {
        $result = [];

        $contest_members = DB::table('contest_members')
            ->join('users', 'contest_members.user_id', '=', 'users.id')
            ->join('contest_options', 'contest_members.option_id', '=', 'contest_options.id')
            ->leftJoin('schools', 'contest_members.school_id', '=', 'schools.id')
            ->where('contest_members.auditorium_id', $auditorium->id)
            ->select('reg_number', 'school_name', 'school_id', 'short_title', 'contest_members.seat as seat', 'contest_options.variant_number as variant')
            ->orderBy('seat')
            ->get();

        $contest = Contest::findOrFail($auditorium->contest_id);
        $level = Level::findOrFail($auditorium->level_id);
        $place = Place::findOrFail($auditorium->place_id);

        $data = [
            'auditorium_title' => $auditorium->title,
            'contest_members' => $contest_members,
            'contest_title' => $contest->title,
            'level_title' => $level->title,
            'ppi_number' => $place->ppi_number
        ];

        $pdf = Pdf::loadView('pdf.protocol', $data);
        $pdf->setPaper('A4', 'landscape');

        $filePath = storage_path("app/private/protocols/{$auditorium->id}.pdf");
        $pdf->save($filePath);

        $pdf->download("{$auditorium->id}.pdf");
    }

    public function generatePDFFromTex($inputPath, $outputPath, $title) {
        $process = new Process(['pdflatex', '-interaction=nonstopmode', '-output-directory="' . storage_path("app/private/options") . '"', '-jobname="'.$title . '"', '"' . $inputPath . '"']);

        try {
            $process->mustRun();
            \Log::info('PDF generated successfully: ' . $outputPath);
        } catch (ProcessFailedException $exception) {
            \Log::error('Error generating PDF: ' . $exception->getMessage());
        }
    }

    public function generateTexFiles($levelId, $optionsList) {
        $level = Level::findOrFail($levelId);
        $raw_pattern = $level->pattern;
        $pattern = $raw_pattern ? $raw_pattern : "";

        foreach ($optionsList as $variantNumber => $option) {
            $current_pattern = "{$pattern}";
            foreach ($option as $key => $tpId) {
                $task_prototype = TaskPrototype::findOrFail($tpId);
                $task = Task::findOrFail($task_prototype->task_id);
                $task_number = $task->number;

                $search = "{block{task{$task_number}}}";

                $current_pattern = str_replace($search, $task_prototype->task_text, $current_pattern);
            }

            file_put_contents(storage_path("app/private/texs/{$variantNumber}.tex"), $current_pattern);
        }
    }

    public function generateAuditoriumFile($auditorium) {
        $contest_members = ContestMember::where('auditorium_id', $auditorium->id)->get();
        $data = [];

        foreach ($contest_members as $contest_member) {
            $option_id = $contest_member->option_id;
            $option = ContestOption::findOrFail($option_id);

            $data[$contest_member->reg_number] = $option->variant_number;
        }

        $serialized_data = json_encode([$auditorium->id, $data]);

        $tmp_file_name = uniqid();
        $tmp_file_path = storage_path("app/private/papers/tmp/{$tmp_file_name}");
        file_put_contents($tmp_file_path, $serialized_data);

        $process = new Process(['python3', storage_path("app/private/papers/generate_papers.py"), storage_path('app/private/papers/tmp/' . $tmp_file_name)]);

        try {
            $process->mustRun();
            \Log::info('PDF generated successfully');

        } catch (ProcessFailedException $exception) {
            \Log::error('Error generating auditorium PDF' . $exception->getMessage());
        }

        unlink($tmp_file_path);
    }

    public function generatePPIFile($place) {
        $pattern = [
            "PPI_INFO" => [
                "PPI_NUMBER" => $place->ppi_number,
                "PPI_TITLE" => $place->title,
                "PPI_CITY" => $place->locality ? $place->locality : ""
            ],
            "AUDITORIUMS" => [],
        ];

        $auditoriums = Auditorium::where('place_id', $place->id)->get();

        foreach ($auditoriums as $auditorium) {
            $pattern["AUDITORIUMS"][$auditorium->title] = [
                "STUDENTS" => []
            ];

            $contestMembers = DB::table('contest_members')
            ->join('contest_options', 'contest_members.option_id', '=', 'contest_options.id')
            ->select('reg_number', 'variant_number')
            ->where('contest_members.auditorium_id', $auditorium->id)
            ->get();

            foreach ($contestMembers as $contestMember) {
                $pattern["AUDITORIUMS"][$auditorium->title]["STUDENTS"][$contestMember->reg_number] = strval($contestMember->variant_number);
            }
        }

        $json = json_encode($pattern);

        file_put_contents(storage_path("app/private/ppis/PPI_{$place->ppi_number}.PPI"), $json);
    }

    public function generateAllPPIFiles($contest) {
        $places = Place::where('contest_id', $contest->id)->get();

        foreach ($places as $place) {
            $this->generatePPIFile($place);
        }
    }
}
