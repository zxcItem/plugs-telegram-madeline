<?php

declare (strict_types=1);

namespace plugin\telegram\service;

use plugin\telegram\model\PluginTelegramChannelContent;
use think\admin\Service;

/**
 * 发布频道内容
 * Class ReleaseContent
 * @package plugin\telegram\service
 */
class ReleaseContent extends Service
{

    /**
     * 处理发布内容
     * @param $content_id
     * @return mixed
     */
    public static function mediaData($content_id)
    {
        $data = PluginTelegramChannelContent::mk()->where('id',$content_id)
            ->with(['release'=>function($release){
                $release->with(['bot'])->field('channel_id,robot_id');
            },'media'=>function($media){
                $media->field('grouped_id,type,caption,media');
            }])->find()->toArray();
        $modifiedData = array_map(function($item) {
            unset($item['grouped_id']);
            return $item;
        }, $data['media']);
        return self::send($modifiedData,$data['release_channel_id'],$data['release']['bot']['bot_token']);
    }

    /**
     * 发送频道内容
     * @param $modifiedData
     * @param $chat_id
     * @param $token
     * @return mixed
     */
    public static function send($modifiedData,$chat_id,$token)
    {
        $params = [
            'chat_id' => $chat_id,
            'media'   => json_encode($modifiedData)
        ];
        return TelegramApi::sendMediaGroup($params,$token);
    }

    /**
     * 处理发布内容
     * @param $content
     * @return mixed
     */
    public static function contentData($content)
    {
        $params = [
            'chat_id' => $content['channel_id'],
            'media'   => $content['media']
        ];
        return TelegramApi::sendMediaGroup($params,$content['bot_token']);
    }
}