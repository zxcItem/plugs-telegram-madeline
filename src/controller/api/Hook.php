<?php

declare (strict_types=1);

namespace plugin\telegram\madeline\controller\api;

use plugin\telegram\madeline\model\PluginTelegramSourceForward;
use plugin\telegram\madeline\service\ConfigService;
use plugin\telegram\madeline\service\TelegramApi;
use think\admin\Controller;
use think\exception\HttpResponseException;

/**
 * Telegram API WebHook
 * @package plugin\telegram\madeline\controller\api
 */
class Hook extends Controller
{

    /**
     * 获取Telegram推送内容
     */
    public function hook()
    {
        try {
            // 获取请求的数据
            $result = $this->request->post('');
            // 判断是否包含 channel_post
            $channelPost = $result['channel_post'] ?? null;
            if ($channelPost && isset($channelPost['forward_from_message_id'], $channelPost['forward_from_chat'])) {
                // 提取消息 ID 和频道 ID
                $messageId = $channelPost['forward_from_message_id'];
                $channelId = $channelPost['forward_from_chat']['id'];
                // 获取媒体内容
                $mediaContent = $channelPost['photo'] ?? $channelPost['video'] ?? null;
                // 如果有媒体内容，更新记录
                if ($mediaContent) {
                    // 获取最后一个媒体文件 ID
                    $base64Image = '';
                    if (isset($channelPost['photo'])) $media = end($mediaContent)['file_id'] ?? '';
                    if (isset($channelPost['video'])) $media = $channelPost['video']['thumbnail']['file_id'];
                    $bot_token = ConfigService::get('bot_token');
                    $url =TelegramApi::getFile($media,$bot_token);
                    $imageData = file_get_contents($url);
                    if ($imageData !== false) {
                        $base64Image = "data:image/png;base64,".base64_encode($imageData);
                    }
                    // 更新数据库中的媒体内容
                    PluginTelegramSourceForward::mk()
                        ->where(['channel_id' => $channelId, 'message_id' => $messageId])
                        ->update(['new_message_id'=>$channelPost['message_id'],'media' => $media,'forward'=>1,'cover'=>$base64Image]);
                }
            }
            if ($channelPost && isset($channelPost['reply_to_message'])){
                if ($channelPost['text'] == '删除'){
                    $message_id = $channelPost['reply_to_message']['message_id'];
                    $grouped_id = PluginTelegramSourceForward::mk()->withoutField('cover')->where('new_message_id',$message_id)->value('grouped_id');
                    PluginTelegramSourceForward::mk()->where('grouped_id',$grouped_id)->delete();
                }
            }
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            trace_file($exception);
            $this->error($exception->getMessage());
        }
    }


}