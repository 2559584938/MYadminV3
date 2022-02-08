<?php

namespace app\admin\validate;

use think\Validate;

class Auth extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'title' => 'require|chsAlphaNum',
        'name' => '/^[\w\/?=\.]+$/',
        'weight' => 'number',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message = [
        'title.require' => '规则名称不能为空！',
        'title.chsAlphaNum' => '规则名称只能是汉字、字母和数字！',
        'name' => '输入正确的规则URL！',
        'weight.number' => '排序权重只能是数字！',
    ];
}