<?php

namespace plugin\telegram\madeline\service;

use danog\MadelineProto\Exception;
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
                if (isset($message['_']) && ($message['_'] === 'messageService' || isset($message['fwd_from']))) {
                    continue;  // 跳过符合条件的消息
                }
                $mediaMessage = [];
                if (isset($message['media']) && !empty($message['media'])) {
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
        $grouped_id = isset($message['grouped_id']) ? $message['grouped_id'] : CodeExtend::uniqidNumber(17);
        return [
            'channel_id'   => $message['peer_id'],
            'account_id'   => $channel['account_id'],
            'message_id'   => $message['id'],
            'grouped_id'   => $grouped_id,
            'caption'      => $message['message'] ?? '',
            'date'         => $message['date'] ?? 0,
        ];
    }
}