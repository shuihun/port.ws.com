<?php

/**
 * 基于swoole的http_server异步服务
 * http_server  + redis 
 * 接收post请求后，判断数据是否合法，存入redis队列keyword中
 * User：陈金河
 * Date: 2019-04-12
 * @
 */


define('BASE_PATH', dirname(__FILE__));

cli_set_process_title("php httpServerRedis.php: httpServerRedis");  // 设置程序的进程名

class httpServerRedis
{
    
    private $serv;
    private $debug = true;
    private $redis;

    /**
     * 架构函数
     */
    public function __construct()
    {
        $allConfig = require_once BASE_PATH.'/Config/config.php';

        $server_config = $allConfig['swoole']['server'];
        $redis_config = $allConfig['redis'];

        $this->serv = new swoole_http_server($server_config['host'], $server_config['port']);
        $this->serv->set($server_config);

        $this->redis = new \Redis();
        $this->redis->connect($redis_config['host'],$redis_config['port']);

        $this->serv->on('request', array($this, 'onRequest'));

        $this->serv->on('task', array($this, 'onTask'));
        $this->serv->on('Finish', array($this, 'onFinish'));
        $this->serv->start();
    }


    /**
     * 请求处理函数
     * @param  swoole_http_request  $request  [description]
     * @param  swoole_http_response $response [description]
     * @return [type]                         [description]
     */
    public function onRequest(swoole_http_request $request, swoole_http_response $response)
    {
        //请求过滤
        if($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico'){
            return $response->end();
        }   
        $taskId = isset($request->get['taskId']) ? $request->get['taskId']: ''; 
        if($taskId !== ''){
            //返回任务状态
            //$status = $redis->get($taskId);
            //return $response->end("task: $taskId;status: $status");
        }
        $jsonData = $request->post['jsonData'];  // post 请求变量
        if (!$jsonData) {
            $array = array("response_code"=>0,"sub_msg"=>"数据为空");
            return $response->end(json_encode($array));
        }
        $jsonArray = json_decode($jsonData,true);
        if(gettype($jsonArray) != 'array'){
            $array = array("response_code"=>0,"sub_msg"=>"数据格式必须为JSON格式");
            return $response->end(json_encode($array));
        }
        $taskId = $this->serv->task($jsonData);
        $array = array("response_code"=>1,"sub_msg"=>"数据提交成功");
        $response->end(json_encode($array));
    }


    /**
     * 异步任务处理中心
     * @param  [type] $serv    [description]
     * @param  [type] $task_id [description]
     * @param  [type] $from_id [description]
     * @param  [type] $data    [description]
     * @return [type]          [description]
     */
    public function onTask($serv, $task_id, $from_id, $data)
    {
        $this->redis->lpush('keyword',$data);
        return 1;//必须有return 否则不会调用onFinish
    }


    /**
     * 任务完成回调
     * @param  [type] $serv    [description]
     * @param  [type] $task_id [description]
     * @param  [type] $data    [onTask return回来的值]
     * @return [type]          [description]
     */
    public function onFinish($serv, $task_id, $data)
    {
        echo "Task {$task_id} finish\n";
        echo "Result: {$data}\n";
    }


}


$server = new httpServerRedis();
