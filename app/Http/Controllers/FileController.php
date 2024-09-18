<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\Scan;
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
        return false;
    }

    public function getScan($filename) {
        if ($this->checkScanAffliance($filename) or $this->checkIfExpertHasAccess($filename)) {
            $path = storage_path('app/private/' . $filename);

            if (!Storage::exists('private/scans/' . $filename)) {
                abort(404);
            }

            $file = Storage::get('private/scans/' . $filename);
            $type = Storage::mimeType('private/scans/' . $filename);

            return response($file, 200)->header('Content-Type', $type);
        }
        return abort(404);
    }
}
