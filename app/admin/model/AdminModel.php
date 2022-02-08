<?php

namespace app\admin\model;

use app\admin\model\AuthGroupAccess;
use think\Model;

class AdminModel extends Model
{
// 模型名
    protected $name = 'admin';

    // 字段设置类型自动转换
    protected $type = [
        'login_time'  =>  'timestamp',
        'last_login_time'  =>  'timestamp',
    ];

    /**
     * @param $value
     * @return string
     */
    public function setPasswordAttr($value)
    {
        return md5($value);
    }

    /**
     * @return \think\model\relation\HasMany
     */
    public function roles()
    {
        return $this->hasMany(AuthGroupAccess::class,'uid','id')->with('group')->order('group_id');
    }

    /**
     * @return \think\model\relation\HasMany
     */
    public function rolesi($id)
    {
        return $this->hasMany(AuthGroupAccess::class,'uid','id')->with('group')->order('group_id');
    }
}