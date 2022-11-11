<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectImage extends Model
{
    use HasFactory;
    protected $table = 'projects_images';

    protected $fillable = [
        'name',
        'project_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
