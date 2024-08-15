<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContestMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'contest_id', 'level_id', 'place_id', 'reg_number'
    ];

    public static function generateUniqueRegNumber()
    {
        do {
            $number = str_pad(random_int(0, 999999999), 9, '0', STR_PAD_LEFT);
        } while (self::where('reg_number', $number)->exists());

        return $number;
    }
}
