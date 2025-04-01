<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    // Use file_paths for storing multiple file paths
    protected $fillable = ['title', 'description', 'price', 'duration', 'user_id', 'file_paths'];

    public function instructor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function enrollments() {
        return $this->hasMany(Enrollment::class);
    }
    
}