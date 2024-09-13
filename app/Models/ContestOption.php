<?php

namespace App\Models;

use App\Models\TaskPrototype;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContestOption extends Model
{
    use HasFactory;

    protected $fillable = ['contest_id', 'level_id', 'variant_number'];

    public function tasks() {
        return $this->belongsToMany(TaskPrototype::class, 'contest_option_task');
    }

    public static function generateUniqueVariantNumber()
    {
        do {
            $number = random_int(1000, 9999);
        } while (self::where('variant_number', $number)->exists());

        return $number;
    }
}
