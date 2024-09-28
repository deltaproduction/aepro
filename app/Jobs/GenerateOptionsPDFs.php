<?php

namespace App\Jobs;

use App\Models\Contest;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\File;

use Symfony\Component\Process\Exception\ProcessFailedException;

use App\Http\Controllers\Contest\GenerateFiles;


class GenerateOptionsPDFs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $levelId;
    protected $contest_id;
    protected $optionsList;

    public function __construct(int $levelId, array $optionsList, int $contest_id)
    {
        $this->optionsList = $optionsList;
        $this->levelId = $levelId;
        $this->contest_id = $contest_id;
    }

    public function handle(): void
    {
        $outputPath = storage_path('app/private/options/');

        $service = new GenerateFiles();

        $service->generateTexFiles($this->levelId, $this->optionsList);

        foreach ($this->optionsList as $variantNumber => $option) {
            $texPath = "{$variantNumber}.tex";
            $inputPath = storage_path('app/private/texs/' . $texPath);
            $service->generatePDFFromTex($inputPath, $outputPath, "{$variantNumber}");

            File::delete(storage_path("app/private/texs/".$texPath));
        }
        $this->done();
    }

    protected function done()
    {
        $contest = Contest::findOrFail($this->contest_id);
        $contest->options_status = 1;
        $contest->save();
    }

    public function failed($exception)
    {
        $contest = Contest::findOrFail($this->contest_id);
        $contest->options_status = 2;
        $contest->save();

        return json_encode($exception);
    }
}
