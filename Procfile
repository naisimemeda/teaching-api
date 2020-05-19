web: vendor/bin/heroku-php-nginx -C nginx_app.conf public/

worker: php artisan queue:restart && php artisan queue:work --sleep=3 --tries=3 --daemon
