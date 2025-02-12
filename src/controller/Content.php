<?php

declare (strict_types=1);

namespace plugin\telegram\madeline\controller;

use plugin\telegram\madeline\model\PluginTelegramSourceForward;
use plugin\telegram\madeline\model\PluginTelegramChannelSource;
use plugin\telegram\madeline\service\ConfigService;
use plugin\telegram\madeline\service\MadelineProtoApi;
use think\admin\Controller;
use think\admin\helper\QueryHelper;
use think\exception\HttpResponseException;

/**
 * 目标频道资源
 * Class Content
 * @package plugin\telegram\madeline\controller
 */
class Content extends Controller
{
    /**
     * 目标频道内容
     * @return void
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        PluginTelegramSourceForward::mQuery()->layTable(function () {
            $this->title = '目标频道资源';
            $this->source = PluginTelegramChannelSource::mk()->column('channel_title','channel_id');
        }, function (QueryHelper $query) {
            $query->with(['channel'=>function($channel){
                $channel->field('channel_id,channel_title');
            },'account'])->equal('channel_id,forward')->like('caption')->dateBetween('create_at');
        });
    }

    /**
     * 编辑内容
     * @auth true
     */
    public function edit()
    {
        PluginTelegramSourceForward::mForm('form');
    }

    /**
     * 删除内容
     * @auth true
     */
    public function remove()
    {
        PluginTelegramSourceForward::mDelete();
    }

    /**
     * 自动刷新资源信息
     * @auth true
     */
    public function preview()
    {
        $this->_queue('自动刷新资源信息', "plugin:telegram:Preview", 0,[],0,600);
    }

    /**
     * 刷新媒体信息数据
     * @auth true
     */
    public function forward()
    {
        try {
            $map = $this->_vali(['id.require' => 'ID不可为空！']);
            $content = PluginTelegramSourceForward::mk()->where($map)->with(['media'=>function($media){
                $media->field('grouped_id,message_id');
            }])->find()->toArray();
            if (isset($content['media'])) {
                $message = array_column($content['media'],'message_id');
                $forward_channel = ConfigService::get('forward_channel');
                MadelineProtoApi::forwardMessages($content['account_id'],$forward_channel,$content['channel_id'],$message,true,true);
                $this->success('刷新媒体数据成功！');
            }
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}