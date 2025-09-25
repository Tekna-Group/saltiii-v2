<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskCommentUserTagged extends Model
{
    //
    protected $table = 'task_comment_user_tagged';

    protected $fillable = ['task_comment_id', 'user_id','task_id'];

    public function comment()
    {
        return $this->belongsTo(TaskComment::class, 'task_comment_id','task_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
