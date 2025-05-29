<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    //
    protected $fillable = [
        'name',
        'description',
        'completed', 
        'user_id', // add this line
    ];
    protected $casts = [
        'completed' => 'boolean',
    ];
    public function users()
    {
        return $this->belongsToMany(User::class, 'project_users', 'project_id', 'user_id');
    }



}
