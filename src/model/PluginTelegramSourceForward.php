<?php

declare (strict_types=1);

namespace plugin\telegram\madeline\model;

/**
 * 目标频道内容
 * Class PluginTelegramSourceForward
 * @package plugin\telegram\madeline\model
 */
class PluginTelegramSourceForward extends Abs
{

    /**
     * 关联频道
     * @return \think\model\relation\HasOne
     */
    public function channel()
    {
        return $this->hasOne(PluginTelegramChannelSource::class, 'channel_id', 'channel_id');
    }

    /**
     * 关联采集账号
     * @return \think\model\relation\HasOne
     */
    public function account()
    {
        return $this->hasOne(PluginTelegramAccount::class, 'account_id', 'account_id');
    }

    /**
     * 关联集合内容
     * @return \think\model\relation\HasMany
     */
    public function media()
    {
        return $this->hasMany(PluginTelegramSourceForward::class, 'grouped_id', 'grouped_id');
    }

    /**
     * 格式化发送时间
     * @param mixed $value
     * @return string
     */
    public function getDateAttr($value): string
    {
        return format_datetime($value);
    }

}