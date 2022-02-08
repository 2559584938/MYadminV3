<?php

namespace app\admin\validate;

use think\Validate;

class Admin extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     * @var array
     */
    protected $rule = [
        'username' => 'require|length:2,20|alphaNum|unique:admin',
        'nickname' => 'require|length:2,20|chsAlphaNum',
        'newpassword' => 'require|length:6,20|alphaNum',
        'repassword' => 'require|confirm:newpassword',
        'userEditRoleSel' => 'require',
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
        'username.unique' => '登录账号已经存在，请使用其它账号！',
        'nickname.require' => '账号昵称不能为空！',
        'nickname.length' => '账号昵称必须2到20个字符！',
        'nickname.chsAlphaNum' => '账号昵称只能是汉字、字母和数字！',
        'newpassword.require' => '新密码不能为空！',
        'newpassword.length' => '新密码必须6到20个字符！',
        'newpassword.alphaNum' => '新密码只能是字母和数字！',
        'repassword.require' => '确认密码不能为空！',
        'repassword.confirm' => '新密码和确认密码不一致！',
        'userEditRoleSel.require' => '请选择所属角色！',
    ];

    /**
     * edit 验证编辑场景定义
     * @return Admin
     */
    public function sceneEdit()
    {
        return $this->only(['nickname','newpassword','repassword'])
            ->remove('newpassword', 'require')
            ->remove('repassword', 'require');
    }

    /**
     * 修改密码场景定义
     * @var array
     */
    protected $scene = [
        'editpassword'  =>  ['newpassword','repassword'],
    ];
}
