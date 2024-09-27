<?php

namespace App\Http\Controllers;

use App\Models\Contest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\Scan;
use App\Models\Expert;
use App\Models\ContestMember;


class FileController extends Controller
{
    public function checkScanAffliance($filename) {
        $scan = Scan::where("path", $filename)->first();

        if ($scan) {
            $contestMember = ContestMember::where("id", $scan->contest_member_id)->first();

            if ($contestMember) {
                if ($contestMember->user_id == auth()->id())
                    return true;
            }
        }

        return false;
    }

    public function checkIfExpertHasAccess($filename) {
        $scan = Scan::where("path", $filename)->first();

        if ($scan) {
            $contestMember = ContestMember::where("id", $scan->contest_member_id)->first();

            if ($contestMember) {
                if (Expert::where("id", $contestMember->expert_id)->where("email", auth()->user()->email)->first()) {
                    return true;
                }
            }
        }

        return false;
    }

    public function checkIfContestCreatorHasAccess($filename) {
        $scan = Scan::where("path", $filename)->first();

        if ($scan) {
            $contestMember = ContestMember::where("id", $scan->contest_member_id)->first();
            $contest = Contest::where('id', $contestMember->contest_id);

            if ($contest->exists()) {
                if ($contest->first()->creator_id == auth()->id()) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getScan($filename) {
        if ($this->checkScanAffliance($filename)
            or $this->checkIfExpertHasAccess($filename)
            or $this->checkIfContestCreatorHasAccess($filename)) {

            if (!Storage::exists('private/scans/' . $filename)) {
                abort(404);
            }

            $file = Storage::get('private/scans/' . $filename);
            $type = Storage::mimeType('private/scans/' . $filename);

            return response($file, 200)->header('Content-Type', $type);
        }

        abort(404);
    }
}
