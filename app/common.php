<?php
// 应用公共文件
use think\facade\Request;

if (!function_exists('getAdminId')) {
    /**
     * 获取用户ID
     * @return mixed
     */
    function getAdminId()
    {
        $data = session('admin_info.admin_id');
        return $data;
    }
}

if (!function_exists('data_sign')) {
    /**
     * 数据签名认证
     * @param  array $data 被认证的数据
     * @return string       签名
     */
    function data_sign($data)
    {
        // 数据类型检测
        if (!is_array($data)) {
            $data = (array)$data;
        }
        ksort($data); // 排序
        $code = http_build_query($data); // url编码并生成query字符串
        $sign = sha1($code); // 生成签名
        return $sign;
    }
}

/**
 * 返回json数据，用于接口
 * @param    integer    $code
 * @param    string     $msg
 * @param    array      $data
 * @param    string     $url
 * @param    integer    $httpCode
 * @param    array      $header
 * @param    array      $options
 * @return   json
 */
function to_assign($code = 0, $msg = "操作成功", $data = [], $url = '', $httpCode = 200, $header = [], $options = [])
{
    $res = ['code' => $code];
    $res['msg'] = $msg;
    $res['url'] = $url;
    $res['time'] = time();
    if (is_object($data)) {
        $data = $data->toArray();
    }
    $res['data'] = $data;
    $response = \think\Response::create($res, "json", $httpCode, $header, $options);
    throw new \think\exception\HttpResponseException($response);
}

if (!function_exists('list_to_tree')) {
    /**
     * 把返回的数据集转换成Tree
     * @param $list 要转换的数据集
     * @param bool $disabled 渲染下拉树xmSelect时，有子类不可选择，默认可选
     * @param string $pk
     * @param string $pid
     * @param string $children 有子类时添加children数组
     * @param int $root
     * @return array
     */
    function list_to_tree($list, $disabled = false, $pk='id', $pid = 'pid', $children = 'children', $root = 0)
    {
        // 创建Tree
        $tree = array();
        if(is_array($list)) {
            // 创建基于主键的数组引用
            $refer = array();
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] =& $list[$key];
            }

            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId =  $data[$pid];
                if ($root == $parentId) {
                    $tree[] =& $list[$key];
                }else{
                    if (isset($refer[$parentId])) {
                        $parent =& $refer[$parentId];
                        $parent[$children][] =& $list[$key];
                        $disabled ? $parent['disabled'] = true : '';
                    }
                }
            }
        }
        return $tree;
    }
}

//获取url参数
function get_params($key = "")
{
    return Request::instance()->param($key);
}