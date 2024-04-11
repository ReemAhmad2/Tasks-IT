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
        'comment_text',
    ];

    protected $casts = [
        'uuid'=>'string',
        'comment_text'=>'string',
    ];
}
