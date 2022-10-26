<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'users_tasks';

    protected $fillable = [
        'user_id',
        'description',
        'link',
        'complete',
    ];

    public function images()
    {
        return $this->hasMany(TaskImage::class, 'task_id', 'id');
    }
}
