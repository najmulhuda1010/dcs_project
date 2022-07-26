<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Process extends Model
{
    protected $table= 'dcs.processes';
    protected $fillable = [
        'process',
    ];
}
