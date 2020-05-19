<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class FollowerResources extends Resource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'avatar_url' => $this->avatar_url,
            'star' => $this->star ? true : false ,
        ];
    }
}
