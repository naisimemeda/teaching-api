<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

class Student extends Authenticatable
{
    use Notifiable, HasApiTokens;

    public static $avatars = [
        'https://cdn.learnku.com/uploads/images/201710/14/1/s5ehp11z6s.png',
        'https://cdn.learnku.com/uploads/images/201710/14/1/Lhd1SHqu86.png',
        'https://cdn.learnku.com/uploads/images/201710/14/1/LOnMrqbHJn.png',
        'https://cdn.learnku.com/uploads/images/201710/14/1/xAuDMxteQy.png',
        'https://cdn.learnku.com/uploads/images/201710/14/1/ZqM7iaP4CR.png',
        'https://cdn.learnku.com/uploads/images/201710/14/1/NDnzMutoxX.png',
    ];

    protected $guarded = [];

    protected $hidden = [
        'password'
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function followersTeachers()
    {
        return $this->belongsToMany(Teacher::class, 'followers')
            ->withTimestamps()
            ->orderBy('followers.created_at', 'desc');
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

    public function findForPassport($username)
    {
        filter_var($username, FILTER_VALIDATE_EMAIL) ?
            $credentials['email'] = $username :
            $credentials['account'] = $username;
        return self::where($credentials)->first();
    }
}
