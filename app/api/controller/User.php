<?php

namespace app\api\controller;

use app\user\model\User as userModel;

use app\user\model\Profile as ProfileModel;
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
            $userid = 98;//$this->token($this->request->cookie('token'));
            $usermodel = userModel::get($userid);
            if (!empty($post) && !empty($usermodel)) {
                $promodel = new ProfileModel();
                $ide = $promodel->where(['identity' => $post['identity']])->find();
                if ($ide) {
                    return json(['code' => -1, 'msg' => '证件已被注册','data'=>'']);
                }
                $validate = new \think\Validate([
                    ['name', 'require', '请填写姓名'],
                    ['addr', 'require', '请填写地址'],
                    ['idpic', 'require', '请上传照片'],
                    ['edu', 'require', '请填写学历'],
                    ['identity', 'require', '请填写证件信息'],
                ]);

                if (!$validate->check($post)) {
                    return json(['code' => -1, 'msg' => $validate->getError(),'data'=>'']);
                }
                $promodel->name = $post['name'];
                $promodel->addr = $post['addr'];
                $promodel->idpic = $post['idpic'];
                $promodel->edu = $post['edu'];
                $promodel->identity = $post['identity'];
                $promodel->phone = $post['phone'];
                $ret = $usermodel->profile()->save($promodel);
                
                if ($ret) {
                    return json(['code' => 0, 'msg' =>'' ,'data'=>'']);
                }
            } else {
                return json(['code' => -1, 'msg' =>'参数不正确','data'=>'']);
            }
        } else {
            //获取资料
            //$token = $this->request->cookie('token');
            $uid = 88; //$this->token();
            $usermodel = new userModel();
            $user = $usermodel->get($uid);
            if(!empty($user)){
                $user['idpic'] = 'www.baidu.com' . str_replace('\\', '/', $user['idpic']);
                return json($user->profile);
            } else{
                return json([
                    'code' => -1,
                    'msg' => '请先登录',
                    'data' => ''
                ]);
            }
            
        }
    }


    


    
    // public function reg()
    // {
    //     if ($this->request->param('username')) {
    //         $user = new userModel();
    //         if ($user->allowField(true)->save($this->request->get())) {
    //              $enuid = $this->encode($user->id);
    //             if ($user->isUpdate(true)->allowField(true)->save(['openid' => $enuid])) {
    //                 $promodel = new ProfileModel();
    //                 $promodel->name ='name';
    //                 $promodel->addr = 'addr';
    //                 $promodel->idpic = 'idpic';
    //                 $promodel->edu = 'edu';
    //                 $promodel->identity = 'identity';
    //                 $promodel->phone = 'phone';

    //                 $ret = $user->profile()->save($promodel);
    //                 $pid = $promodel->getLastInsID();            
    //                 $t = $user->allowField(true)->save(['profile_id' => $pid],['id' => $user->id]);          
    //                 if($t && $ret){                       
    //                     Cookie::set('token', $enuid);
    //                     return json(['code' => 200, 'msg' => '注测成功']);
    //                 }else{
    //                     return json(['1' => $t,'2' => $user->getError()]);
    //                 }
                  
    //             } else {
    //                 return json(['code' => 400, 'msg' => '注测失败', 'error' =>
    //                 $user->getLastSql(), 'enuid' => $user->getError()]);
    //             }
    //         }
    //     } else {
    //         return json(['code' => -1, 'msg' => 'id不正确']);
    //     }
    // }

    public function loginup()
    {
        return $this->login();
    }

    //我的培训
    public function getplan(){
        $uid = 88;
        $cid = 1; //培训安排
        $ret = Db::query("SELECT * FROM tplay_buy_plan WHERE `user_id`=? AND plan_id=?",[$uid,$cid]);
        if($ret){
            return json([
                'code' => 0,
                'msg' => '',
                'data' => $ret
            ]);
        }else{
            return json([
                'code' => 0,
                'msg' => '没有数据',
                'data' => ''
            ]);
        }
    }
}
