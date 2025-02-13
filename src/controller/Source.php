<?php

declare (strict_types=1);

namespace plugin\telegram\madeline\controller;

use plugin\telegram\madeline\model\PluginTelegramAccount;
use plugin\telegram\madeline\model\PluginTelegramChannelSource;
use think\admin\Controller;
use think\admin\helper\QueryHelper;


/**
 * 网络素材频道
 * Class Source
 * @package plugin\telegram\madeline\controller
 */
class Source extends Controller
{

    /**
     * 网络素材频道
     * @return void
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $this->type = $this->get['type'] ?? 'index';
        PluginTelegramChannelSource::mQuery()->layTable(function () {
            $this->title = '网络素材频道';
        }, function (QueryHelper $query) {
            $query->with(['account'=>function($account){
                $account->field('title,account_id');
            }])->like('channel_name,channel_title,account_id')->dateBetween('create_at');
            $query->where(['status' => intval($this->type === 'index'), 'deleted' => 0]);
        });
    }

    /**
     * 添加频道
     * @auth true
     */
    public function add()
    {
        PluginTelegramChannelSource::mForm('form');
    }

    /**
     * 编辑频道
     * @auth true
     */
    public function edit()
    {
        PluginTelegramChannelSource::mForm('form');
    }

    /**
     * 修改频道状态
     * @auth true
     */
    public function state()
    {
        PluginTelegramChannelSource::mSave($this->_vali([
            'status.in:0,1'  => '状态值范围异常！',
            'status.require' => '状态值不能为空！',
        ]));
    }

    /**
     * 表单数据处理
     * @param array $data
     */
    protected function _form_filter(array &$data)
    {
        if ($this->request->isGet()){
            $this->account = PluginTelegramAccount::getAccount('account_id,title');
        }
    }

    /**
     * 删除频道
     * @auth true
     */
    public function remove()
    {
        PluginTelegramChannelSource::mDelete();
    }


    /**
     * 启动采集
     * @auth true
     */
    public function channel()
    {
        $data = $this->_vali(['id.require' => 'ID不可为空！','title.require' => '标题不可为空！']);
        $this->_queue("启动频道【{$data['title']}】采集任务", "plugin:telegram:channel", 0, ['id'=>$data['id']],0,3600);
    }
}