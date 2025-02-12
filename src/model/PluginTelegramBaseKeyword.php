<?php

declare (strict_types=1);

namespace plugin\telegram\madeline\model;

/**
 * 敏感词管理
 * Class PluginTelegramBaseKeyword
 * @package plugin\telegram\madeline\model
 */
class PluginTelegramBaseKeyword extends Abs
{

    /**
     * 获取全部敏感词
     * @return array
     */
    public static function keyword()
    {
        return self::mk()->where('status',1)->column('name');
    }
}