<?php
// 这是系统自动生成的公共文件

if (!function_exists('getMenuData')) {
    /**
     * 获取菜单数据
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    function getMenuData()
    {
        if (session('admin_info.is_admin') == 1) {
            $where = ['isnav' => 1, 'status' => 1];
        } else {
            $groupid = \app\admin\model\AuthGroupAccess::where('uid', getAdminId())->column('group_id');
            $authid = implode(',', \app\admin\model\AuthGroup::where('id', 'in', $groupid)->column('rules'));
            $arr = explode(',', $authid); //explode()以逗号为分割，变成一个新的数组
            $arr = array_unique($arr); //array_unique()函数移除数组中的重复的值，并返回结果数组
            $data = implode(',', $arr); //implode() 函数返回由数组元素组合成的字符串
            $where = [
                ['id', 'in', $data],
                ['isnav', '=', 1],
                ['status', '=', 1],
            ];
        }
        $menulist = \app\admin\model\AuthRule::where($where)->order(['weight', 'id'])->select()->toArray();
        return $menulist;
    }
}

//递归排序
function set_recursion($result, $pid = 0, $format = "L ")
{
    /*记录排序后的类别数组*/
    static $list = array();

    foreach ($result as $k => $v) {
        if ($v['pid'] == $pid) {
            if ($pid != 0) {
                $v['title'] = $format . $v['title'];
            }
            /*将该类别的数据放入list中*/
            $list[] = $v;
            set_recursion($result, $v['id'], "  " . $format);
        }
    }

    return $list;
}

function create_tree_list($pid, $arr, $group, &$tree = [])
{
    foreach ($arr as $key => $vo) {
        if ($key == 0) {
            $vo['spread'] = true;
        }
        if (!empty($group) and in_array($vo['id'], $group)) {
            $vo['checked'] = true;
        } else {
            $vo['checked'] = false;
        }
        if ($vo['pid'] == $pid) {
            $child = create_tree_list($vo['id'], $arr, $group);
            if ($child) {
                $vo['children'] = $child;
            }
            $tree[] = $vo;
        }
    }
    return $tree;
}

function get_admin_group_info($id)
{
    $group = \think\facade\Db::name('AuthGroup')->where(['id' => $id])->find();
    $group['rules'] = explode(',', $group['rules']);
    return $group;
}

if (!function_exists('formatBytes')) {
    /**
     * @param $size 字节数
     * @param string $delimiter 数字和单位分隔符
     * @return string 格式化后的带单位的大小
     */
    function formatBytes($size, $delimiter = '')
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
        return round($size, 2) . $delimiter . $units[$i];
    }
}

if (!function_exists('basicConfiguration')) {
    /**
     * @return string 基本配置
     */
    function basicConfiguration($data)
    {
        return \app\admin\model\Config::getConfigassign('system',$data);
    }
}
