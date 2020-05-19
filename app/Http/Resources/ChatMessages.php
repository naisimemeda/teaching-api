<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ChatMessages extends Resource
{
    public function toArray($request)
    {
        if ($this->send_type === 'teacher') {
            $avatar_url = $this->teacher->avatar_url ?? '';
            $name = $this->teacher->name ?? '';
        } else {
            $avatar_url = $this->student->avatar_url ?? '';
            $name = $this->student->name ?? '';
        }

        return [
            'message' => $this->message,
            'send_id' => $this->send_id,
            'send_type' => $this->send_type,
            'created_at' => $this->created_at->toDateTimeString(),
            'avatar_url' => $avatar_url,
            'name' => $name,
        ];
    }
}
