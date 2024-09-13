<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        's_id', 'city_id', 'region_id', 'short_title', 'full_title', 'region', 'website', 'address', 'city'
    ];
}
