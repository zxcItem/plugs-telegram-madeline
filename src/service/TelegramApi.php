<?php

declare (strict_types=1);

namespace plugin\telegram\madeline\service;


use think\admin\Service;

/**
 * Telegram Api 接口服务
 * Class TelegramApi
 * @package plugin\telegram\madeline\service
 */
class TelegramApi extends Service
{

    /**
     * 发布集合媒体信息
     * @param $params
     * @param $token
     * @return mixed
     */
    public static function sendMediaGroup($params,$token)
    {
        $result = json_decode(http_post("https://api.telegram.org/bot{$token}/sendMediaGroup",$params),true);
        if ($result && $result['ok'] === true){
            return $result;
        }
    }

    /**
     * 获取文件的地址
     * @param $file_id
     * @param $token
     * @return mixed
     */
    public static function getFile($file_id,$token)
    {
        $result = json_decode(http_get("https://api.telegram.org/bot{$token}/getFile?file_id={$file_id}"),true);
        if ($result && $result['ok'] === true){
            return "https://api.telegram.org/file/bot{$token}/{$result['result']['file_path']}";
        }
    }

    /**
     * 获取频道的ID
     * @param $token
     * @param $channel_name
     * @return mixed
     */
    public static function getChat($token,$channel_name)
    {
        $result = json_decode(http_get("https://api.telegram.org/bot{$token}/getChat?chat_id={$channel_name}"),true);
        if ($result && $result['ok'] === true){
            return $result['result'];
        }
    }
}