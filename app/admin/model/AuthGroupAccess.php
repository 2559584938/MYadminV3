<?php

namespace app\admin\model;

use app\admin\model\AuthGroup;
use think\Model;

class AuthGroupAccess extends Model
{
    /**
     * @return \think\model\relation\HasOne
     */
    public function group()
    {
        return $this->hasOne(AuthGroup::class,'id','group_id')->bind(['title']);
    }
}