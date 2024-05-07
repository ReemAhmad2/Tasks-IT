<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Task extends Model
{
    use HasFactory;
    protected $fillable = ['uuid','teacher_id','description',
                'deadline','subject_id','max_student_count'];

    protected $casts = [
        'uuid'=>'string',
        'description'=>'string',
        'deadline'=>'date',
        'max_student_count'=>'int'
    ];

    public static function roles()
    {
        return  [
        'description' => 'required|string',
        'max_student_count' => 'integer|min:1',
        'deadline' => 'required|date|after:today',
        'subject' => 'required|string|exists:subjects,uuid',
        'category' => 'required|array',
        'category.*' => 'required|string|exists:categories,uuid',
        ];
    }
    
    public function getDurationAttribute()
    {
        $createdAt = Carbon::parse($this->created_at);
        $now = Carbon::now();
        $duration = $createdAt->diffForHumans($now);
        return $duration;
    }
    public function getStatusAttribute()
    {
        $user = Auth::user();
        $submissions = $user->student->submissions;
        foreach($submissions as $submission)
        {
            if($submission->task_id == $this->id)
            {
                return 'Submitted';
            }
        }
        return 'Not Submitted';
    }

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
