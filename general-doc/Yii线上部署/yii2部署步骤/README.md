# Yii2 线上部署 #

以NMS为例，其余同理。

## 新建yii工程 ##

克隆yii官方模板
```
$ git clone https://github.com/yiisoft/yii2-app-advanced.git
```
初始化PHP
```
$ php init
```
此时yii2-app-advanced内包含了未被纳入版本控制的本地文件。

## 加入版本控制 ##

克隆工程文件
```
$ git clone git@git.oschina.net:FogVDN/Operation-NMS.git
```
拷贝工程至已初始化的官方模板内
```
$ cp -rf Operation-NMS/. yii2-app-advanced
```
重命名
```
$ mv yii2-app-advanced nms
```
此时nms为工程目录，同级的Operation-NMS可删除

## 修改工程配置文件 ##

需修改的配置文件有三处：

配置数据库

common/config/main-local.php
```php
<?php
return [
    'components' => [
        'db' => [
            'dsn' => 'mysql:host=571f3fb81b3da.sh.cdb.myqcloud.com;port=6020;dbname=fogvdn',
            'username' => 'pear_root',
            'password' => 'its4pear',
        ],
    ],
];
```

配置访问路径

frontend/config/main.php
```php
<?php
return [
    'homeUrl' => '/',
    'components' => [
        'request' => [
            'baseUrl' => '',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
    ],
];
```

backend/config/main.php
```php
<?php
return [
    'homeUrl' => '/admin',
    'components' => [
        'request' => [
            'baseUrl' => '/admin',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
    ],
];
```

## 参考Nginx配置 ##

/etc/nginx/sites-enabled 下新建配置文件：

nms.webrtc.win.conf
```conf
server {
    listen 0.0.0.0:80;
    listen [::]:80;

    server_name nms.webrtc.win;
    server_tokens off; ## Don't show the nginx version number, a security best practice
    root /var/www/nms;

    location / {
        root /var/www/nms/frontend/web;
        try_files  $uri /frontend/web/index.php?$args;
    }

    location /admin {
        alias  /var/www/nms/backend/web;
        try_files  $uri /backend/web/index.php?$args;

        location ~* ^/admin/(.+\.php)$ {
            try_files  $uri /backend/web/$1?$args;
        }
    }

    location ~ \.php$ {
        # include snippets/fastcgi-php.conf;
        # # With php7.0-cgi alone:
        # fastcgi_pass 127.0.0.1:9000;
        # # With php7.0-fpm:
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_pass unix:/run/php/php7.0-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
    }
}
server {
    listen 0.0.0.0:443  ssl;
    listen [::]:443  ssl;

    server_name nms.webrtc.win;
    server_tokens off; ## Don't show the nginx version number, a security best practice
    root /var/www/nms;

    ssl on;
    ssl_certificate /etc/nginx/ca_certs/webrtc.win/webrtc.win.crt;
    ssl_certificate_key /etc/nginx/ca_certs/webrtc.win/webrtc.win.key;
    ssl_client_certificate /etc/nginx/ca_certs/webrtc.win/webrtc.win.crt;
    ssl_ciphers 'ECDHE-RSA-AES256-GCM-SHA384:ECDHE-RSA-AES128-GCM-SHA256';
    ssl_protocols  TLSv1 TLSv1.1 TLSv1.2;
    ssl_prefer_server_ciphers on;
    ssl_session_cache  builtin:1000  shared:SSL:10m;
    ssl_session_timeout  5m;
    ssl_stapling on;
    ssl_stapling_verify on;

    location / {
        root /var/www/nms/frontend/web;
        try_files  $uri /frontend/web/index.php?$args;
    }

    location /admin {
        alias  /var/www/nms/backend/web;
        try_files  $uri /backend/web/index.php?$args;

        location ~* ^/admin/(.+\.php)$ {
            try_files  $uri /backend/web/$1?$args;
        }
    }

    location ~ \.php$ {
        # include snippets/fastcgi-php.conf;
        # # With php7.0-cgi alone:
        # fastcgi_pass 127.0.0.1:9000;
        # # With php7.0-fpm:
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_pass unix:/run/php/php7.0-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
    }
}
```

## PHP错误排查 ##

若缺少扩展：
```
$ apt-get install php-curl

$ apt-get install php-mysql

$ apt-get install php-gd

$ apt-get install php-mbstring
```
若提示如下：

> no input file specified.

换一台服务器重试吧 -_-