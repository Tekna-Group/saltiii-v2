<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskActivity extends Model
{
    //
       public function user()
        {
            return $this->belongsTo(User::class);
        }
        public function task()
        {
            return $this->belongsTo(Task::class);
        }
        public function project()
        {
            return $this->belongsTo(Project::class);
        }
}
