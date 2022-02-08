<?php

namespace app\admin;

use app\BaseController;

class AdminBase extends BaseController
{
    /**
     * 无需登录的方法
     * @var array
     */
    protected $noNeedLogin = [];

    /**
     * 无需权限判断的方法
     * @var array
     */
    protected $noNeedAuth = ['form', 'serach'];

    /**
     * 类初始化
     */
    public function initialize()
    {
        !$this->checkLogin() && $this->error('登录超时，请重新登录', url('@admin/login'));
        !$this->checkAuth() && $this->error('抱歉，您没有操作权限');
    }

    /**
     * @return bool
     */
    public function checkLogin()
    {
        if (!$this->isLogin() && !in_array(strtolower(request()->action()), $this->noNeedLogin)) {
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    public function checkAuth()
    {
        $auth = new \liliuwei\think\Auth();
        $appname = strtolower(app('http')->getName());
        $controller = strtolower(request()->controller());
        // 复用方法中第一个短杆之前的验证权限
        if(substr(strtolower(request()->action()),-5) == '_same'){
            $action = substr(strtolower(request()->action()),0, stripos(strtolower(request()->action()),'_'));
        }else{
            $action = strtolower(request()->action());
        }
        $url = $appname . "/" . $controller . "/" . $action;
        if (session('admin_info.is_admin') == 0 && !in_array($action, $this->noNeedLogin) && !in_array($action, $this->noNeedAuth) && !$auth->check($url, getAdminId()))
        {
            return false;
        }
        return true;
    }

    /**
     * 检测是否登录
     * @return bool
     */
    public function isLogin()
    {
        $admin = session('admin_info');
        if (empty($admin)) {
            return false;
        } else {
            return session('admin_sign') == data_sign($admin) ? true : false;
        }
    }

}