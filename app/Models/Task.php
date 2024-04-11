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
        'group',
    ];

    protected $casts = [
        'uuid'=>'string',
        'description'=>'string',
        'deadline'=>'date',
        'group'=>'string',
    ];
}
