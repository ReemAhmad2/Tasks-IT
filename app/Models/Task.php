<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $fillable = [
        'uuid',
        'teacher_id',
        'description',
        'deadline',
        'subject_id',
        'max_student_count',
    ];

    protected $casts = [
        'uuid'=>'string',
        'description'=>'string',
        'deadline'=>'date',
        'max_student_count'=>'int',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function submissions()
    {
        return $this->hasMany(TaskSubmission::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
