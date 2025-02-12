<?php

declare (strict_types=1);

namespace plugin\telegram\controller;

use plugin\telegram\model\PluginTelegramChannel;
use plugin\telegram\model\PluginTelegramChannelRelease;
use plugin\telegram\service\ConfigService;
use plugin\telegram\service\ReleaseContent;
use think\admin\Controller;
use think\admin\helper\QueryHelper;
use think\exception\HttpResponseException;


/**
 * 发布内容资源
 * Class Release
 * @package plugin\telegram\controller
 */
class Release extends Controller
{

    /**
     * 发布内容资源
     * @return void
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        PluginTelegramChannelRelease::mQuery()->withoutField('media')->layTable(function () {
            $this->title = '发布内容资源';
            $this->channel = PluginTelegramChannel::mk()->column('channel_title','channel_id');
        }, function (QueryHelper $query) {
            $query->equal('channel_id')->like('caption')->with(['channel'])->dateBetween('create_at');
        });
    }

    /**
     * 编辑内容
     * @auth true
     */
    public function edit()
    {
        PluginTelegramChannelRelease::mForm('form');
    }

    /**
     * 表单数据处理
     * @param array $data
     * @throws \think\admin\Exception
     */
    protected function _form_filter(array &$data)
    {
        if ($this->request->isGet()){
            $data['media'] = json_decode($data['media'],true);
            $this->channel = PluginTelegramChannel::getChannel('channel_id,channel_title');
            $this->title = '内容媒体详情';
        }else{
            $data['media'] = json_decode($data['media'],true);
            $data['media'][0]['caption'] = $data['caption'];
            $data['media'] = json_encode($data['media']);
        }
    }

    /**
     * 表单结果处理
     * @param boolean $state
     */
    protected function _form_result(bool $state)
    {
        if ($state) {
            $this->success('内容保存成功！', 'javascript:history.back()');
        }
    }

    /**
     * 删除内容
     * @auth true
     */
    public function remove()
    {
        PluginTelegramChannelRelease::mDelete();
    }

    /**
     * 发布频道内容
     * @auth true
     */
    public function release()
    {
        try {
            $map = $this->_vali(['id.require'=>'id不可为空！']);
            $content = PluginTelegramChannelRelease::mk()->where($map)->find()->toArray();
            $bot_token = ConfigService::get('bot_token');
            $mediaContent = [
                'channel_id' => $content['channel_id'],
                'media'      => $content['media'],
                'bot_token'  => $bot_token
            ];
            $result = ReleaseContent::contentData($mediaContent);
            if ($result['ok'] === true) {
                PluginTelegramChannelRelease::mk()->where($map)->update(['status'=>1]);
                $this->success('内容发布成功！');
            }
            $this->error('内容发布失败：'.$result['description']);
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}