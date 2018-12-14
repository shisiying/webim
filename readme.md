# 基于laravel+swoole的在线聊天室


## 目录

- [介绍](#introduction)
- [技术栈](#versions)
- [安装](#installation)
- [配置](#configuration)
- [启动](#run)

## Introduction

想法参考了这个[项目](https://github.com/hellosee/swoole-webim-demo)这个项目没有任何框架直接使用扩展来开发，并且以文本形式存储用户数据，
本项目参考了其前端页面样式以及部分逻辑使用laravel以及使用laravel-swoole的package [laravel-x](https://github.com/hhxsv5/laravel-s)
重构了整个项目，其中使用了swoole的http_server,swoole_websocket_server，异步任务，和swoole_table。

- 用户数据使用了swoole_table进行存储，一旦重启所有数据都消失了
- 所有ws请求都是用异步任务进行处理
- 使用http_server代替nginx
- 使用swoole_websocket_server搭建ws服务

主要代码在以下几个目录

- app/ChatTask 异步任务
- app/Services wbsocket服务处理
- public 静态资源目录


以上知识点均可在[swoole](https://www.swoole.com/)官网进行查看

**目前实现的功能有** ：

> * 支持群聊
> * 支持发送文字
> * 支持登出
> * 支持切换房间聊天功能
> * 支持私聊，可以点点私聊 
> * 显示未读消息数
> * 接入server酱，使用的是我开发的[sevenshi-serverchan](https://github.com/shisiying/sevenshi-serverchan)，订阅过后可以收到群聊的推送
 
你可以直接看一下最终的效果，请查看 [http://webim.xhzyxed.cn/](http://webim.xhzyxed.cn/) 。

如果想看看代码原理，请请查看三篇文章：

# Versions
> Laravel 5.5
> swoole
 
因为使用 `swoole`扩展 所以需要一些基本的安装需求：

* [swoole扩展的安装](https://www.swoole.com/)
 
## installation

```bash
    git clone https://github.com/shisiying/webim
    composer install
    php artisan laravels  publish 
    选择laravel-s和sevenshi-serverchan进行发布

```

## configuration

在config路径下


- laravels.php
```bash
<?php
/**
 * @see https://github.com/hhxsv5/laravel-s/blob/master/Settings-CN.md  Chinese
 * @see https://github.com/hhxsv5/laravel-s/blob/master/Settings.md  English
 */
return [
    'listen_ip'                => env('LARAVELS_LISTEN_IP', '0.0.0.0'),
    'listen_port'              => env('LARAVELS_LISTEN_PORT', 你的端口),
    'socket_type'              => env('LARAVELS_SOCKET_TYPE', defined('SWOOLE_SOCK_TCP') ? \SWOOLE_SOCK_TCP : 1),
    'enable_gzip'              => env('LARAVELS_ENABLE_GZIP', false),
    'enable_coroutine_runtime' => false,
    'server'                   => env('LARAVELS_SERVER', 'LaravelS'),
    'handle_static'            => env('LARAVELS_HANDLE_STATIC', true),
    'laravel_base_path'        => env('LARAVEL_BASE_PATH', base_path()),
    'inotify_reload'           => [
        'enable'        => env('LARAVELS_INOTIFY_RELOAD', false),
        'watch_path'    => base_path(),
        'file_types'    => ['.php'],
        'excluded_dirs' => [],
        'log'           => true,
    ],
    'websocket'                => [
        'enable' => true,
        'handler' => \App\Services\WebSocketService::class,
    ],
    'sockets'                  => [
    ],
    'processes'                => [
    ],
    'timer'                    => [
        'enable' => false,
        'jobs'   => [
            // Enable LaravelScheduleJob to run `php artisan schedule:run` every 1 minute, replace Linux Crontab
            //\Hhxsv5\LaravelS\Illuminate\LaravelScheduleJob::class,
            // Two ways to configure parameters:
            // [\App\Jobs\XxxCronJob::class, [1000, true]], // Pass in parameters when registering
            // \App\Jobs\XxxCronJob::class, // Override the corresponding method to return the configuration
        ],
    ],
    'events'                   => [
    ],
    'swoole_tables'            => [
        //定义登陆用户的信息
        'ws_users'          =>[
            'size'   => 102400,//Table的最大行数
            'column' => [// Table的列定义$roomid,$fd,$name,$email,$avatar
                ['name' => 'roomid', 'type' => \swoole_table::TYPE_STRING, 'size' => 8],
                ['name' => 'fd', 'type' => \swoole_table::TYPE_INT, 'size' => 8],
                ['name' => 'name', 'type' => \swoole_table::TYPE_STRING, 'size' => 100],
                ['name' => 'email', 'type' => \swoole_table::TYPE_STRING, 'size' => 100],
                ['name' => 'avatar', 'type' => \swoole_table::TYPE_STRING, 'size' => 100],
                ['name' => 'time', 'type' => \swoole_table::TYPE_STRING, 'size' => 50],
            ]
        ],
        'ws_rooms'=>[
            'size'   => 102400,//Table的最大行数
            'column' => [// Table的列定义$roomid,$fd,$name,$email,$avatar
                ['name' => 'users', 'type' => \swoole_table::TYPE_STRING, 'size' => 1024],
            ]
        ],
        'ws_roomUsers'=>[
            'size'   => 102400,//Table的最大行数
            'column' => [// Table的列定义$roomid,$fd,$name,$email,$avatar
                ['name' => 'infos', 'type' => \swoole_table::TYPE_STRING, 'size' => 1024],
            ]
        ],
    ],
    'register_providers'       => [
    ],
    'swoole'                   => [
        'daemonize'          => env('LARAVELS_DAEMONIZE', false),
        'dispatch_mode'      => 2,
        'reactor_num'        => function_exists('\swoole_cpu_num') ? \swoole_cpu_num() * 2 : 4,
        'worker_num'         => function_exists('\swoole_cpu_num') ? \swoole_cpu_num() * 2 : 8,
        'task_worker_num'   => function_exists('\swoole_cpu_num') ? \swoole_cpu_num() * 2 : 8,
        'task_ipc_mode'      => 1,
        'task_max_request'   => 5000,
        'task_tmpdir'        => @is_writable('/dev/shm/') ? '/dev/shm' : '/tmp',
        'message_queue_key'  => ftok(base_path('public/index.php'), 1),
        'max_request'        => 3000,
        'open_tcp_nodelay'   => true,
        'pid_file'           => storage_path('laravels.pid'),
        'log_file'           => storage_path(sprintf('logs/swoole-%s.log', date('Y-m'))),
        'log_level'          => 4,
        'document_root'      => base_path('public'),
        'buffer_output_size' => 16 * 1024 * 1024,
        'socket_buffer_size' => 128 * 1024 * 1024,
        'package_max_length' => 4 * 1024 * 1024,
        'reload_async'       => true,
        'max_wait_time'      => 60,
        'enable_reuse_port'  => true,
        'enable_coroutine'   => false,

        /**
         * More settings of Swoole
         * @see https://wiki.swoole.com/wiki/page/274.html  Chinese
         * @see https://www.swoole.co.uk/docs/modules/swoole-server/configuration  English
         */
    ],
];


```

记住，websocket_server跟http_server监听同个端口，只不过协议不同而已

- chat.php
```bash
define("DOMAIN","域名或者刚才设置的ip和端口");
```

- serverchan.php

```bash

设置推送给群组的server配置
return [
    'a'=>'6739-83848e2xxxxdfd5a74b30aca15d71ec5e961', //sw群组
    'b'=>'6741-7c0b0355xxx85e7e2e86cc6edcdbc7bd',//php群组
    'c'=>'6740-023ed322xxxxfds9c6e3d0e4b268a762622a'//go群组
];
```


- init.js

配置public目录下的init.js
```bash
var config = {
	'domain' : "http://192.168.10.10:9090",//你的ip和端口，也就是laravel-s上设置的
	'wsserver' : "ws://192.168.10.10:9090",//除了协议不同，其他ip跟端口是一致的
}
```

- nginx配置

可参考以下配置
```bash
map $http_upgrade $connection_upgrade {
    default upgrade;
    ''      close;
}
upstream laravels {
    # 通过 IP:Port 连接
    server 127.0.0.1:5200 weight=5 max_fails=3 fail_timeout=30s;
    # 通过 UnixSocket Stream 连接，小诀窍：将socket文件放在/dev/shm目录下，可获得更好的性能
    #server unix:/xxxpath/laravel-s-test/storage/laravels.sock weight=5 max_fails=3 fail_timeout=30s;
    #server 192.168.1.1:5200 weight=3 max_fails=3 fail_timeout=30s;
    #server 192.168.1.2:5200 backup;
    keepalive 16;
}
server {
    listen 80;
    # 别忘了绑Host哟
    server_name laravels.com;
    root /xxxpath/laravel-s-test/public;
    access_log /yyypath/log/nginx/$server_name.access.log  main;
    autoindex off;
    index index.html index.htm;
    # Nginx处理静态资源(建议开启gzip)，LaravelS处理动态资源。
    location / {
        try_files $uri @laravels;
    }
    # 当请求PHP文件时直接响应404，防止暴露public/*.php
    #location ~* \.php$ {
    #    return 404;
    #}
    # Http和WebSocket共存，Nginx通过location区分
    # !!! WebSocket连接时路径为/ws
    # Javascript: var ws = new WebSocket("ws://laravels.com/ws");
    location =/ws {
        proxy_http_version 1.1;
        # proxy_connect_timeout 60s;
        # proxy_send_timeout 60s;
        # proxy_read_timeout：如果60秒内被代理的服务器没有响应数据给Nginx，那么Nginx会关闭当前连接；同时，Swoole的心跳设置也会影响连接的关闭
        # proxy_read_timeout 60s;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Real-PORT $remote_port;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header Host $http_host;
        proxy_set_header Scheme $scheme;
        proxy_set_header Server-Protocol $server_protocol;
        proxy_set_header Server-Name $server_name;
        proxy_set_header Server-Addr $server_addr;
        proxy_set_header Server-Port $server_port;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection $connection_upgrade;
        proxy_pass http://laravels;
    }
    location @laravels {
        proxy_http_version 1.1;
        # proxy_connect_timeout 60s;
        # proxy_send_timeout 60s;
        # proxy_read_timeout 60s;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Real-PORT $remote_port;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header Host $http_host;
        proxy_set_header Scheme $scheme;
        proxy_set_header Server-Protocol $server_protocol;
        proxy_set_header Server-Name $server_name;
        proxy_set_header Server-Addr $server_addr;
        proxy_set_header Server-Port $server_port;
        proxy_set_header Connection "";
        proxy_pass http://laravels;
    }
}
```
## run
```bash

直接项目根目录下执行,加d可以守护态运行

php artisan laravels start -d 


```


## License

The MIT License (MIT).







