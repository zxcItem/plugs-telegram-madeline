<?php

declare (strict_types=1);

namespace plugin\telegram\command;

use plugin\telegram\model\PluginTelegramChannelContent;
use plugin\telegram\model\PluginTelegramChannelSource;
use plugin\telegram\service\MadelineProtoApi;
use plugin\telegram\service\TelegramMessage;
use think\admin\Command;
use think\console\Input;
use think\console\Output;

/**
 * 频道采集
 * @class Clear
 * @package plugin\telegram\command
 */
class Channel extends Command
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
        $this->setName('plugin:telegram:channel');
        $this->setDescription('采集频道内容');
    }

    /**
     * 业务指令执行
     * @param Input $input
     * @param Output $output
     * @return void
     * @throws \think\admin\Exception
     */
    protected function execute(Input $input, Output $output)
    {
        $this->channel = PluginTelegramChannelSource::getChannelId(
            $this->queue->data['id'],
            'account_id,channel_id,channel_type,limit_number,last_message_id,release_channel_id'
        );
        if ($this->channel['channel_type']){
            $contentMessage = $this->_autoChannelHistory();
        }else{
            $contentMessage = $this->_autoChannelNew();
        }
        $this->setQueueSuccess("此次共采集【{$contentMessage}】条媒体数据");
    }

    /**
     * 获取最新频道数据
     * @return int
     * @throws \think\admin\Exception
     */
    private function _autoChannelNew()
    {
        try {
            $response = MadelineProtoApi::ChannelNewMessage($this->channel);
            $contentMessage = TelegramMessage::processMessages($this->channel,$response);
            if ($contentMessage){
                $last_message = end($contentMessage);
                $offset_id = $last_message['message_id'];
                $this->_saveContentMedia($contentMessage,$offset_id);
            }
            return count($contentMessage);
        } catch (\Exception $exception) {
            $this->setQueueError($exception->getMessage());
        }
    }

    /**
     * 获取历史频道数据
     * @return int
     * @throws \think\admin\Exception
     */
    private function _autoChannelHistory()
    {
        try {
            $response = MadelineProtoApi::ChannelHistoryMessage($this->channel);
            $contentMessage = TelegramMessage::processMessages($this->channel,$response);
            $offset_id = $this->channel['last_message_id'] + $this->channel['limit_number'];
            $this->_saveContentMedia($contentMessage,$offset_id);
            return count($contentMessage);
        } catch (\Exception $exception) {
            $this->setQueueError($exception->getMessage());
        }
    }

    /**
     * 更新采集内容
     * @param $contentMessage
     * @param $offset_id
     */
    private function _saveContentMedia($contentMessage,$offset_id)
    {
        $this->app->db->transaction(function () use ($contentMessage,$offset_id) {
            if ($contentMessage){
                PluginTelegramChannelContent::mk()->saveAll($contentMessage);
                PluginTelegramChannelSource::mk()->where('channel_id',$this->channel['channel_id'])->update(['last_message_id'=>$offset_id]);
            }
        });
    }
}