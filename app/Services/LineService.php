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
        $client = new Client();
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ];
        $response = $client->request('GET', 'https://api.line.me/v2/profile', [
            'headers' => $headers
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }
}
