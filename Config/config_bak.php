<?php

/**
 * 配置文件
 * 
 * User：陈金河
 * Date: 2019-04-12
 * 
 * 其中httpsqs访问链接 -如下链接
 * http://192.168.128.128:1218/?charset=utf-8&name=keyword&opt=status&check=123456
 */


defined('BASE_PATH') OR exit('No direct script access allowed');

$config = array(
    'swoole' => array(
        'server' => array(  //swoole服务端配置
            'host' => '0.0.0.0',
            'port' => 9501,
            'daemonize' => false,//是否开启守护进程
            'worker_num' => 2,
            'dispatch_mode' => 3, //数据包分发策略
            'task_worker_num' => 4,//开启task的个数，根据实际情况进行调整

            'task_ipc_mode' => 3,
            //设置task进程与worker进程之间通信的方式。
            //1, 使用unix socket通信，默认模式；
            //2, 使用消息队列通信；
            //3, 使用消息队列通信，并设置为争抢模式
            
            'open_tcp_nodelay' => true,  
            //开启后TCP连接发送数据时会关闭Nagle合并算法，立即发往客户端连接
            
            'log_file' => '/tmp/swoole.log'//日志存放
        ),
        'client' => array(  //swoole客户端配置
            'host' => '0.0.0.0.0',
            'port' => 9501
        )
    ),
    'redis' => array(
        'host' => '127.0.0.1',
        'port' => 6379,
    ),
    'httpsqs' => array(
        'host' => '127.0.0.1',
        'port' => 1218,
        'auth' => '123456'
    ),

);

return $config;