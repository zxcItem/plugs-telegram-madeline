<?php

declare (strict_types=1);

namespace plugin\telegram\madeline\service;

use think\admin\Exception;

/**
 * Telegram配置服务
 * @class ConfigService
 * @package plugin\telegram\madeline\service
 */
class ConfigService
{

    /**
     * 配置缓存名
     * @var string
     */
    private static $skey = 'plugin.telegram.config';


    /**
     * 读取配置参数
     * @param string|null $name
     * @param $default
     * @return array|mixed|null
     * @throws Exception
     */
    public static function get(?string $name = null, $default = null)
    {
        $syscfg = sysvar(self::$skey) ?: sysvar(self::$skey, sysdata(self::$skey));
        return is_null($name) ? $syscfg : ($syscfg[$name] ?? $default);
    }

    /**
     * 配置参数
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    public static function set(array $data)
    {
        return sysdata(self::$skey, $data);
    }
}