<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stuff extends Model
{
    use SoftDeletes; //digunakan hanya untuk table yang menggunakan fitur soft deletes
    protected $fillable = ["name", "category"];

    public function stuffStock()
    {
        //one to one
        return $this->hasOne(StuffStock::class);
    }
    public function inboundStuffs()
    {   
        //one to many
        return $this->hasMany(InboundStuff::class);
    }
    public function Lendings()
    {   
        //one to many
        return $this->hasMany(Lending::class);
    }
}
