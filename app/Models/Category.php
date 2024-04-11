<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['uuid', 'year', 'number',];

    protected $casts = [
        'uuid'=>'string',
        'year'=>'integer',
        'number'=>'integer',
    ];
}
