<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => public_path(),
            'url' => env('APP_URL').'/public',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
        ],

        'qiniu' => [
            'driver'     => 'qiniu',
            'access_key' => env('QINIU_ACCESS_KEY'),
            'secret_key' => env('QINIU_SECRET_KEY'),
            'bucket'     => env('QINIU_BUCKET'),
            'domain'     => env('QINIU_DOMAIN'),
        ],

        'qiniuadmin' => [
            'driver'  => 'qiniu',
            'domains' => [
                'default'   => env('QINIU_CDN'), //你的七牛域名QINIU_CDN
            ],
            'domain' => env('QINIU_CDN'),
            'access_key'=> env('QINIU_ACCESS_KEY'),  //AccessKey
            'secret_key'=> env('QINIU_SECRET_KEY'),  //SecretKey
            'bucket'    => env('QINIU_BUCKET'),  //Bucket名字
            'url'       => env('QINIU_CDN'),  // 填写文件访问根url
            'notify_url'=> env('QINIU_CDN'),
            'access'    => 'public',
            'http_url'    => env('QINIU_HTTPCDN'),
        ],
    ],

];
