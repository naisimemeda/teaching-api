FROM php:7.4-fpm

RUN apt-get clean \
    && cd /var/lib/apt \
    && mv lists lists.old \
    && mkdir -p lists/partial \
    && apt-get clean \
    && echo "deb http://mirrors.aliyun.com/debian/ buster main non-free contrib \n \
        deb-src http://mirrors.aliyun.com/debian/ buster main non-free contrib \n \
        deb http://mirrors.aliyun.com/debian-security buster/updates main \n \
        deb-src http://mirrors.aliyun.com/debian-security buster/updates main \n \
        deb http://mirrors.aliyun.com/debian/ buster-updates main non-free contrib \n \
        deb-src http://mirrors.aliyun.com/debian/ buster-updates main non-free contrib" > /etc/apt/sources.list

# 更新及安装库
RUN apt-get update

# 类库
RUN apt-get install -y libmagickwand-dev libmcrypt-dev libpq-dev libzip-dev zip

# PHP扩展安装
RUN docker-php-ext-install -j$(nproc) pdo_mysql bcmath sockets
RUN pecl install redis imagick mcrypt zip
RUN docker-php-ext-enable redis imagick mcrypt zip

ADD php.ini /usr/local/etc/php/php.ini

# OPcache
COPY opcache.ini /home/opcache.ini
RUN cat /home/opcache.ini >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

# Install composer
COPY composer.phar /usr/local/bin/composer
RUN chmod +x /usr/local/bin/composer


# Chinese mirror
RUN /usr/local/bin/composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/

# Write Permission
RUN usermod -u 1000 www-data

# Create directory
RUN mkdir /docker/www -p
RUN mkdir /docker/log/php7 -p

RUN chown -R www-data.www-data /docker/www /docker/log/php7

RUN touch /docker/log/php7/php_errors.log && chmod 777 /docker/log/php7/php_errors.log

CMD ["php-fpm"]
