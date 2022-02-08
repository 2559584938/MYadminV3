<?php

namespace app\admin\controller;

use app\admin\AdminBase;
use app\admin\model\OplogModel;

class Oplog extends AdminBase
{
    /**
     * 日志管理
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
    public function datalist()
    {
        $list = OplogModel::order('id', 'desc')->select();
        $ip2region = new \Ip2Region();
        foreach ($list as $item) {
            $result = $ip2region->btreeSearch($item['geoip']);
            $item['isp'] = isset($result['region']) ? $result['region'] : '';
            $item['isp'] = str_replace(['中国|', '0|', '内网IP|', '|'], '', $item['isp']);
        }
        return to_assign(0, '获取成功', $list);
    }

    /**
     * 删除日志
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
            $result = OplogModel::destroy($ids);
            if ($result == true) {
                return to_assign(0, '删除成功');
            } else {
                return to_assign(1, '删除失败');
            }
        }
    }
}