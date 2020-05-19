<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $guarded = [];

    public function student()
    {
        return $this->belongsTo(Student::class, 'send_id', 'id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'send_id', 'id');
    }
}
