<?php


declare (strict_types=1);

namespace plugin\telegram\model;

use think\admin\Model;

/**
 * 模型抽象类
 * @class Abs
 * @package plugin\telegram\model
 */
abstract class Abs extends Model
{
    /**
     * 创建字段
     * @var string
     */
    protected $createTime = 'create_at';

    /**
     * 更新字段
     * @var string
     */
    protected $updateTime = 'update_at';



    /**
     * 字段属性处理
     * @param mixed $value
     * @return string
     */
    public function setExtraAttr($value): string
    {
        return is_string($value) ? $value : json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 字段属性处理
     * @param mixed $value
     * @return array
     */
    public function getExtraAttr($value): array
    {
        return empty($value) ? [] : (is_string($value) ? json_decode($value, true) : $value);
    }
}