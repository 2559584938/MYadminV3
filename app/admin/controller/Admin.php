<?php

namespace app\admin\controller;

use app\admin\AdminBase;
use app\admin\model\AdminModel;
use app\admin\model\AuthGroup;
use app\admin\model\AuthGroupAccess;
use think\exception\ValidateException;
use think\facade\Console;

class Admin extends AdminBase
{
    /**
     * 账号管理
     * @return \think\response\View
     */
    public function index()
    {
        if (request()->isPost()) {
            $data = input('param.');
            $admin = AdminModel::where(['id'=>$data['id']])->find();
            //$admin['password'] = '';
            $list = AdminModel::with(['roles'])->where(['id'=>$data['id']])->order('id', 'desc')->find();
            foreach ($list['roles'] as $v){
                $roleid[] = $v['id'];
            }
            return to_assign(0, '获取成功', $list);
        }
        $rolelist = AuthGroup::field('id,title')->order('id')->select();
        return view('',[
            'rolelist'  =>  $rolelist,
            'admin_id' => getAdminId(),
        ]);
    }

    /**
     * 返回Json格式的数据
     * @param int $limit
     * @throws \think\db\exception\DbException
     */
    public function datalist($limit=15)
    {
        $list = AdminModel::with(['roles'])->order('id', 'desc')->paginate($limit);
        return to_assign(0, '获取成功', $list);
    }

    public function roleslist()
    {
        $data = input('param.');
        $rolelist = AuthGroup::wher([''])->field('id,title')->order('id')->select();
        return to_assign(0, '获取成功', $rolelist);
    }

    public function post_submit()
    {
        $data = input('param.');
        if($data['id'] > 0){
            $data['role_id'] = $data['userEditRoleSel'];
            try {
                $this->validate($data, 'Admin.edit');
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return to_assign(1, $e->getError());
            }
            $data['password'] = $data['newpassword'];
            $result = AdminModel::update($data, ['id' => $data['id']]);
            if ($result == true) {
                AuthGroupAccess::where('uid', $data['id'])->delete();
                $role_id = $data['role_id'];
                foreach ($role_id as $value) {
                    $dataset[] = ['uid' => $data['id'], 'group_id' => $value];
                }
                AuthGroupAccess::insertAll($dataset);
                return to_assign(0, '编辑成功');
            }
        }else{
            //$data['userEditRoleSel'] = implode(",", $data['userEditRoleSel']);
            $data['role_id'] = $data['userEditRoleSel'];
            try {
                $this->validate($data, 'Admin');
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return to_assign(1, $e->getError());
            }
            $data['password'] = $data['newpassword'];
            $result = AdminModel::strict(false)->field(true)->insertGetId($data);
            // 新增用户所属角色
            $role_id = $data['role_id'];
            foreach ($role_id as $value) {
                $dataset[] = ['uid' => $result, 'group_id' => $value];
            }
            AuthGroupAccess::insertAll($dataset);

            return to_assign(0, '添加账号成功！');

        }
        return $data;
    }

    /**
     * 删除账号
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
            $idsdata = AdminModel::whereIn('id', $ids)->select();
            foreach($idsdata as $value){
                if ($value['id'] == getAdminId()) {
                    return to_assign(1, '自己账号禁止删除！');
                } elseif ($value['is_admin'] == 1) {
                    return to_assign(1, '超级管理员禁止删除！');
                }
            }
            $result = AdminModel::destroy($ids);

            if ($result == true) {
                // 删除用户所属角色
                AuthGroupAccess::whereIn('uid', $ids)->delete();
                return to_assign(0, '账号删除成功！');
            } else {
                return to_assign(1, '账号删除失败！');
            }
        }
    }
}