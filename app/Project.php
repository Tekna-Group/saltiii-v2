<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    //
    protected $fillable = ['name', 'description','user_id'];
    public function users()
{
    return $this->belongsToMany(User::class, 'project_user'); // if many-to-many
}



}
