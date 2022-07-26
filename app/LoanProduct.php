<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanProduct extends Model
{
    protected $table= 'dcs.loan_products';
    protected $fillable = [
        'productname', 'productcode', 'status',
    ];
}
