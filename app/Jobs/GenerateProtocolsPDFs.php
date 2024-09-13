<?php

namespace App\Jobs;

use App\Models\Contest;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Http\Controllers\Contest\GenerateFiles;


class GenerateProtocolsPDFs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $contest_id;
    protected $auditoriums;

    public function __construct($auditoriums, $contest_id)
    {
        $this->auditoriums = $auditoriums;
        $this->contest_id = $contest_id;
    }

    public function handle(): void
    {
        $service = new GenerateFiles();

        foreach ($this->auditoriums as $auditorium) {
            $service->generateProtocol($auditorium);
        }

        $this->done();
    }

    protected function done()
    {
        $contest = Contest::findOrFail($this->contest_id);
        $contest->protocols_status = 1;
        $contest->save();
    }

    public function failed(\Exception $exception)
    {
        $contest = Contest::findOrFail($this->contest_id);
        $contest->protocols_status = 2;
        $contest->save();
    }
}
