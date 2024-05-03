<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;
    protected $fillable = ['uuid','name','year','term'];

    protected $casts = [
        'uuid'=>'string',
        'name'=>'string',
        'year'=>'integer',
        'term'=>'integer'
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function getCountTasksAttribute(){
        $count = $this->tasks->count();
        return $count;
    }
}
