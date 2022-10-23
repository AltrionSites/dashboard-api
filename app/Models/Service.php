<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'description',
        'image',
        'user_service_manager',
    ];

    public function getUser($id)
    {
        return $this->belongsTo(User::class, 'user_service_manager', 'id')->first();
    }
}
