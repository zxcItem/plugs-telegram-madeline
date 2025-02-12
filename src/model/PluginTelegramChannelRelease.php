<?php

declare (strict_types=1);

namespace plugin\telegram\model;

/**
 * 频道发布内容管理
 * Class PluginTelegramChannelRelease
 * @package plugin\telegram\model
 */
class PluginTelegramChannelRelease extends Abs
{

    /**
     * 关联频道
     * @return \think\model\relation\HasOne
     */
    public function channel()
    {
        return $this->hasOne(PluginTelegramChannel::class, 'channel_id', 'channel_id');
    }
}