<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appeal extends Model
{
    use HasFactory;

    protected $fillable = ['aa', 'text', 'email', 'phone', 'changed'];

    public function contestMember()
    {
        return $this->belongsTo(ContestMember::class);
    }
}
