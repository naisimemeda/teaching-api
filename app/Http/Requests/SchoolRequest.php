<?php

namespace App\Http\Requests;


use App\Models\School;
use App\Models\SchoolTeacher;
use Illuminate\Support\Facades\Auth;

class SchoolRequest extends ApiBaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->route()->getActionMethod()) {
            case 'inviteTeacher':
                return [
                    'school_id' => ['required', 'integer', function ($attribute, $value, $fail) {
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
                    'email' => ['required', 'email']
                ];
            case 'store':
                return [
                    'name' => ['required'],
                    'cover' => ['required']
                ];
        }
    }

    public function messages()
    {
        return [
            'school_id.required' => '请选择学校',
            'email.required' => '请输入邀请邮箱',
            'email.email' => '错误的邮箱地址',
            'name.required' => '请填写学校名称',
            'cover.required' => '上传一个图片吧',
        ];
    }
}
