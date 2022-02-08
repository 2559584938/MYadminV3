<?php

namespace app\admin\controller;

use app\admin\AdminBase;
use app\admin\model\Config;
use think\exception\ValidateException;

class Configure extends AdminBase
{
    /**
     * 消息
     * @return \think\response\View
     */
    public function index()
    {
        return view('', [
            'sysconf' => Config::getConfigData('system'),
            'storage' => Config::getConfigData('storage'),
            'email' => Config::getConfigData('email'),
            'weixin' => Config::getConfigData('weixin'),
            'wxapp' => Config::getConfigData('wxapp'),
            'wxpay' => Config::getConfigData('wxpay'),
        ]);
    }

    public function post_submit()
    {
        if (request()->isPost()) {
            $data = input('param.');
            try {
                $this->validate($data, 'Configure.'.$data['typename']);
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return to_assign(1, $e->getError());
            }
            \app\admin\model\Config::setConfigData($data['typename'], $data);
            return to_assign(0, '保存成功');
        }
    }
}