<?php

namespace App\Http\Controllers\Admin;

use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FollowerController extends Controller
{
    /**
     * 自己的 fans
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $teacher = Teacher::query()->find(Auth::id());
        $fans = $teacher->fans()->paginate($request->get('pageSize'));
        return $this->success($fans);
    }
}
