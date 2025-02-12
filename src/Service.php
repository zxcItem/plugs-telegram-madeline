<?php

declare (strict_types=1);

namespace plugin\telegram;

use plugin\telegram\command\Channel;
use plugin\telegram\command\Comment;
use plugin\telegram\command\BotActive;
use plugin\telegram\command\Preview;
use think\admin\Plugin;

/**
 * 组件注册服务
 * @class Service
 * @package plugin\telegram
 */
class Service extends Plugin
{
    /**
     * 定义插件名称
     * @var string
     */
    protected $appName = 'Telegram服务';

    /**
     * 定义安装包名
     * @var string
     */
    protected $package = 'xiaochao/plugs-telegram';

    /**
     * 插件服务注册
     * @return void
     */
    public function register(): void
    {
        $this->commands([Channel::class,Comment::class,BotActive::class,Preview::class]);
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
                'name' => '属性配置',
                'subs' => [
                    ['name' => '参数属性管理', 'icon' => 'layui-icon layui-icon-set', 'node' => "{$code}/config/index"],
                    ['name' => '敏感词汇管理', 'icon' => 'iconfont iconfont-warn_light', 'node' => "{$code}/keyword/index"]
                ],
            ],
            [
                'name' => '账号频道',
                'subs' => [
                    ['name' => '账号数据管理', 'icon' => 'layui-icon layui-icon-user', 'node' => "{$code}/account/index"],
                    ['name' => '经营频道管理', 'icon' => 'iconfont iconfont-similar', 'node' => "{$code}/channel/index"],
                ],
            ],
            [
                'name' => '资源管理',
                'subs' => [
                    ['name' => '目标频道管理', 'icon' => 'iconfont iconfont-similar', 'node' => "{$code}/source/index"],
                    ['name' => '目标资源管理', 'icon' => 'layui-icon layui-icon-read', 'node' => "{$code}/content/index"],
                ],
            ],
            [
                'name' => '发布内容',
                'subs' => [
                    ['name' => '发布内容分类', 'icon' => 'iconfont iconfont-similar', 'node' => "{$code}/type/index"],
                    ['name' => '发布内容管理', 'icon' => 'layui-icon layui-icon-read', 'node' => "{$code}/release/index"],
                ],
            ],
        ];
    }
}