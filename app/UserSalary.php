<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserSalary extends Model
{
    protected $table = 'user_salaries';

    protected $fillable = [
        'user_id',
        'salary',
        'type',
    ];
}
