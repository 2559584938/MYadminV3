<?php

namespace app\admin\model;

use app\admin\model\AdminModel;
use think\Model;

class Attachment extends Model
{
    /**
     * 格式化字节大小
     * @param $value
     * @return string
     */
    public function getFilesizeAttr($value)
    {
        return formatBytes($value);
    }

    /**
     * @param $value
     * @return string
     */
    public function getStorageAttr($value)
    {
        $storage = ['local'=>'本地', 'qiniu'=>'七牛云'];
        return $storage[$value];
    }

    /**
     * 保存上传文件信息
     * @param $url
     * @param $filetype
     * @param $filesize
     * @param $mimetype
     * @param $storage
     * @param $sha1
     */
    public static function record($url, $filetype, $filesize, $mimetype, $storage, $sha1, $hash = '')
    {
        self::create([
            'admin_id' => getAdminId(),
            'url' => $url,
            'filetype' => $filetype,
            'filesize' => $filesize,
            'mimetype' => $mimetype,
            'storage' => $storage,
            'sha1' => $sha1,
            'hash' => $hash,
        ]);
    }

    /**
     * 获取上传者信息
     * @return \think\model\relation\HasOne
     */
    public function username()
    {
        return $this->hasOne(AdminModel::class, 'id', 'admin_id')->bind(['username']);
    }
}