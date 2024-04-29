<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'number_of_student',
        'user_id',
        'category_id'
    ];

    protected $casts = [
        'uuid'=>'string',
        'number_of_student'=>'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function submissions()
    {
        return $this->hasMany(TaskSubmission::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
