<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auditorium extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'rows', 'columns', 'contest_id', 'place_id', 'level_id'];
}
