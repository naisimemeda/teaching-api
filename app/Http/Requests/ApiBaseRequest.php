<?php

namespace App\Http\Requests;

use App\Exceptions\ApiRequestExcept;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ApiBaseRequest extends FormRequest
{
    /**
     * 程序自定义业务错误码
     *
     * @var int
     */
    protected $code = 0;

    /**
     * http状态码
     *
     * @var int
     */
    protected $statusCode = 400;

    /**
     * Determine if the user is authorized to make this request. * * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @param Validator $validator
     *
     * @throws ApiRequestExcept
     */
    protected function failedValidation(Validator $validator)
    {
        throw new ApiRequestExcept(
            $validator->errors()->first(),
            $this->code,
            $this->statusCode
        );
    }
}
