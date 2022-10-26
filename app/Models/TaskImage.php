<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskImage extends Model
{
    use HasFactory;

    protected $table = 'users_tasks_images';

    protected $fillable = [
        'name',
        'task_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
