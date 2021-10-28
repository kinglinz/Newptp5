<?php

namespace app\api\controller;

use app\user\model\User as userModel;

use app\user\model\Profile as ProfileModel;
use think\Cookie;
use think\Db;

class User extends \app\jk\controller\Login
{
    /**
     * 修改注册个人信息
     */
    public function Profile()
    {
        if ($this->request->isPost()) {
            //更改注册资料
            $post = $this->request->post();
            $userid = $this->token($this->request->cookie('token'));
            $usermodel = userModel::get($userid);
            if ($post) {
                $promodel = new ProfileModel();
                $ide = $promodel->where(['identity' => $post['identity']])->find();
                if ($ide) {
                    return json(['type' => 'error', 'errormsg' => '证件已被注册'], 401);
                }
                $validate = new \think\Validate([
                    ['name', 'require', '请填写姓名'],
                    ['addr', 'require', '请填写地址'],
                    ['idpic', 'require', '请上传照片'],
                    ['edu', 'require', '请填写学历'],
                    ['identity', 'require', '请填写证件信息'],
                ]);

                if (!$validate->check($post)) {
                    return json(['type' => 'error', 'msg' => $validate->getError()]);
                }
                $promodel->name = $post['name'];
                $promodel->addr = $post['addr'];
                $promodel->idpic = $post['idpic'];
                $promodel->edu = $post['edu'];
                $promodel->identity = $post['identity'];
                $promodel->phone = $post['phone'];
                $ret = $usermodel->profile()->save($promodel);
                
                if ($ret) {
                    return json(['type' => 'success', 'msg' => '修改成功' ], 200);
                }
            } else {
                return json(['type' => 'error', 'msg' => '参数不正确'], 401);
            }
        } else {
            //获取资料
            $token = $this->request->cookie('token');
            $id = $this->token($token);
            $user = new userModel();
            return json($user->get($id)->profile()->find());
        }
    }


    


    
    public function reg()
    {
        if ($this->request->param('username')) {
            $user = new userModel();
            if ($user->allowField(true)->save($this->request->get())) {
                 $enuid = $this->encode($user->id);
                if ($user->isUpdate(true)->allowField(true)->save(['openid' => $enuid])) {
                    $promodel = new ProfileModel();
                    $promodel->name ='name';
                    $promodel->addr = 'addr';
                    $promodel->idpic = 'idpic';
                    $promodel->edu = 'edu';
                    $promodel->identity = 'identity';
                    $promodel->phone = 'phone';

                    $ret = $user->profile()->save($promodel);
                    $pid = $promodel->getLastInsID();            
                    $t = $user->allowField(true)->save(['profile_id' => $pid],['id' => $user->id]);          
                    if($t && $ret){                       
                        Cookie::set('token', $enuid);
                        return json(['code' => 200, 'msg' => '注测成功']);
                    }else{
                        return json(['1' => $t,'2' => $user->getError()]);
                    }
                  
                } else {
                    return json(['code' => 400, 'msg' => '注测失败', 'error' =>
                    $user->getLastSql(), 'enuid' => $user->getError()]);
                }
            }
        } else {
            return json(['code' => -1, 'msg' => 'id不正确']);
        }
    }

    public function log()
    {
        $get = $this->request->get();
        $username = $get['username'];
        $password = $get['password'];
        $ret = Db::query(
            "SELECT openid from tplay_user WHERE username='$username' AND password='$password'"
        );
        if ($ret) {
            Cookie::set('token', $ret[0]['openid'], 3600);
            return json(['code' => 200, 'msg' => '登录成功']);
        } else {
            Cookie::clear();
            return json(['code' => 200, 'msg' => '登录失败', 'jump' => url('reg')]);
        }
    }
}
