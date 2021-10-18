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
        //$result = Db::query('select * from tplay_profile INNER JOIN tplay_user ON(tplay_profile.user_id=tplay_user.id)');
        //dump($result);die;
        $userModel = new UserModel();
        $result = $userModel->order('id desc')->paginate(20);
        //dump($result);
        $this->assign('data', $result);
        return $this->fetch();
    }

    public function delete()
    {
        $userModel = new UserModel();
        //$profileModel = new ProfileModel();
        $ids = '';
        $post = $this->request->param();
        if (isset($post['id']) && !empty($post['id'])) {
            if (is_array($post['id'])) {
                //组合id
                
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

            if ($userModel->where('id', 'in', $ids)->delete()) {
                $userModel->profile()->where('id', 'in', $ids)->delete();
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

    function test(){      
       
        for($i = 1;$i<60;$i++){
            $u = new UserModel();
            dump($i);
            $u->id = $i;
            $u->password = $i;
            $u->username = $i;
            $u->profile_id = $i;
            if($u->allowField(true)->save()){
                $p = new ProfileModel();
                $p->id = $i;
                $p->name = $i;
                $p->addr = $i;
                $p->idpic = $i;
                $p->edu = $i;
                $p->job = $i;
                $p->phone = $i;
                $p->region = $i;
                $p->identity = $i;
                $p->user_id = $i;
                $u->profile()->save($p);
            }
        }
    }
}
