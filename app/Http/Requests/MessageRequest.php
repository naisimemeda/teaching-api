<?php

namespace App\Http\Requests;


use App\Models\School;
use App\Models\SchoolTeacher;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class MessageRequest extends ApiBaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->route()->getActionMethod()) {
            case 'chatMessages':
                return [
                    'id' => ['required'],
                ];
            case 'sendTeacherMessage':
                return [
                    'message' => ['required'],
                ];
            case 'sendStudentChatMessage':
                return [
                    'student_id' => ['required', 'exists:students,id', function ($attribute, $value, $fail) {
                        $school_id = Student::query()->where('id', $this->get('student_id'))->value('school_id');
                        if (! SchoolTeacher::query()
                            ->where('school_id', $school_id)
                            ->where('teacher_id', Auth::id())
                            ->where('is_admin', true)->exists()) {
                            return $fail('无权限发送信息');
                        }
                    }],
                    'message' => ['required'],
                ];
        }
    }

    public function messages()
    {
        return [
            'id.required' => '请选择学生',
            'message.required' => '请输入信息',
        ];
    }
}
