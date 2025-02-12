<?php

declare (strict_types=1);

namespace plugin\telegram\command;

use danog\MadelineProto\Exception;
use plugin\telegram\model\PluginTelegramChannelContent;
use plugin\telegram\service\MadelineProtoApi;
use plugin\telegram\service\TelegramMessage;
use think\admin\Command;
use think\console\Input;
use think\console\Output;

/**
 * 评论区采集
 * @class Clear
 * @package plugin\telegram\command
 */
class Comment extends Command
{
    /**
     *  当前频道
     * @var array
     */
    protected $channel;

    /**
     * 指令参数配置
     * @return void
     */
    protected function configure()
    {
        $this->setName('plugin:telegram:comment');
        $this->setDescription('采集频道评论区');
    }

    /**
     * 业务指令执行
     * @param Input $input
     * @param Output $output
     * @return void
     * @throws \think\admin\Exception|Exception
     */
    protected function execute(Input $input, Output $output)
    {
        $comment = PluginTelegramChannelContent::mk()->where(['is_replies'=>1,'replies_status'=>0])->find()->toArray();
        $response = MadelineProtoApi::ChannelMessageComments([
            'account_id' => $comment['account_id'],
            'channel_id' => $comment['channel_id'],
            'message_id' => $comment['message_id'],
            'replies'    => $comment['replies']
        ]);
        if (!isset($response['messages'])) $this->setQueueSuccess("刷新来源频道内容评论ID [{$comment['message_id']}] 失败。");
        $contentMessage = TelegramMessage::processMessages($comment,$response);
        $this->app->db->transaction(function () use ($contentMessage,$comment) {
            if ($contentMessage){
                foreach ($contentMessage as &$message) {
                    $message['message_id'] = $comment['message_id'];
                    $message['channel_id'] = $comment['channel_id'];
                    $message['cate'] = 1;
                }
                PluginTelegramChannelContent::mk()->saveAll($contentMessage);
                PluginTelegramChannelContent::mk()->where('id',$comment['id'])->update(['replies_status'=>1]);
            }
        });
        $this->setQueueSuccess("此次共处理 {$response['count']} 个评论操作。");
    }

}