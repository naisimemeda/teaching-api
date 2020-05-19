<?php

namespace App\Services;

use GuzzleHttp\Client;

class LineService
{
    /**
     * 获取用户信息
     * @param $token
     * @return array
     */
    public function getUserProfile($token): array
    {
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ];

        $response = (new Client)->get('https://api.line.me/v2/profile', compact('headers'));

        return json_decode($response->getBody()->getContents(), true);
    }
}
