<?php

declare (strict_types=1);

namespace plugin\telegram\model;

/**
 * 频道发布内容分类
 * Class PluginTelegramReleaseType
 * @package plugin\telegram\model
 */
class PluginTelegramReleaseType extends Abs
{

    /**
     * 获取分类信息
     * @param string $column
     * @return mixed
     */
    public static function getType($column = '*')
    {
        return self::mk()->order('sort desc')->column($column);
    }
}