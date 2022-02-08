<?php

namespace app\admin\controller;

use app\admin\AdminBase;
use app\admin\model\AuthGroup;
use app\admin\model\AuthRule;
use think\exception\ValidateException;
use think\facade\View;

class Role extends AdminBase
{
    /**
     * 角色管理
     * @return \think\response\View
     */
    public function index()
    {
        if (request()->isPost()) {
            $data = input('param.');
            $data = AuthGroup::where(['id'=>$data['id']])->find();
            return to_assign(0, '获取成功', $data);
        }else{
            return view();
        }
    }

    /**
     * 返回Json格式的数据
     * @param int $limit
     * @throws \think\db\exception\DbException
     */
    public function datalist($limit=15)
    {
        $list = AuthGroup::order('id')->paginate($limit);
        return to_assign(0, '获取成功', $list);
    }


    public function post_submit()
    {
        if (request()->isPost()) {
            $param = input('param.');
            if ($param['id'] > 0) {
                if(!empty($param['title'])){
                    try {
                        $this->validate($param, 'Role');
                    } catch (ValidateException $e) {
                        // 验证失败 输出错误信息
                        return to_assign(1, $e->getError());
                    }
                }
                $result = AuthGroup::update($param, ['id' => $param['id']]);
                if ($result == true) {
                    return to_assign(0, '角色编辑成功');
                } else {
                    return to_assign(1, '角色编辑失败');
                }
            }else{
                try {
                    $this->validate($param, 'Role');
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $result = AuthGroup::create($param);
                if ($result == true) {
                    return to_assign(0, '角色添加成功');
                } else {
                    return to_assign(1, '角色添加失败');
                }
            }
        }
    }

    /**
     * 删除角色
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
            $result = AuthGroup::destroy($ids);
            if ($result == true) {
                return to_assign(0, '规则删除成功');
            } else {
                return to_assign(1, '规则删除失败');
            }
        }
    }

    /**
     * 权限分配
     * @param $id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function authlist()
    {
        $id = empty(get_params('id')) ? 0 : get_params('id');
        if($id > 0) {
            //$data = AuthGroup::where('id',$id)->find();
            $rule = AuthRule::field('id,pid,title')->order(['weight', 'id'])->select();
            $group = get_admin_group_info($id);
//            if(!(is_null($data['rules']))){
//                foreach ($list as $k => $v) {
//                    $list[$k]['checked'] = in_array($v['id'], explode(',', $data['rules']));
//                }
//            }
            $list = create_tree_list(0, $rule, $group['rules']);
            //return to_assign(0, '规则删除成功',$list);
        }else{
            $list = AuthRule::field('id,pid,title')->order(['weight', 'id'])->select();
            //$list = create_tree_list(0, $list, []);
        }
        View::assign('list', $list);
        View::assign('id', $id);
        return view();
    }

    public function auth_submit()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $ruleData = isset($param['rules']) ? $param['rules'] : 0;
            $param['rules'] = implode(',',$ruleData);
            if ($param['id'] == 1) {
                return to_assign(1, '为了系统安全,该管理组不允许修改');
            }
            AuthGroup::where(['id' => $param['id']])->strict(false)->field(true)->update($param);
            return to_assign();
        }
    }
}