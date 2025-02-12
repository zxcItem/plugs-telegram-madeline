<?php

declare (strict_types=1);

namespace plugin\telegram\controller;

use plugin\telegram\model\PluginTelegramReleaseType;
use think\admin\Controller;


/**
 * 内容分类
 * Class Type
 * @package plugin\telegram\controller
 */
class Type extends Controller
{

    /**
     * 内容分类
     * @return void
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        PluginTelegramReleaseType::mQuery()->layTable(function () {
            $this->title = '内容分类';
        });
    }

    /**
     * 添加分类
     * @auth true
     */
    public function add()
    {
        PluginTelegramReleaseType::mForm('form');
    }

    /**
     * 编辑分类
     * @auth true
     */
    public function edit()
    {
        PluginTelegramReleaseType::mForm('form');
    }

    /**
     * 修改分类状态
     * @auth true
     */
    public function state()
    {
        PluginTelegramReleaseType::mSave($this->_vali([
            'status.in:0,1'  => '状态值范围异常！',
            'status.require' => '状态值不能为空！',
        ]));
    }


    /**
     * 删除分类
     * @auth true
     */
    public function remove()
    {
        PluginTelegramReleaseType::mDelete();
    }

}