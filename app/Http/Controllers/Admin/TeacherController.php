<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SchoolRequest;
use App\Models\School;
use App\Models\SchoolTeacher;
use App\Models\Teacher;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TeacherController extends Controller
{
    public function me(Request $request)
    {
        return $this->success(\Auth::user());
    }

    /**
     * 查看该学校下的老师
     * @param Request $request
     * @param School $school
     * @return mixed
     * @throws AuthorizationException
     */
    public function schoolIndex(request $request, School $school)
    {
        $this->authorize('show', $school);

        $teachers = $school->teacher()->paginate($request->get('pageSize', 16));

        $auth = SchoolTeacher::query()
            ->where('teacher_id', Auth::id())
            ->where('school_id', $school->id)
            ->where('is_admin', true)
            ->exists();

        return $this->success(compact('teachers', 'auth'));
    }

    /**
     * 邀请其他教师加入学校
     * @param SchoolRequest $request
     * @return mixed
     */
    public function inviteTeacher(SchoolRequest $request)
    {
        $key = Str::random(10);
        $school_name = School::query()->where('id', $request->get('school_id'))->value('name');
        $email = $request->get('email');
        Cache::put($key, ['email' => $email, 'school_id' => $request->get('school_id')], 600);
        $this->sendEmailConfirmationTo($email, $key, $school_name);
        return $this->success('成功');
    }


    public function sendEmailConfirmationTo(string $email, string $key, string $school_name)
    {
        $view = 'emails.confirm';
        $data = [
            'link' => config('app.web_url') . "#/invite?key=$key&school=$school_name",
        ];
        $from = '2514430140@qq.com';
        $name = '邀请您成为学校老师！请确认。';
        $to = $email;
        $subject = "邀请您成为学校老师！请确认。";

        Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
            $message->from($from, $name)->to($to)->subject($subject);
        });
    }


    /**
     * 接受邀请
     * @param Request $request
     * @return mixed
     */
    public function acceptInvitation(request $request)
    {
        $key = $request->get('key');

        if (!$cache = Cache::pull($key)) {
            return $this->failed('邀请码已过期');
        }

        if (!$teacher = Teacher::query()->where('email', $cache['email'])->first()) {
            return $this->failed('请先进行邮箱注册');
        }

        if (SchoolTeacher::query()->where('teacher_id', $teacher->id)->where('school_id', $cache['school_id'])->exists()) {
            return $this->failed('已经加入了该学校');
        }

        SchoolTeacher::query()->create([
            'teacher_id' => $teacher->id,
            'school_id' => $cache['school_id']
        ]);

        return $this->success('成功');
    }
}
