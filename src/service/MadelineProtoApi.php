<?php


namespace plugin\telegram\service;

use danog\MadelineProto\API;
use danog\MadelineProto\Exception;
use think\admin\Service;

/**
 * MadelineProto接口
 * Class MadelineProtoApi
 * @package plugin\telegram\service
 */
class MadelineProtoApi extends Service
{

    /**
     * 初始化 MadelineProto
     * @param $account_id
     * @param null $settings
     * @return API
     * @throws Exception
     */
    public static function initMadelineProto($account_id,$settings = null)
    {
        $session_file = syspath() ."public/session_{$account_id}.madeline";
        $madeline = new API($session_file,$settings);
        $madeline->start();
        return $madeline;
    }


    /**
     * 获取频道最新消息
     * @param array $channel
     * @return array
     * @throws Exception
     */
    public static function ChannelNewMessage(array $channel)
    {
        $madeline = self::initMadelineProto($channel['account_id']);
        return $madeline->messages->getHistory([
            'peer'  => $channel['channel_id'],
            'limit' => $channel['limit_number']
        ]);
    }

    /**
     * 获取频道历史消息
     * @param array $channel
     * @return array
     * @throws Exception
     */
    public static function ChannelHistoryMessage(array $channel)
    {

        $madeline = self::initMadelineProto($channel['account_id']);
        return $madeline->messages->getHistory([
            'peer'      => $channel['channel_id'],
            'offset_id' => $channel['last_message_id'] - $channel['limit_number'],
            'limit'     => $channel['limit_number'],
        ]);
    }

    /**
     * 获取频道内容评论
     * @param array $channel
     * @return array
     * @throws Exception
     */
    public static function ChannelMessageComments(array $channel)
    {

        $madeline = self::initMadelineProto($channel['account_id']);
        return $madeline->messages->getReplies([
            'peer'        => $channel['channel_id'],
            'msg_id'      => $channel['message_id'],
            'limit'       => $channel['replies'],
        ]);
    }

    /**
     * 获取原始文件ID
     * @param $account_id
     * @param $media
     * @param $type
     * @return mixed|string
     * @throws Exception
     */
    public static function getMessageFileId($account_id,$media,$type)
    {
        $madeline = self::initMadelineProto($account_id);
        $botAPI = $madeline->MTProtoToBotAPI($media);
        if ($type == 1) {
            $last_photo = end($botAPI['photo']);
            $file_id = $last_photo['file_id'] ?? '';
        }
        if ($type == 2) $file_id = $botAPI['video']['file_id'] ?? '';
        return $file_id ?? '';
    }

    /**
     * 获取原始文件ID
     * @param $account_id
     * @param $media
     * @return array
     * @throws Exception
     */
    public static function getOriginalFileId($account_id,$media)
    {
        $madeline = self::initMadelineProto($account_id);
        return $madeline->MTProtoToBotAPI($media);
    }

    /**
     * 账号保活
     * @param $account_id
     * @param $bot_username
     * @param $message
     * @return array
     * @throws Exception
     */
    public static function toBotMessage($account_id,$bot_username,$message)
    {
        $madeline = self::initMadelineProto($account_id);
        return $madeline->messages->sendMessage([
            'peer'    => $bot_username,
            'message' => $message
        ]);
    }

    /**
     * 转发消息
     * 需要转发后获取全新的文件信息
     * @param int $account_id  用户账号chat_id
     * @param int|string $to_peer     目标对话或频道的 peer（目标频道或群组）。表示您希望将消息转发到哪里。
     * @param int|string $from_peer   消息来源的 peer（对话或频道）。这是一个必填参数，表示消息从哪个对话或频道转发。
     * @param array $ids       消息 ID 数组，表示您要转发的消息 ID。每条消息的 ID 都是唯一的，您需要指定这些 ID 才能进行转发。
     * @param bool $silent     是否发送消息时不触发通知。设为 true 时，目标客户端不会收到通知，适用于避免打扰的场景。
     * @param bool $background 是否在后台发送消息。设为 true 时，消息发送不会阻塞当前操作，适用于异步操作。
     * @return array
     * @throws Exception
     */
    public static function forwardMessages($account_id,$to_peer,$from_peer,$ids,bool $silent=false, bool $background=false)
    {
        $madeline = self::initMadelineProto($account_id);
        return $madeline->messages->forwardMessages([
            'to_peer'    => $to_peer,
            'from_peer'  => $from_peer,
            'id'         => $ids,
            'silent'     => $silent,
            'background' => $background
        ]);
    }
}