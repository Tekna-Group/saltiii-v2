<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    //
    public function user()
    {
       return $this->belongsTo(User::class);
    }
}
