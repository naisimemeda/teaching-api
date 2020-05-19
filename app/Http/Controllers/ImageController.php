<?php

namespace App\Http\Controllers;

use App\Api\Helpers\ImageUploadHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImageController extends Controller
{
    public function upload(Request $request, ImageUploadHandler $uploader)
    {
        $result = $uploader->save($request->file('file'), 'avatars', Auth::id());

        return $this->success($result['path']);
    }
}
