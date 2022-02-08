<?php

namespace app\admin\validate;

use think\Validate;

class Role extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'title' => 'require|chsAlphaNum|unique:auth_group',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message = [
        'title.require' => '角色名称不能为空！',
        'title.chsAlphaNum' => '角色名称只能是汉字、字母和数字！',
        'title.unique' => '角色名称已经存在，请重新输入！',
    ];
}