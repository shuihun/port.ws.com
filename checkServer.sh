#!/bin/bash

runPath=/data0/web/port.ws.com
phpBinPath=/usr/local/webserver/php-71/bin

if [ "$1" = "restart" ];then
	ps -eaf |grep "httpServerHttpSqs.php" | grep -v "grep"| awk '{print $2}'|xargs kill -9
fi
if [ "$1" = "stop" ];then
	ps -eaf |grep "httpServerHttpSqs.php" | grep -v "grep"| awk '{print $2}'|xargs kill -9
	echo -e "\033[32m 关闭接收站长之家swoole服务成功 \033[0m"
	exit 0
fi
# httpServerHttpSqs服务
count=`ps -fe |grep "httpServerHttpSqs.php" | grep -v "grep" | grep "httpServerHttpSqs" | wc -l`
if [ $count -lt 1 ]; then
	ulimit -c unlimited
	$phpBinPath/php $runPath/httpServerHttpSqs.php
	sleep 3
	countnew=`ps -fe |grep "httpServerHttpSqs.php" | grep -v "grep" | grep "httpServerHttpSqs" | wc -l`
	if [ $countnew -lt 1 ]; then
		echo 'httpServerHttpSqs.php restart error'
		echo 'httpServerHttpSqs.php_'$(date +%Y-%m-%d_%H:%M:%S) >$runPath/log/restart_error.log
	else
		echo "httpServerHttpSqs.php restart success";
		echo 'httpServerHttpSqs.php_'$(date +%Y-%m-%d_%H:%M:%S) >$runPath/log/restart.log
	fi
fi