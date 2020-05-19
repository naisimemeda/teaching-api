<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class ApiRequestExcept extends Exception
{
    /**
     * @var int http status code
     */
    protected $statusCode;

    public function __construct($message = "", $code = 0, $statusCode = 400)
    {
        $this->statusCode = $statusCode;
        parent::__construct($message, $code);
    }

    public function render(Request $request)
    {
        return response([
            'code' => $this->code,
            'message' => $this->message
        ])->setStatusCode($this->statusCode);
    }
}
