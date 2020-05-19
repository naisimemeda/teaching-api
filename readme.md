### Teaching 

- 基于Laravel 5.5 LTS和 Laravel Passport Line 第三方登陆, WebSocket 基于 Pusher, 前后端分离使用 Vue, 前端仓库地址 [teaching](https://github.com/naisimemeda/teaching)

## 主要扩展包使用

|扩展包|描述|使用场景|
|---|---|---|
|[laravel/passport](https://github.com/laravel/passport)| OAuth2 | 多表登陆, 以及第三方登陆 |
|[linecorp/line-bot-sdk](https://github.com/line/line-bot-sdk-php)| Line 官方SDK | 消息通知 |
|[socialiteproviders/line](https://github.com/SocialiteProviders/Line)| 接入 Line |实现第三方登陆|
|[pusher/pusher-php-server](https://github.com/pusher/pusher-http-php)| 实现 WebSocket  | 广播通知、 私人频道 |
|[encore/laravel-admin](https://github.com/z-song/laravel-admin)| 管理员后台 | 快速集成后台 |
|[overtrue/laravel-filesystem-qiniu](https://github.com/overtrue/laravel-filesystem-qiniu)| 七牛云存储 | 替换 Laravel-admin disk 以及前台图片上传 |
#### 部署

- 以下基于 docker-compose 部署
```
git clone https://github.com/naisimemeda/teaching-api.git

copy .env.example .env
```

docker-compose.yml  配置:  
```
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```
.env 配置:  
配置 `Email`:  

``` 
MAIL_DRIVER=
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=xxx@gmail.com
MAIL_FROM_NAME=test
```

配置 `Pusher`: 
``` 
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=eu
```

配置 `Pusher`: 
``` 
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=eu
```

配置 `Line`: 
``` 
LINE_KEY=
LINE_SECRET=
LINE_REDIRECT_URI=
```

配置 `Line Channel`: 
``` 
LINE_CHANNEL=
LINE_CHANNEL_SECRET=
```

配置 `前端地址`: 
``` 
WEB_URL=
```
配置 `七牛云`: 
``` 
QINIU_ACCESS_KEY=
QINIU_SECRET_KEY=
QINIU_BUCKET=
QINIU_DOMAIN=
QINIU_CDN=
```

- 配置nginx：docker/vhost.conf
- 启动项目
```
docker-compose up -d

进入容器
docker-compose exec laravel bash

安装 composer
composer install

执行迁移
php artisan migrate

passport 生成
php artisan passport:keys

创建客户端
php artisan passport:client --password --name='teaching'

.env 配置客户端
client_id=
client_secret=

执行队列任务
php artisan queue:work
```
