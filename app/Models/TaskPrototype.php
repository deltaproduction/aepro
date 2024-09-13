<?php

namespace App\Models;

use App\Models\ContestOption;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskPrototype extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_text', 'task_answer', 'prototype_number', 'task_id', 'level_id', 'contest_id'
    ];

    public static function generateUniquePrototypeNumber()
    {
        do {
            $number = str_pad(random_int(0, 999999999999), 12, '0', STR_PAD_LEFT);
        } while (self::where('prototype_number', $number)->exists());

        return $number;
    }

    public function contestOptions() {
        return $this->belongsToMany(ContestOption::class, 'contest_option_task');
    }
}
