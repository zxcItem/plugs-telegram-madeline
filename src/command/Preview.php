<?php

declare (strict_types=1);

namespace plugin\telegram\command;

use danog\MadelineProto\Exception;
use plugin\telegram\model\PluginTelegramChannelContent;
use plugin\telegram\service\ConfigService;
use plugin\telegram\service\MadelineProtoApi;
use think\admin\Command;
use think\console\Input;
use think\console\Output;


class Preview extends Command
{

    /**
     * 指令参数配置
     * @return void
     */
    protected function configure()
    {
        $this->setName('plugin:telegram:Preview');
        $this->setDescription('频道资源转发');
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
        try {
            $content = PluginTelegramChannelContent::mk()->withoutField('cover')->where(['forward'=>0])->with(['media'=>function($media){
                $media->field('grouped_id,message_id');
            }])->find()->toArray();
            if (isset($content['media'])) {
                $message = array_column($content['media'],'message_id');
                $forward_channel = ConfigService::get('forward_channel');
                MadelineProtoApi::forwardMessages($content['account_id'],$forward_channel,$content['channel_id'],$message,true,true);
                PluginTelegramChannelContent::mk()
                    ->where('channel_id',$content['channel_id'])->whereIn('message_id',$message)->update(['forward'=>1]);
                $this->setQueueSuccess("资源转发成功！");
            }
            $this->setQueueSuccess("暂无资源！");
        } catch (\Exception $exception) {
            $this->setQueueError($exception->getMessage());
        }
    }
}