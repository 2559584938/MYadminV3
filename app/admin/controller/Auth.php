<?php

namespace app\admin\controller;

use app\admin\AdminBase;
use app\admin\model\AuthRule;
use think\exception\ValidateException;

class Auth extends AdminBase
{
    /**
     * 无需权限判断的方法
     * @var array
     */
    protected $noNeedAuth = ['icon'];

    /**
     * 规则管理
     * @return \think\response\View
     */
    public function index()
    {
        //print_r(list_to_tree(getMenuData()));
        if (request()->isPost()) {
            $data = input('param.');
            $data = AuthRule::where(['id'=>$data['id']])->find();
            return to_assign(0, '获取成功', $data);
        }else{
            $list = AuthRule::order(['weight','id'])->select()->toArray();
            return view('index',[
                'sidenav'   =>  $list
            ]);
        }
    }

    /**
     * 返回Json格式的数据
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function datalist()
    {
        $list = AuthRule::order(['weight','id'])->select();
        return to_assign(0, '获取成功', $list);
    }

    public function post_submit()
    {
        if (request()->isPost()) {
            $param = input('param.');
            if ($param['id'] > 0) {
                if(!empty($param['title'])){
                    try {
                        $this->validate($param, 'Auth');
                    } catch (ValidateException $e) {
                        // 验证失败 输出错误信息
                        return to_assign(1, $e->getError());
                    }
                }
                $result = AuthRule::update($param, ['id' => $param['id']]);
                if ($result == true) {
                    return to_assign(0, '规则编辑成功');
                } else {
                    return to_assign(1, '规则编辑失败');
                }
            }else{
                try {
                    $this->validate($param, 'Auth');
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $result = AuthRule::create($param);
                if ($result == true) {
                    return to_assign(0, '规则添加成功');
                } else {
                    return to_assign(1, '规则添加失败');
                }
            }
        }
    }

    /**
     * 删除规则
     */
    public function del($id)
    {
        if (request()->isPost()) {
            $data = input('param.');
            if(empty($id)){
                $ids = explode(',', $data['ids']);
            }else{
                $ids = $id;
            }
            $result = AuthRule::destroy($ids);
            if ($result == true) {
                return to_assign(0, '规则删除成功');
            } else {
                return to_assign(1, '规则删除失败');
            }
        }
    }
}