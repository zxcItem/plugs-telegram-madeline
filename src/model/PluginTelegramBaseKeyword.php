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