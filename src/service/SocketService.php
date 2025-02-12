<?php

namespace plugin\telegram\service;

use Exception;
use GatewayWorker\Lib\Gateway;
use think\admin\Service;

class SocketService extends Service
{
    /**
     * 广播信息
     * @param mixed $message 消息
     * @throws Exception
     */
    public static function sendToAll($message)
    {
        Gateway::sendToAll(json_encode($message,JSON_UNESCAPED_UNICODE));
    }
}