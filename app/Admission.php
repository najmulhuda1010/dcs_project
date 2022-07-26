<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Admission extends Model
{
    protected $table= 'dcs.admissions';
    protected $guarded = [];

    protected $casts = [
        'DynamicFieldValue' => 'array'
    ];
}
