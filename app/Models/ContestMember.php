<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContestMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'contest_id', 'level_id', 'place_id', 'expert_id', 'reg_number', 'school_id', 'school_name', 'option_id', 'end_time', 'absence', 'blanks', 'tasks', 'not_finished'
    ];

    public static function generateUniqueRegNumber(): string
    {
        do {
            $number = str_pad(random_int(0, 999999999), 9, '0', STR_PAD_LEFT);
        } while (self::where('reg_number', $number)->exists());

        return $number;
    }

    public function scans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Scan::class);
    }

    public function expert(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Expert::class);
    }

    public function grades(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function appeal()
    {
        return $this->hasOne(Appeal::class);
    }
}
