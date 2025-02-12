<?php

declare (strict_types=1);

namespace plugin\telegram\controller\api;

use plugin\telegram\service\MadelineProtoApi;
use plugin\telegram\service\SocketService;
use plugin\telegram\service\TelegramApi;
use plugin\telegram\service\WorkerService;
use think\admin\Controller;
use think\exception\HttpResponseException;

/**
 * 目标频道操作
 * @package plugin\telegram\controller\api
 */
class Source extends Controller
{

    /**
     * 获取频道消息
     */
    public function getMessage()
    {
        try {
            $channel = $this->_vali([
                'account_id.require'   => '账号ID不可为空！',
                'channel_id.require'   => '频道ID不可为空！',
                'limit_number.require' => '数量不可为空！'
            ]);
            $response = MadelineProtoApi::ChannelNewMessage($channel);
            $this->success('获取成功',$response);
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    /**
     * 获取频道历史消息
     */
    public function getHistory()
    {
        try {
            $channel = $this->_vali([
                'account_id.require'   => '账号ID不可为空！',
                'channel_id.require'   => '频道ID不可为空！',
                'limit_number.require' => '数量不可为空！',
                'last_message_id.require' => '开始ID不可为空！'
            ]);
            $response = MadelineProtoApi::ChannelHistoryMessage($channel);
            $this->success('获取成功',$response);
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    /**
     * 获取频道消息评论
     */
    public function getComment()
    {
        try {
            $channel = $this->_vali([
                'account_id.require'  => '账号ID不可为空！',
                'channel_id.require'  => '频道ID不可为空！',
                'message_id.require'  => '内容ID不可为空！',
                'replies.require'     => '评论数量不可为空！'
            ]);
            $response = MadelineProtoApi::ChannelMessageComments($channel);
            $this->success('获取成功',$response);
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    /**
     * 获取文件地址
     */
    public function file()
    {
        try {
            $file = $this->_vali([
                'file_id.require'  => '文件ID不可为空！',
                'token.require'    => 'TOKEN不可为空！'
            ]);
            $response = TelegramApi::getFile($file['file_id'],$file['token']);
            $this->success('获取成功',$response);
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    /**
     * 获取文件原始ID
     */
    public function getFileId()
    {
        try {
            $file = $this->_vali([
                'media.require'      => 'ID不可为空！',
                'account_id.require' => '账号ID不可为空！',
            ]);
            $response = MadelineProtoApi::getOriginalFileId($file['account_id'],$file['media']);
            $this->success('获取成功',$response);
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    /**
     * 账号保活
     */
    public function wakeUp()
    {
        try {
            $data = $this->_vali([
                'account_id.require'   => '账号ID不可为空！',
                'bot_username.require' => '机器名不可为空！',
                'message.require'      => '消息不可为空！',
            ]);
            $response = MadelineProtoApi::toBotMessage($data['account_id'],$data['bot_username'],$data['message']);
            $this->success('获取成功',$response);
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    /**
     * 转发频道消息
     */
    public function forMessage()
    {
        try {
            $map = $this->_vali([
                'account_id.require'  => '账号ID不可为空！',
                'to_peer.require'     => '目标频道不可为空！',
                'from_peer.require'   => '来源频道不可为空！',
                'message.require'     => '消息不可为空！',
            ]);
            $response = MadelineProtoApi::forwardMessages($map['account_id'],$map['to_peer'],$map['from_peer'],$map['message']);
            $this->success('获取成功',$response);
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}