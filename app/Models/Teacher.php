<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

class Teacher extends Authenticatable
{
    use Notifiable, HasApiTokens;

    protected $guarded = [];

    protected $hidden = [
        'password'
    ];

    public function fans()
    {
        return $this->belongsToMany(Student::class, 'followers')
            ->withTimestamps()
            ->orderBy('followers.created_at', 'desc');
    }

    public function school()
    {
        return $this->hasMany(School::class);
    }

    public function setPasswordAttribute($value)
    {
        // 如果值的长度等于 60，即认为是已经做过加密的情况
        if (strlen($value) != 60) {
            // 不等于 60，做密码加密处理
            $value = Hash::make($value);
        }
        $this->attributes['password'] = $value;
    }

    public function star()
    {
        return $this->hasOne(Follower::class, 'teacher_id', 'id');
    }
}
