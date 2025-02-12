<?php

declare (strict_types=1);

namespace plugin\telegram\service;

use think\admin\Service;
use think\cache\driver\Redis;

/**
 * redis
 * Class RedisService
 * @package plugin\telegram\service
 */
class RedisService extends Service
{
    protected $redis;

    /**
     *初始化
     */
    protected function initialize()
    {
        $this->redis = new Redis(config('cache.stores.redis'));
    }

    /**
     * 保存
     * @param $key
     * @param $value
     * @param int $expiry
     */
    public function set($key, $value, $expiry = 0)
    {
        $this->redis->set($key, $value, $expiry);
    }

    /**
     * 获取
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->redis->get($key);
    }
}