<?php

declare (strict_types=1);

namespace plugin\telegram\madeline\service;

use plugin\telegram\madeline\model\PluginTelegramBaseKeyword;
use think\admin\Service;

/**
 * 基础
 * Class DataService
 * @package plugin\telegram\madeline\service
 */
class DataService extends Service
{

    /**
     * 过滤敏感词
     * @param $message
     * @return bool
     */
    public static function checkKeyword($message)
    {
        if ($message){
            $keys = self::instance()->app->cache->get('telegram_sensitive_keywords');
            if (!$keys) {
                $keys = PluginTelegramBaseKeyword::keyword();
                self::instance()->app->cache->set('telegram_sensitive_keywords', $keys);
            }
            $escapedKeys = array_map('preg_quote', $keys);
            $pattern = '/\b(' . implode('|', $escapedKeys) . ')\b/iu'; // `\b` 确保匹配完整单词

            return preg_match($pattern, $message) === 1;
        }
        return false;
    }
}