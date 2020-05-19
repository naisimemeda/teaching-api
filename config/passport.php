<?php

return [
    'proxy' => [
        'client_id' => env('client_id', 1),
        'client_secret' => env('client_secret', ''),
        'scope' => '*',
        'grant_type' => 'password',
    ]
];
