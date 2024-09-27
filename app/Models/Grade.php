<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = ['contest_member_id', 'task_id', 'score', 'final_score'];

    public function contestMember()
    {
        return $this->belongsTo(ContestMember::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
