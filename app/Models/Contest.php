<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Place;
use App\Models\Level;
use App\Models\Auditorium;
use App\Models\ContestMember;

class Contest extends Model
{
    use HasFactory;

    protected $fillable = ['creator_id', 'title', 'contest_code', 'application_type', 'options_status', 'auditoriums_status', 'protocols_status'];

    public static function generateUniqueContestCode()
    {
        do {
            $code = str_pad(random_int(0, 9999999), 7, '0', STR_PAD_LEFT);
        } while (self::where('contest_code', $code)->exists());

        return $code;
    }

    public function getPlacesCount()
    {
        $result = 0;
        $places = Place::where('contest_id', $this->id)->get();

        foreach ($places as $place) {
            $result += $place->getPlacesCount();
        }

        return $result;
    }


    public function getFreePlacesCount($place_id, $level_id)
    {
        $contestMembersCount = ContestMember::where('place_id', $place_id)->where('level_id', $level_id)->count();

        $allPlaces = 0;
        $auditoriums = Auditorium::where('place_id', $place_id)->where('level_id', $level_id)->get();

        foreach ($auditoriums as $auditorium) {
            $allPlaces += $auditorium->rows * $auditorium->columns;
        }

        return $allPlaces - $contestMembersCount;
    }
}
