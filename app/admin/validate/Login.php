<?php

namespace app\admin\validate;

use think\Validate;

class Login extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'username' => 'require|length:2,20|alphaNum',
        'password' => 'require|length:6,20|alphaNum',
        'captcha' => 'require|captcha',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message = [
        'username.require' => '登录账号不能为空！',
        'username.length' => '登录账号必须2到20个字符！',
        'username.alphaNum' => '登录账号只能是字母和数字！',
        'password.require' => '登录密码不能为空！',
        'password.length' => '登录密码必须6到20个字符！',
        'password.alphaNum' => '登录密码只能是字母和数字！',
        'captcha.require' => '验证码不能为空！',
        'captcha.captcha' => '验证码错误，请重新输入！',
    ];

    /**
     * 无验证码场景定义
     * @var array
     */
    protected $scene = [
        'nocaptcha'  =>  ['username','password'],
    ];
}