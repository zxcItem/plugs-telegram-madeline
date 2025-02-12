<?php

declare (strict_types=1);

namespace plugin\telegram\controller;

use danog\MadelineProto\API;
use danog\MadelineProto\Exception;
use danog\MadelineProto\Settings;
use plugin\telegram\model\PluginTelegramAccount;
use plugin\telegram\service\MadelineProtoApi;
use think\admin\Controller;
use think\admin\helper\QueryHelper;
use think\exception\HttpResponseException;
use \danog\MadelineProto\Settings\AppInfo;

/**
 * 账号管理
 * Class Account
 * @package plugin\telegram\controller
 */
class Account extends Controller
{

    /**
     * 账号管理
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
        PluginTelegramAccount::mQuery()->layTable(function () {
            $this->title = '账号管理';
        }, function (QueryHelper $query) {
            $query->like('username,phone_number')->dateBetween('create_at');
            $query->where(['status' => intval($this->type === 'index'), 'deleted' => 0]);
        });
    }

    /**
     * 添加账号
     * @auth true
     */
    public function add()
    {
        PluginTelegramAccount::mForm('form');
    }

    /**
     * 编辑账号
     * @auth true
     */
    public function edit()
    {
        PluginTelegramAccount::mForm('form');
    }

    /**
     * 修改账号状态
     * @auth true
     */
    public function state()
    {
        PluginTelegramAccount::mSave($this->_vali([
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

    }

    /**
     * 删除账号
     * @auth true
     */
    public function remove()
    {
        PluginTelegramAccount::mDelete();
    }

    /**
     * 自动刷新账号活跃
     * @auth true
     */
    public function account()
    {
        $this->_queue('自动刷新账号活跃', "plugin:telegram:BotActive", 0,[],0,1800);
    }

    /**
     * 初始化 MadelineProto
     * @param $session_file
     * @param null $settings
     * @return API
     * @throws Exception
     */
    private function initMadelineProto($session_file,$settings = null)
    {
        return new API($session_file,$settings);
    }

    /**
     * 账号授权登录
     */
    public function login()
    {
        try {
            $data = $this->_vali(['account_id.require' => '账号ID不可为空！']);
            $account = PluginTelegramAccount::getTelegramId($data['account_id'],'account_id,phone_number,api_id,api_hash');
            $appInfo = (new AppInfo())->setApiId($account['api_id'])->setApiHash($account['api_hash']);
            $settings = (new Settings)->setAppInfo($appInfo);
            $MadelineProto = MadelineProtoApi::initMadelineProto($data['account_id'],$settings);
            $MadelineProto->phoneLogin($account['phone_number']);
            $this->fetch('sms_code',['vo' => $account]);
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    /**
     * 提交验证码，完成登录
     */
    public function submitCode()
    {
        try {
            $data = $this->_vali([
                'code.require'         => '验证码不可为空！',
                'account_id.require'  => '账号ID不可为空！'
            ]);
            $madeline = MadelineProtoApi::initMadelineProto($data['account_id']);
            $madeline->completePhoneLogin($data['code']);
            $this->success('授权成功，登录完成！');
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    /**
     * 获取用户信息，确认是否登录成功
     */
    public function info()
    {
        try {
            $data = $this->_vali(['account_id.require' => '账号ID不可为空！']);
            $madeline = MadelineProtoApi::initMadelineProto($data['account_id']);
            // 获取当前用户信息
            $this->user = $madeline->getSelf();
            $this->fetch('',$this->user);
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

}