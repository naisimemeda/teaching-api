<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $guarded = [];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function student()
    {
        return $this->hasMany(Student::class);
    }

    public function admin()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'id');
    }

    public function teacher()
    {
        return $this->belongsToMany(Teacher::class, 'school_teacher')
            ->withTimestamps();
    }
}
