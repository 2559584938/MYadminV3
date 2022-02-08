<?php

namespace app\admin\validate;

use think\Validate;

class Configure extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        'domain' => 'url',
        'accesskey' => 'alphaDash',
        'secretkey' => 'alphaDash',
        'appid' => 'alphaNum',
        'appsecret' => 'alphaDash',
        'token' => 'alphaNum',
        'aeskey' => 'alphaDash',
        'mch_id' => 'number',
        'mch_key' => 'alphaDash',
        'username' => 'email',
        'notice_email' => 'email',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'domain.url' => '请输入正确的域名地址',
        'accesskey.alphaDash' => 'AccessKey只能是字母和数字，下划线_及破折号-',
        'secretkey.alphaDash' => 'SecretKey只能是字母和数字，下划线_及破折号-',
        'appid.alphaNum' => 'Appid只能是字母和数字',
        'appsecret.alphaDash' => 'AppSecret只能是字母和数字，下划线_及破折号-',
        'token.alphaNum' => 'Token只能是字母和数字',
        'aeskey.alphaDash' => 'AesKey只能是字母和数字，下划线_及破折号-',
        'mch_id.number' => '商户号只能是数字',
        'mch_key.alphaDash' => '商户密钥只能是字母和数字，下划线_及破折号-',
        'username.email' => '请输入正确的邮箱地址',
        'notice_email.email' => '请输入正确的邮箱地址',
    ];

    /**
     * 验证不同场景的数据
     * @var array
     */
    protected $scene = [
        'system' => ['domain'],
        'storage' => ['accesskey', 'secretkey', 'domain'],
        'email' => ['username', 'notice_email'],
        'weixin' => ['appid', 'appsecret', 'token', 'aeskey'],
        'wxapp' => ['appid', 'appsecret'],
        'wxpay' => ['appid', 'mch_id', 'mch_key'],
    ];
}
