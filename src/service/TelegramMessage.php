<?php

namespace plugin\telegram\madeline\service;

use plugin\telegram\madeline\model\PluginTelegramBaseKeyword;
use think\admin\extend\CodeExtend;
use think\admin\Service;

/**
 * 频道消息管理
 * 定时任务使用消息类
 * Class TelegramMessage
 * @package plugin\telegram\madeline\service
 */
class TelegramMessage extends Service
{

    /**
     * 处理频道信息
     * @param $channel
     * @param $response
     * @return array
     */
    public static function processMessages($channel,$response)
    {
        if (isset($response['messages'])) {
            usort($response['messages'], function($a, $b) {return $a['id'] - $b['id'];});
            $contentMessage = [];
            foreach ($response['messages'] as $message) {
                if (isset($message['_']) && isset($message['entities']) && ($message['_'] === 'messageService')) {
                    continue;  // 跳过符合条件的消息
                }
                $message['grouped_id'] = isset($message['grouped_id']) ? $message['grouped_id'] : CodeExtend::uniqidNumber(17);
                if (self::redisCache($message['grouped_id'],$message['id'])) continue;
                if (self::instance()->checkKeyword($message['message'])) continue;
                $mediaMessage = [];
                if (isset($message['media'])) {
                    if (!in_array($message['media']['_'], ['messageMediaPhoto', 'messageMediaDocument'])) continue;
                    $mediaMessage = self::messageMediaDocument($message,$channel);
                }
                if ($mediaMessage) $contentMessage[] = $mediaMessage;
            }
            return $contentMessage;
        }
    }

    /**
     * 保存集合信息
     * @param $message
     * @param $channel
     * @param $type
     * @return array
     */
    public static function messageMediaDocument($message,$channel)
    {
        RedisService::instance()->set("GroupedId:{$message['grouped_id']}:{$message['id']}",$message['id'],3600*12*7);
        return [
            'channel_id'   => $message['peer_id'],
            'account_id'   => $channel['account_id'],
            'message_id'   => $message['id'],
            'grouped_id'   => $message['grouped_id'],
            'date'         => $message['date'] ?? 0,
        ];
    }

    /**
     * 过滤敏感词
     * @param $text
     * @return bool
     */
    public function checkKeyword($text)
    {
        if ($text){
            $text = strip_tags($text);
            $sensitiveWords = $this->app->cache->get('telegram_sensitive_keywords');
            if (!$sensitiveWords) {
                $sensitiveWords = PluginTelegramBaseKeyword::keyword();
                $this->app->cache->set('telegram_sensitive_keywords', $sensitiveWords,600);
            }
            foreach ($sensitiveWords as $word) {
                if (strpos($text, $word) !== false) {
                    return true; // 如果包含敏感词，则返回 true
                }
            }
        }
        return false;
    }

    /**
     * 检测是否已存在数据
     * @param $grouped_id
     * @param $message_id
     * @return bool
     */
    public static function redisCache($grouped_id,$message_id)
    {
        $redis = RedisService::instance()->get("GroupedId:{$grouped_id}:{$message_id}");
        if ($redis){
            return true;
        }
        return false;
    }
}