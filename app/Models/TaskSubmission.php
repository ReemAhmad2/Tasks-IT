<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskSubmission extends Model
{
    use HasFactory;
    protected $fillable = [
        'uuid',
        'task_id',
        'student_id',
        'file'
    ];

    protected $casts = [
        'uuid'=>'string',
        'file'=>'string'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
