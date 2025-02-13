<?php

declare (strict_types=1);

namespace plugin\telegram\madeline\command;

use danog\MadelineProto\Exception;
use plugin\telegram\madeline\model\PluginTelegramSourceForward;
use plugin\telegram\madeline\service\ConfigService;
use plugin\telegram\madeline\service\MadelineProtoApi;
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
            $content = PluginTelegramSourceForward::mk()->withoutField('cover')->where(['forward'=>0])->with(['media'=>function($media){
                $media->field('grouped_id,message_id');
            }])->find()->toArray();
            if (isset($content['media'])) {
                $message = array_column($content['media'],'message_id');
                $forward_channel = ConfigService::get('channel')['forward'];
                MadelineProtoApi::forwardMessages($content['account_id'],$forward_channel,$content['channel_id'],$message,true,true);
                PluginTelegramSourceForward::mk()
                    ->where('channel_id',$content['channel_id'])->whereIn('message_id',$message)->update(['forward'=>1]);
            }
        } catch (\Exception $exception) {
            $this->setQueueError($exception->getMessage());
        }
        $this->setQueueSuccess("资源转发成功！");
    }
}