<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Level;
use App\Models\Contest;

class Expert extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'contest_id', 'level_id', 'expert_status'
    ];

    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }

    public function contest()
    {
        return $this->belongsTo(Contest::class, 'contest_id');
    }
}
