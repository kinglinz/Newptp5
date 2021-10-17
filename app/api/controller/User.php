<?php
    namespace app\api\controller;
    use app\user\model\User as userModel;
    use think\Db;
    use app\user\model\Profile as ProfileModel;


class User extends \app\jk\controller\Login{
        public function login(){
            parent::login();
        }


        public function getProfile(){     
            $id = $this->token();
            $user = new userModel();
            return json($user->get($id)->profile()->select()[0]);

        }
        public function register(){
            if($this->request->isPost()){
                $post = $this->request->post();
                $ide = ProfileModel::where(['identity' => $post['identity']])->find();
                if($ide){
                    return json(['code'=>420,'errormsg'=>'证件已被注册']);
                }
                $validate = new \think\Validate([
                    ['name','require','请填写姓名'],
                    ['addr','require','请填写地址'],
                    ['idpic','require','请上传照片'],
                    ['edu','require','请填写学历'],
                    ['identity','require','请填写证件信息'],
                ]);

                if(!$validate->check($post)){
                    return json(['code'=>421,'errormsg'=>$validate->getError()]);
                }
                $promodel = new ProfileModel();
                if($promodel->allowField(true)->save()){
                    return json(['code'=>200,'msg'=>'注册成功']);
                }
            }else{
                return json(['code'=>400,'msg'=>'not found']);
            }        
        }
    }