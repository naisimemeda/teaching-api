<?php

namespace App\Http\Controllers\Admin;

use App\Events\sendMessageEvent;
use App\Http\Requests\StudentRequest;
use App\Models\School;
use App\Models\SchoolTeacher;
use App\Models\Student;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{

    /**
     * 查看该学校下的学生
     * @param Request $request
     * @param School $school
     * @return mixed
     * @throws AuthorizationException
     */
    public function schoolIndex(request $request, School $school)
    {
        $this->authorize('show', $school);

        $student = $school->student()->paginate($request->get('pageSize', 16));

        $auth = SchoolTeacher::query()
            ->where('teacher_id', Auth::id())
            ->where('school_id', $school->id)
            ->where('is_admin', true)
            ->exists();

        return $this->success(compact('student', 'auth'));
    }

    /**
     * 添加一名学生
     * @param StudentRequest $request
     * @return mixed
     */
    public function store(StudentRequest $request)
    {
        Student::query()->create([
            'school_id' => $request->get('school_id'),
            'name' => $request->get('name'),
            'account' => $request->get('account'),
            'avatar_url' => collect(Student::$avatars)->random(),
            'password' => $request->get('password'),
            'teacher_id' => Auth::id(),
        ]);

        return $this->success('成功');
    }

    /**
     * 发送信息
     * @param StudentRequest $request
     * @return mixed
     */
//    public function chat(StudentRequest $request)
//    {
//        $student = Student::query()->find($request->get('student_id'));
//
//        event(new sendMessageEvent($student, Auth::user(), 'teacher', Auth::id(), '1111'));
//
//        return $this->success('成功');
//    }
}
