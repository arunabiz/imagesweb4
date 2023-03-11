<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'path', 'drive_id'];

    public function getDriveUrlAttribute()
    {
        return "https://drive.google.com/uc?id={$this->drive_id}";
    }
}
