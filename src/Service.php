<?php

declare (strict_types=1);

namespace plugin\telegram\madeline;

use plugin\telegram\madeline\command\Channel;
use plugin\telegram\madeline\command\Preview;
use think\admin\Plugin;

/**
 * 组件注册服务
 * @class Service
 * @package plugin\telegram\madeline
 */
class Service extends Plugin
{
    /**
     * 定义插件名称
     * @var string
     */
    protected $appName = 'MadelineProto服务';

    /**
     * 定义安装包名
     * @var string
     */
    protected $package = 'xiaochao/plugs-telegram-madeline';

    /**
     * 插件服务注册
     * @return void
     */
    public function register(): void
    {
        $this->commands([Channel::class,Preview::class]);
    }

    /**
     * 增加Telegram服务配置
     * @return array[]
     */
    public static function menu(): array
    {
        $code = self::getAppCode();
        // 设置插件菜单
        return [
            [
                'name' => '账号资源',
                'subs' => [
                    ['name' => '敏感词汇管理', 'icon' => 'iconfont iconfont-warn_light', 'node' => "{$code}/keyword/index"],
                    ['name' => '账号数据管理', 'icon' => 'layui-icon layui-icon-user', 'node' => "{$code}/account/index"],
                    ['name' => '网络素材频道', 'icon' => 'iconfont iconfont-similar', 'node' => "{$code}/source/index"],
                    ['name' => '网络素材资源', 'icon' => 'layui-icon layui-icon-read', 'node' => "{$code}/content/index"],
                ],
            ],
        ];
    }
}