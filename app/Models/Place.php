<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Auditorium;

class Place extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'locality', 'address', 'contest_id', 'ppi_number'
    ];

    public function getPlacesCountByLevels()
    {
        $result = [];
        $levels = Level::where('contest_id', $this->contest_id)->get();

        foreach ($levels as $level) {
            $auditoriums = Auditorium::where('level_id', $level->id)->where('place_id', $this->id)->get();
            $count = 0;

            foreach ($auditoriums as $auditorium) {
                $count += $auditorium->rows * $auditorium->columns;
            }

            $result[$level->id] = $count;
        }

        return $result;
    }

    public function getPlacesCount()
    {
        $result = 0;

        foreach ($this->getPlacesCountByLevels() as $level => $count) {
            $result += $count;
        }

        return $result;
    }

    public static function generateUniquePPINumber()
    {
        do {
            $code = str_pad(random_int(0, 999), 3, '0', STR_PAD_LEFT) . "-" . str_pad(random_int(0, 999), 3, '0', STR_PAD_LEFT);
        } while (self::where('ppi_number', $code)->exists());

        return $code;
    }
}
