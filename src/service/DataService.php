<?php

declare (strict_types=1);

namespace plugin\telegram\service;

use plugin\telegram\model\PluginTelegramBaseKeyword;
use plugin\telegram\model\PluginTelegramChannelContent;
use think\admin\Service;

/**
 * 基础
 * Class DataService
 * @package plugin\telegram\service
 */
class DataService extends Service
{

    /**
     * 过滤敏感词
     * @param $message
     * @return bool
     */
    public static function checkKeyword($message)
    {
        if ($message){
            $keys = self::instance()->app->cache->get('telegram_sensitive_keywords');
            if (!$keys) {
                $keys = PluginTelegramBaseKeyword::keyword();
                self::instance()->app->cache->set('telegram_sensitive_keywords', $keys);
            }
            $escapedKeys = array_map('preg_quote', $keys);
            $pattern = '/\b(' . implode('|', $escapedKeys) . ')\b/iu'; // `\b` 确保匹配完整单词

            return preg_match($pattern, $message) === 1;
        }
        return false;
    }

    /**
     * 发布内容后更新message_id
     * @param $comment_id
     * @param $result
     * @throws \danog\MadelineProto\Exception
     */
    public static function updateMessageId($comment_id,$result)
    {
        $channel = PluginTelegramChannelContent::mk()->with(['release'=>function($release){
            $release->field('account_id,channel_id,group_id');
        }])->where('id',3)->field('release_channel_id')->find()->toArray();
        $response = MadelineProtoApi::ChannelNewMessage([
            'account_id'   => $channel['release']['account_id'],
            'channel_id'   => $channel['release']['group_id'],
            'limit_number' => count($result)+1
        ]);
        $message_id = $result[0]['message_id'];
        $group_message_id = self::getGroupMessageId($response['messages'],$message_id);
        PluginTelegramChannelContent::mk()
            ->where('id',$comment_id)
            ->update([
                'now_message_id'   => $message_id,
                'status'           => 1,
                'group_message_id' => $group_message_id
            ]);
    }

    /**
     * 更新发布后同步到群组的消息ID
     * @param $results
     * @param $message_id
     * @return int
     */
    public static function getGroupMessageId($results,$message_id)
    {
        $group_message_id = 0;
        foreach ($results as $result) {
            if (isset($result['fwd_from']['channel_post']) && $result['fwd_from']['channel_post'] === $message_id) {
                $group_message_id = $result['id'];
                break;
            }
        }
        return $group_message_id;
    }
}