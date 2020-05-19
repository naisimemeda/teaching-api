<?php

namespace App\Http\Requests;

class AuthRequest extends ApiBaseRequest
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
                    'email' => ['required', 'unique:teachers,email'],
                    'name' => ['required'],
                    'password' => ['required', 'max:16', 'min:6'],
                ];
            case 'login':
                return [
                    'email' => ['required'],
                    'password' => ['required', 'max:16', 'min:6'],
                    'provider' => ['required', 'in:teacher,student'],
                ];
            case 'lineAuth':
                return [
                    'oauth_key' => ['required'],
                    'provider' => ['required', 'in:teacher,student'],
                    'id' => ['required'],
                ];
        }
    }

    public function messages()
    {
        return [
            'email.unique' => '邮箱已经存在',
            'password.required' => '密码不能为空',
            'password.max' => '密码长度不能超过16个字符',
            'name.max' => '名字不能超过12个字符',
            'password.min' => '密码长度不能小于6个字符',
            'provider.required' => '请选择登陆方式',
            'provider.in' => '非法参数',
        ];
    }
}
