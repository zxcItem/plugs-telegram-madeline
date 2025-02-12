<?php

namespace plugin\telegram\service;

use danog\MadelineProto\Exception;
use think\admin\extend\CodeExtend;
use think\admin\Service;

/**
 * 频道消息管理
 * 定时任务使用消息类
 * Class TelegramMessage
 * @package plugin\telegram\service
 */
class TelegramMessage extends Service
{

    /**
     * 处理频道信息
     * @param $channel
     * @param $response
     * @return array
     * @throws Exception
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
                // if (DataService::checkKeyword($message['message'])) continue;
                $mediaMessage = [];
                if (isset($message['media']) && !empty($message['media'])) {
                    if (!in_array($message['media']['_'], ['messageMediaPhoto', 'messageMediaDocument'])) continue;
                    $type ='';
                    if ($message['media']['_'] == 'messageMediaPhoto'){
                        if (isset($message['media']['photo'])){
                            $type = 'photo';
                        }
                    }elseif ($message['media']['_'] == 'messageMediaDocument'){
                        if ($message['media']['document']['mime_type'] == 'video/mp4'){
                            $type = 'video/mp4';
                        }
                    }
                    $mediaMessage = self::messageMediaDocument($message,$channel,$type);
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
    public static function messageMediaDocument($message,$channel,$type)
    {
        $is_replies = isset($message['replies']) ? $message['replies']['replies'] : 0;
        $grouped_id = isset($message['grouped_id']) ? $message['grouped_id'] : CodeExtend::uniqidNumber(17);
        return [
            'channel_id'   => $message['peer_id'],
            'account_id'   => $channel['account_id'],
            'message_id'   => $message['id'],
            'grouped_id'   => $grouped_id,
            'caption'      => $message['message'] ?? '',
            'type'         => $type,
            'date'         => $message['date'] ?? 0,
            'is_replies'   => $is_replies ? 1 : 0,
            'replies'      => $is_replies
        ];
    }
}