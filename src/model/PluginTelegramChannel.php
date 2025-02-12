<?php

declare (strict_types=1);

namespace plugin\telegram\model;

/**
 * 经营频道模型
 * Class PluginTelegramChannelSource
 * @package plugin\telegram\model
 */
class PluginTelegramChannel extends Abs
{

    /**
     * 关联账号
     * @return \think\model\relation\HasOne
     */
    public function account()
    {
        return $this->hasOne(PluginTelegramAccount::class,'account_id','account_id');
    }

    /**
     * 获取指定字段账号信息
     * @param string $column
     * @return array
     */
    public static function getChannel($column = '*')
    {
        return self::mk()->column($column);
    }
}