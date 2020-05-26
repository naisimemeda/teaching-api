<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SchoolRequest;
use App\Models\School;
use App\Models\SchoolTeacher;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SchoolController extends Controller
{

    /**
     * 学校列表
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $school_id = SchoolTeacher::query()->where('teacher_id', Auth::id())->pluck('school_id');
        $school = School::query()->withCount(['student', 'teacher'])->whereIn('id', $school_id)->latest()->paginate($request->get('pageSize', 16));
        return $this->success($school);
    }

    /**
     * 选项列表
     *
     * @param Request $request
     * @return mixed
     */
    public function option(Request $request)
    {
        $teacher = Auth::user();
        $school = $teacher->school()->where('status', true)->select('id', 'name')->get();
        return $this->success($school);
    }

    /**
     * 申请学校
     * @param SchoolRequest $request
     * @return mixed
     * @throws \Throwable
     */
    public function store(SchoolRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $teacher_id = Auth::id();
                //创建学校
                $school = new School([
                    'name' => $request->get('name'),
                    'cover' => $request->get('cover'),
                    'teacher_id' => $teacher_id,
                    'status' => false
                ]);
                $school->save();

                $school->teacher()->attach(Auth::user(), ['is_admin' => true]);
            });
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->failed('申请学校失败');
        }


        return $this->success('成功');
    }
}
