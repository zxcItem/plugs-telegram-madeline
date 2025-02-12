<?php

declare (strict_types=1);

namespace plugin\telegram\command;

use danog\MadelineProto\Exception;
use plugin\telegram\model\PluginTelegramAccount;
use plugin\telegram\service\ConfigService;
use plugin\telegram\service\MadelineProtoApi;
use think\admin\Command;
use think\console\Input;
use think\console\Output;


class BotActive extends Command
{

    /**
     * 指令参数配置
     * @return void
     */
    protected function configure()
    {
        $this->setName('plugin:telegram:BotActive');
        $this->setDescription('账号保活操作');
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
        [$total, $count] = [PluginTelegramAccount::mk()->count(), 0];
        foreach (PluginTelegramAccount::mk()->where('status',1)->field('account_id')->cursor() as $user) try {
            $this->queue->message($total, ++$count, "刷新账号 [{$user['account_id']}] 数据...");
            $bot_name = ConfigService::get('bot_name');
            MadelineProtoApi::toBotMessage($user['account_id'],$bot_name,'Hello Bot');
            $this->queue->message($total, $count, "刷新账号 [{$user['account_id']}] 数据成功", 1);
        } catch (\Exception $exception) {
            $this->queue->message($total, $count, "刷新账号 [{$user['account_id']}] 数据失败, {$exception->getMessage()}", 1);
        }
        $this->setQueueSuccess("此次共处理 {$total} 个刷新操作。");
    }
}