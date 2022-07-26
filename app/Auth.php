<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Auth extends Model
{
    protected $table = 'dcs.auths';
    protected $fillable = [
        'roleId', 'projectcode', 'processId','createdBy',
    ];
    protected $casts = [
        'isAuthorized'=>'boolean'
    ];
}
