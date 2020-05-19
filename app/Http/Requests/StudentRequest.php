<?php

namespace App\Http\Requests;

use App\Models\School;
use App\Models\SchoolTeacher;
use Illuminate\Support\Facades\Auth;

class StudentRequest extends ApiBaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->route()->getActionMethod()) {
            case 'store':
                return [
                    'school_id' => ['required', function ($attribute, $value, $fail) {
                        $where = [
                            ['teacher_id', Auth::id()],
                        ];
                        if (!School::query()->where($where)->where('status', true)->find($this->get('school_id'))) {
                            return $fail('该学校不存在 或 未通过审核');
                        }
                        if (!SchoolTeacher::query()->where($where)->where('is_admin', true)->exists()) {
                            return $fail('非管理员无权限邀请他人');
                        }
                    }],
                    'account' => ['required', 'unique:students,account'],
                    'password' => ['required'],
                    'name' => 'required'
                ];
            case 'chat':
                return [
                    'student_id' => ['required', 'exists:students,id'],
                ];
        }
    }

    public function messages()
    {
        return [
            'student_id.required' => '请选择学生',
            'account.required' => '请填写账号',
            'account.unique' => '账号已存在, 请换一个试试吧',
            'password.required' => '请填写账号密码',
            'student_id.unique' => '学生账号已存在',
            'student_id.exists' => '学生不存在',
            'name.required' => '请输入名称'
        ];
    }
}
