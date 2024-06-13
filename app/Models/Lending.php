<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lending extends Model
{
    use SoftDeletes; //digunakan hanya untuk table yang menggunakan fitur soft deletes
    protected $fillable = ["stuff_id", 
    "date_time", "name", "user_id", "notes", 
    "total_stuff"];

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function stuff()
    {
        return $this->belongsTo(Stuff::class);
    }
    public function restoration()
    {
        return $this->hasOne(Restoration::class);
    }
}
