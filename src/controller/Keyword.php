<?php

declare (strict_types=1);

namespace plugin\telegram\controller;

use plugin\telegram\model\PluginTelegramBaseKeyword;
use think\admin\Controller;
use think\admin\helper\QueryHelper;


/**
 * 敏感词管理
 * Class Keyword
 * @package plugin\telegram\controller
 */
class Keyword extends Controller
{

    /**
     * 敏感词管理
     * @return void
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        PluginTelegramBaseKeyword::mQuery()->layTable(function () {
            $this->title = '敏感词管理';
        }, function (QueryHelper $query) {
            $query->like('name')->dateBetween('create_at');
        });
    }

    /**
     * 添加敏感词
     * @auth true
     */
    public function add()
    {
        PluginTelegramBaseKeyword::mForm('form');
    }

    /**
     * 编辑敏感词
     * @auth true
     */
    public function edit()
    {
        PluginTelegramBaseKeyword::mForm('form');
    }

    /**
     * 修改敏感词状态
     * @auth true
     */
    public function state()
    {
        PluginTelegramBaseKeyword::mSave($this->_vali([
            'status.in:0,1'  => '状态值范围异常！',
            'status.require' => '状态值不能为空！',
        ]));
    }


    /**
     * 删除敏感词
     * @auth true
     */
    public function remove()
    {
        PluginTelegramBaseKeyword::mDelete();
    }

}