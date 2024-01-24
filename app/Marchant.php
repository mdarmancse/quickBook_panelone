<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Marchant extends Model
{
    protected $table = "marchants";
    protected $fillable = [
        'name',
        'email',
        'password',
    ];
}
