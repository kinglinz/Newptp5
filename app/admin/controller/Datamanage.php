<?php

namespace app\admin\controller;

use think\Controller;
use app\user\model\User as UserModel;
use app\user\model\Profile as ProfileModel;
use think\Db;

/**
 * 用户资料控制器
 */
class Datamanage extends Controller
{
    public function index()
    {
        if ($post = $this->request->post()) {
            //添加搜索功能
        }
        $userModel = new UserModel();
        $result = $userModel->order('id desc')->paginate(20);
        $this->assign('data', $result);
        return $this->fetch();
    }

    public function delete()
    {
        $userModel = new UserModel();
        $ids = '';
        $post = $this->request->param();
        if (isset($post['id']) && !empty($post['id'])) {
            if (is_array($post['id'])) {
            //组合批量删除id              
                for ($i = 0; $i < count($post['id']); $i++) {
                    $ids .= $post['id'][$i];
                    if ($i == (count($post['id']) - 1)) {
                        break;
                    } else {
                        $ids .= ',';
                    }
                }
            }else
            {
                $ids .= $post['id'];
            }

            if ($userModel->where('id', 'in', $ids)->delete() &&
                 $userModel->profile()->where('user_id', 'in', $ids)->delete()
            ) {
                
                return json([
                    'code' => 1,
                    'msg' => '删除成功',
                    'url' => url('admin/datamanage/index'),
                ]);
            } else {
                return json([
                    'code' => 1,
                    'msg' => '删除失败',
                    'url' => url('admin/datamanage/index'),
                ]);
            }
        } else {
            return json([
                'code' => 1,
                'msg' => '参数不正确',
                'url' => url('admin/datamanage/index'),
            ]);
        }
    }

   
}
