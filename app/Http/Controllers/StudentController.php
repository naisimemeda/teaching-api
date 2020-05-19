<?php

namespace App\Http\Controllers;

use App\Http\Resources\FollowerResources;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function me(Request $request)
    {
        return $this->success(Student::query()->find(Auth::id()));
    }

    /**
     * 全部教师
     * @param Request $request
     * @return mixed
     */
    public function teachersList(Request $request)
    {
        $teacher = Teacher::with(['star' => function ($query) {
            $query->where('student_id', Auth::id());
        }])->paginate($request->get('pageSize'));

        $results = FollowerResources::collection($teacher);
        return $this->success(['data' => $results, 'total' => $teacher->total()]);
    }

    /**
     * 关注的教师
     * @param Request $request
     * @return mixed
     */
    public function followerTeachers(Request $request)
    {
        $student = Student::query()->find(Auth::id());
        $teachers = $student->followersTeachers()->with(['star' => function ($query) {
            $query->where('student_id', Auth::id());
        }])->paginate($request->get('pageSize'));

        $results = FollowerResources::collection($teachers);
        return $this->success(['data' => $results, 'total' => $teachers->total()]);
    }

    /**
     * 关注老师
     * @param Teacher $teacher
     * @param Request $request
     * @return mixed
     */
    public function follower(Teacher $teacher, Request $request)
    {
        $student = Student::query()->find(Auth::id());

        if ($student->followersTeachers()->find($teacher->id)) {
            return $this->success('成功');
        }

        $student->followersTeachers()->attach($teacher);

        return $this->success('成功');
    }

    /**
     * 取消关注
     * @param Teacher $teacher
     * @param Request $request
     * @return mixed
     */
    public function disFollower(Teacher $teacher, Request $request)
    {
        $student = Student::query()->find(Auth::id());

        $student->followersTeachers()->detach($teacher);

        return $this->success('成功');
    }
}
