<?php

namespace app\admin\controller;

use app\admin\AdminBase;
use app\admin\model\Attachment;
use app\admin\controller\Upload;

class Attach extends AdminBase
{
    /**
     * 附件管理
     * @return \think\response\View
     */
    public function index()
    {
        return view();
    }

    /**
     * 返回Json格式的数据
     * @param int $limit
     * @throws \think\db\exception\DbException
     */
    public function datalist($limit=15)
    {
        $list = Attachment::with(['username'])->order('id', 'desc')->paginate($limit);
        return to_assign(0, '获取成功', $list);
    }

    /**
     * 删除附件
     * @param $id
     */
    public function del($id)
    {
        if (request()->isPost()) {
            $data = input('param.');
            if (empty($id)) {
                $ids = explode(',', $data['ids']);
            } else {
                $ids = $id;
            }

            $idsdata = \app\admin\model\Attachment::whereIn('id', $ids)->select();
            foreach($idsdata as $value){
                if ($value['storage'] === '七牛云') {
                    $arr = parse_url($value['url']);
                    $upload = new Upload($this->app);
                    $upload->delFile(substr($arr['path'],1));
                } else if($value['storage'] === '本地') {
                    $url = str_ireplace(request()->domain(), '', $value['url']);
                    $path = substr($url, 1);
                    if (file_exists($path)) {
                        unlink($path);
                    }
                }
            }
            $result = Attachment::destroy($ids);
            if ($result == true) {
                return to_assign(0, '删除成功');
            } else {
                return to_assign(1, '删除失败');
            }
        }
    }
}