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

    public function projects()
    {
        return $this->hasOne(Project::class, 'project_id', 'id');
    }
}
