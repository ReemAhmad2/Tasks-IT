<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $fillable = [
        'uuid',
        'task_id',
        'user_id',
        'comment_text'
    ];

    protected $casts = [
        'uuid'=>'string',
        'comment_text'=>'string'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
