<?php

declare (strict_types=1);

namespace plugin\telegram\madeline\model;

/**
 * 账号模型
 * Class PluginTelegramAccount
 * @package plugin\telegram\madeline\model
 */
class PluginTelegramAccount extends Abs
{

    /**
     * 根据Id获取账号信息
     * @param $account_id
     * @param string $field
     * @return mixed
     */
    public static function getTelegramId($account_id,$field = '*')
    {
        return self::mk()->where('account_id',$account_id)->field($field)->find()->toArray();
    }

    /**
     * 获取指定字段账号信息
     * @param string $column
     * @return array
     */
    public static function getAccount($column = '*')
    {
        return self::mk()->column($column);
    }
}