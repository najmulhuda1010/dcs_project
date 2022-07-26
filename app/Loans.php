<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Loans extends Model
{
    protected $table= 'dcs.loans';
    protected $guarded = ['updated_at',];

    protected $casts = [
        'DynamicFieldValue' => 'array'
    ];
    

}
