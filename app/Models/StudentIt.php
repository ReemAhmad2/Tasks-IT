<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentIt extends Model
{
    use HasFactory;
    protected $table = 'students_it';
    protected $fillable = [
        'uuid',
        'number',
        'email',
    ];

    protected $casts = [
        'uuid'=>'string',
        'number'=>'integer',
        'email'=>'string',
    ];
}
