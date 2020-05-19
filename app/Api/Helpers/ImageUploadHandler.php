<?php

namespace App\Api\Helpers;

use Illuminate\Support\Facades\Storage;
use  Illuminate\Support\Str;

class ImageUploadHandler
{
    protected $allowed_ext = ["png", "jpg", 'jpeg'];

    public function save($file, string $folder): array
    {
        $folder_name = "images/$folder/";

        $upload_path = storage_path() . '/' . $folder_name;

        $extension = strtolower($file->getClientOriginalExtension()) ?: 'png';

        $filename = 'avatar' . time() . '_' . Str::random(10) . '.' . $extension;

        if (!in_array($extension, $this->allowed_ext)) {
            return false;
        }

        $file->move($upload_path, $filename);

        $path = $upload_path . $filename;
        $oss_upload = $folder_name . $filename;
        $disk = Storage::disk('qiniu');
        $disk->put($oss_upload, fopen($path, 'r'));
        unlink($path);
        return [
            'path' => 'http://' . env('QINIU_CDN') . '/' . $oss_upload
        ];
    }
}
