<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectDiagram extends Model
{
    //
     protected $fillable = [
        'project_id',
        'name',
        'diagram_json'
    ];
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
