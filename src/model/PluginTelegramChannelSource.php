<?php

declare (strict_types=1);

namespace plugin\telegram\madeline\model;

/**
 * 目标频道模型
 * Class PluginTelegramChannelSource
 * @package plugin\telegram\madeline\model
 */
class PluginTelegramChannelSource extends Abs
{

    /**
     * 根据ID获取频道信息
     * @param $id
     * @param string $field
     * @return mixed
     */
    public static function getChannelId($id,$field = '*')
    {
        return self::mk()->where('id',$id)->field($field)->find()->toArray();
    }

    /**
     * 根据ID获取频道信息
     * @param $channel_id
     * @param string $field
     * @return mixed
     */
    public static function getChannel($channel_id,$field = '*')
    {
        return self::mk()->where('channel_id',$channel_id)->field($field)->find()->toArray();
    }

    /**
     * 关联账号
     * @return \think\model\relation\HasOne
     */
    public function account()
    {
        return $this->hasOne(PluginTelegramAccount::class, 'account_id', 'account_id');
    }
}