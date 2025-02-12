<?php

declare (strict_types=1);

namespace plugin\telegram\controller;

use plugin\telegram\model\PluginTelegramChannel;
use plugin\telegram\model\PluginTelegramChannelContent;
use plugin\telegram\model\PluginTelegramChannelRelease;
use plugin\telegram\model\PluginTelegramChannelSource;
use plugin\telegram\service\ConfigService;
use plugin\telegram\service\MadelineProtoApi;
use think\admin\Controller;
use think\admin\helper\QueryHelper;
use think\exception\HttpResponseException;
use think\exception\PDOException;

/**
 * 目标频道资源
 * Class Content
 * @package plugin\telegram\controller
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
        PluginTelegramChannelContent::mQuery()->withoutField('cover')->layTable(function () {
            $this->title = '目标频道资源';
            $this->source = PluginTelegramChannelSource::mk()->column('channel_title','channel_id');
        }, function (QueryHelper $query) {
            $query->with(['channel'=>function($channel){
                $channel->field('channel_id,channel_title');
            },'account'])->equal('channel_id,status,forward,is_replies')->like('caption,type')->dateBetween('create_at');
        });
    }

    /**
     * 修改内容状态
     * @auth true
     */
    public function state()
    {
        PluginTelegramChannelContent::mSave($this->_vali([
            'status.in:0,1'  => '状态值范围异常！',
            'status.require' => '状态值不能为空！',
        ]));
    }

    /**
     * 编辑内容
     * @auth true
     */
    public function edit()
    {
        PluginTelegramChannelContent::mForm('form');
    }

    /**
     * 删除内容
     * @auth true
     */
    public function remove()
    {
        PluginTelegramChannelContent::mDelete();
    }

    /**
     * 删除结果处理
     * @param boolean $result
     */
    protected function _remove_delete_filter($result)
    {
        if ($result) {
            $where = ['id' => $this->request->post('id')];
            $content = PluginTelegramChannelContent::mk()->withoutField('cover')->where($where)->with(['media'=>function($media){
                $media->field('id,grouped_id');
            }])->find()->toArray();
            $ids = array_column($content['media'],'id');
            PluginTelegramChannelContent::mk()->whereIn('id',$ids)->delete();
            $this->success("删除成功！", '');
        } else {
            $this->error("删除失败，请稍候再试！");
        }
    }

    /**
     * 自动刷新资源评论
     * @auth true
     */
    public function comment()
    {
        $this->_queue('自动刷新资源评论', "plugin:telegram:comment", 0);
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
     * 内容媒体详情
     * @auth true
     */
    public function announce()
    {
        try {
            if ($this->request->isGet()){
                $data = $this->_vali(['id.require' => 'ID不可为空！']);
                $this->media = PluginTelegramChannelContent::mk()->withoutField('cover')->where('id',$data['id'])->with(['media'=>function($media){
                    $media->field('id,grouped_id,type,caption,media,cover');
                }])->find()->toArray();
                if (isset($this->media['media'])) {
                    $nonEmptyCaptions = array_filter($this->media['media'], function ($item) {
                        return !empty($item['caption']);
                    });
                    $this->caption = reset($nonEmptyCaptions)['caption'] ?? null;
                    $this->caption = $this->caption.ConfigService::get('caption');
                }
                $this->source = PluginTelegramChannelSource::getChannel($this->media['channel_id'],'channel_title,release_channel_id');
                $this->channel = PluginTelegramChannel::getChannel('channel_id,channel_title');
                $this->title = '资源媒体详情';
                $this->fetch();
            }
            $content = $this->_vali([
                'channel_id.require' => '发布频道不可为空！',
                'caption.default'    => '',
                'media.require'      => '发布媒体不可为空！',
            ]);
            $media = json_decode($content['media'],true);
            if ($content['caption']) $media[0]['caption'] = $content['caption'];
            $mediaDta = array_map(function ($item){
                unset($item['grouped_id']);
                $item['parse_mode'] = 'HTML';
                return $item;
            },$media);
            PluginTelegramChannelRelease::mk()->save([
                'channel_id' => $content['channel_id'],
                'caption'    => $content['caption'],
                'media'      => json_encode($mediaDta)
            ]);
            $ids = array_column($media,'id');
            PluginTelegramChannelContent::mk()->whereIn('id',$ids)->update(['status'=>1]);
            $this->success('资源收录成功！','javascript:history.back()');
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    /**
     * 刷新媒体信息数据
     * @auth true
     */
    public function forward()
    {
        try {
            $map = $this->_vali(['id.require' => 'ID不可为空！']);
            $content = PluginTelegramChannelContent::mk()->withoutField('cover')->where($map)->with(['media'=>function($media){
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