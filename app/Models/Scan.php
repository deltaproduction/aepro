<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scan extends Model
{
    use HasFactory;

    protected $fillable = ['path', 'page_number', 'contest_member_id'];

    public function contestMember()
    {
        return $this->belongsTo(ContestMember::class);
    }
}
