<?php

namespace App\Exceptions;

use App\Common\Toast;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    public function render($request, Exception $exception)
    {
        if ($exception instanceof MethodNotAllowedHttpException) {
            if ($exception->getStatusCode() == 405) {
                $method = strtolower($exception->getHeaders()['Allow']);
            } else {
                $method = 'get';
            }
            return Response()->json(['message' => 'allow method ' . $method], 405);
        }

        if ($exception instanceof NotFoundHttpException) {
            if ($exception->getStatusCode() == 404) {
                return Response()->json(['message' => 'not found'], 404);
            }
        }

        if ($exception instanceof ValidationException) {
            // 只读取错误中的第一个错误信息
            $errors = $exception->errors();
            $message = '';
            // 框架返回的是二维数组，因此需要去循环读取第一个数据
            foreach ($errors as $key => $val) {
                $keys = array_key_first($val);
                $message = $val[$keys];
                break;
            }
            return Response()->json(['message' => $message], 422);
        }

        if ($exception instanceof AuthorizationException) {
            return Response()->json(['message' => '没有该权限'], 403);
        }

        if ($exception instanceof ModelNotFoundException) {
            return response(['message' => '模型不存在'], 403);
        }
        return parent::render($request, $exception);
    }
}
