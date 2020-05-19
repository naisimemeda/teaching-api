<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolTeacher extends Model
{
    protected $table = 'school_teacher';

    protected $guarded = [];

    protected $casts = [
        'is_admin' => 'boolean',
    ];

    public function teacher()
    {
        return $this->belongsToMany(Teacher::class);
    }
}
