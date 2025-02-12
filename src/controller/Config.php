<?php

declare (strict_types=1);

namespace plugin\telegram\madeline\controller;

use plugin\telegram\madeline\service\ConfigService;
use think\admin\Controller;
use think\admin\Exception;

/**
 * 投票参数配置
 * @class Config
 * @package plugin\telegram\madeline\controller
 */
class Config extends Controller
{



    /**
     * Telegram参数配置
     * @auth true
     * @menu true
     * @return void
     * @throws Exception
     */
    public function index()
    {
        $this->title = 'Telegram配置';
        $this->data = ConfigService::get();
        $this->fetch();
    }

    /**
     * 修改参数配置
     * @auth true
     * @return void
     * @throws Exception
     */
    public function params()
    {
        if ($this->request->isGet()) {
            $this->vo = ConfigService::get();
            $this->fetch('index_params');
        } else {
            ConfigService::set($this->request->post());
            $this->success('配置更新成功！');
        }
    }
}