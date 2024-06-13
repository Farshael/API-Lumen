<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StuffStock extends Model
{
    use SoftDeletes; //digunakan hanya untuk table yang menggunakan fitur soft deletes
    protected $fillable = ["stuff_id", "total_available", "total_defec"];

    public function stuff()
    {
        return $this->belongsTo(Stuff::class);
    }
}
