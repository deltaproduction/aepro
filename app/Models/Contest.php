<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contest extends Model
{
    use HasFactory;

    protected $fillable = ['creator_id', 'title', 'contest_code'];

    public static function generateUniqueContestCode()
    {
        do {
            $code = str_pad(random_int(0, 9999999), 7, '0', STR_PAD_LEFT);
        } while (self::where('contest_code', $code)->exists());

        return $code;
    }
}
