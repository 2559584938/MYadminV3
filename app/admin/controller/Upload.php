<?php

namespace app\admin\controller;

use app\admin\AdminBase;
use app\admin\model\Attachment;
use app\admin\model\Config;
use Qiniu\Auth;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
use think\facade\Filesystem;

class Upload extends AdminBase
{
    /**
     * 无需权限判断的方法
     * @var array
     */
    protected $noNeedAuth = ['local', 'qiniu', 'delFile'];

    /**
     * 上传到本地
     * @param $file
     * @param $path
     * @param $sucMsg
     * @param $errMsg
     * @param string $editor
     * @return \think\response\Json|void
     */
    public function local($file, $path, $sucMsg, $errMsg, $editor = '')
    {
        $savename = Filesystem::disk('public')->putFile($path, $file);
        $filepath = Filesystem::getDiskConfig('public','url').'/'.str_replace('\\','/',$savename);
        $url = $filepath;
        Attachment::record($url, $file->getOriginalExtension(), $file->getSize(), $file->getOriginalMime(), 'local', $file->hash());
        if ($editor == '') {
            if ($savename == true) {
                return $this->success($sucMsg, '', ['filePath' => $url]);
            } else {
                return $this->error($errMsg);
            }
        } else {
            return json(['location' => $url]);
        }
    }

    /**
     * 上传到七牛云
     * @param $file
     * @param $accesskey
     * @param $secretkey
     * @param $bucket
     * @param $domain
     * @param $msg
     * @param string $editor
     * @return \think\response\Json|void
     * @throws \Exception
     */
    public function qiniu($file, $accesskey, $secretkey, $bucket, $domain, $msg, $editor = '')
    {
        // 构建鉴权对象
        $auth = new Auth($accesskey, $secretkey);
        // 生成上传 Token
        $token = $auth->uploadToken($bucket);
        // 要上传文件的本地路径
        $filePath = $file->getRealPath();
        // 上传到七牛存储后保存的文件名
        $key = date("Ymd",time()).'/'.$file->md5().'.'.$file->getOriginalExtension();
        // 初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new UploadManager();
        // 调用 UploadManager 的 putFile 方法进行文件的上传，该方法会判断文件大小，进而决定使用表单上传还是分片上传，无需手动配置。
        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
        if ($err !== null) {
            return $this->error('上传失败');
        } else {
            Attachment::record($domain.'/'.$ret['key'], $file->getOriginalExtension(), $file->getSize(), $file->getOriginalMime(), 'qiniu', $file->hash(), $ret['hash']);
            if ($editor == '') {
                return $this->success($msg, '', ['filePath' => $domain.'/'.$ret['key']]);
            } else {
                return json(['location' => $domain.'/'.$ret['key']]);
            }
        }
    }

    /**
     * 图片上传
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function upload()
    {
        $file = request()->file('file');
        $storage = Config::getConfigData('storage');
        if ($storage['engine'] == 1) { //本地存储
            $this->local($file, 'images', '图片上传成功', '图片上传失败');
        } elseif ($storage['engine'] == 2) { //七牛云存储
            $this->qiniu($file, $storage['accesskey'], $storage['secretkey'], $storage['bucket'], $storage['domain'], '图片上传成功');
        }
    }

    /**
     * 文件上传
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function upload_file_same()
    {
        $file = request()->file('file');
        $storage = Config::getConfigData('storage');
        if ($storage['engine'] == 1) { //本地存储
            $this->local($file, 'file', '文件上传成功', '文件上传失败');
        } elseif ($storage['engine'] == 2) { //七牛云存储
            $this->qiniu($file, $storage['accesskey'], $storage['secretkey'], $storage['bucket'], $storage['domain'], '文件上传成功');
        }
    }

    /**
     * 编辑器上传
     * @return \think\response\Json|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function upload_editor_same()
    {
        $file = request()->file('file');
        $storage = Config::getConfigData('storage');
        if ($storage['engine'] == 1) { //本地存储
            return $this->local($file, 'images', '', '', 1);
        } elseif ($storage['engine'] == 2) { //七牛云存储
            return $this->qiniu($file, $storage['accesskey'], $storage['secretkey'], $storage['bucket'], $storage['domain'], '', 1);
        }
    }

    public function delFile($fileName)
    {
        $storage = Config::getConfigData('storage');
        // 控制台获取密钥：https://portal.qiniu.com/user/key
        $accessKey = $storage['accesskey'];
        $secretKey = $storage['secretkey'];
        $bucket = $storage['bucket'];

        $auth = new Auth($accessKey, $secretKey);

        $config = new \Qiniu\Config();
        $bucketManager = new BucketManager($auth, $config);

        // 删除指定资源，参考文档：https://developer.qiniu.com/kodo/api/1257/delete
        $key = $fileName;

        $err = $bucketManager->delete($bucket, $key);
    }
}