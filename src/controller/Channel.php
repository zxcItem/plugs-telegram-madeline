<?php

declare (strict_types=1);

namespace plugin\telegram\controller;

use plugin\telegram\model\PluginTelegramAccount;
use plugin\telegram\model\PluginTelegramChannel;
use plugin\telegram\service\ConfigService;
use plugin\telegram\service\TelegramApi;
use think\admin\Controller;
use think\admin\helper\QueryHelper;
use think\exception\HttpResponseException;


/**
 * 经营频道管理
 * Class Source
 * @package plugin\telegram\controller
 */
class Channel extends Controller
{

    /**
     * 经营频道管理
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
        PluginTelegramChannel::mQuery()->layTable(function () {
            $this->title = '经营频道管理';
        }, function (QueryHelper $query) {
            $query->with(['account'])->like('channel_name,channel_title')->dateBetween('create_at');
            $query->where(['status' => intval($this->type === 'index'), 'deleted' => 0]);
        });
    }

    /**
     * 添加频道
     * @auth true
     */
    public function add()
    {
        PluginTelegramChannel::mForm('form');
    }

    /**
     * 编辑频道
     * @auth true
     */
    public function edit()
    {
        PluginTelegramChannel::mForm('form');
    }

    /**
     * 修改频道状态
     * @auth true
     */
    public function state()
    {
        PluginTelegramChannel::mSave($this->_vali([
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
        PluginTelegramChannel::mDelete();
    }

    /**
     * 获取频道的id
     * @return mixed
     */
    public function getChannelID()
    {
        try {
            $channel_name = $this->request->post('channel_name');
            $token = ConfigService::get('bot_token');
            $channel = TelegramApi::getChat($token,$channel_name);
            return $channel['id'];
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}