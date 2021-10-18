<?php
namespace app\jk\controller;
use think\Controller;
use think\Db;

class Login extends  Controller
{

    //测试加密token
    public function encode($uid){  
        return createToken($uid);
    }

    public function decode(){
        $uid = $this->request->cookie('token');
        if($uid){
            return $this->token($uid);
        }
    }

  


    /*
    * 授权登录
    * @param code
    * @param username
    * @param image
    * */
    public function login(){
        if(!$this->request->isPost())return json(['status'=>'error','msg'=>'非法请求']);
        $code=$this->request->param('code','');
        if($code=='')return json(['status'=>'error','msg'=>'缺少参数']);

        $config['appid'] = 'wxc4c9eb9b2452e386';
        $config['secret'] = 'd2f8105a243e55196edef4805c8af04f';
        $username = $this->request->param('username');
        $username = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $username);
        $image = $this->request->param('image');
        $sex = $this->request->param('sex');

        $url="https://api.weixin.qq.com/sns/jscode2session?appid=".$config['appid']."&secret=".$config['secret']."&js_code=".$code."&grant_type=authorization_code";
        $html = file_get_contents($url);
        $arr = json_decode($html,true);

        if(empty($arr['openid'])){
            return json(['status'=>'error','msg'=>'授权登录失败']);
        }

        if (null == Db('user')->where(['openid'=>$arr['openid']])->find()) {
            $data=[
                'openid' => $arr['openid'],
                'username'=>$username,
                'image'=>$image,
                'shop_name'=>$username.'的小店',
                'is_show'=>'true',
                'sex'=>$sex,
                'create_time'=>time()
            ];
            $user_id = DB::name('user')->insertgetId($data);
        }else{
            $user_id = DB::name('user')->where('openid',$arr['openid'])->value('id');
        }

        if($user_id){
            $data = [
                'status'=>'success',
                'token'=>createToken($user_id),
                'msg'=>'微信授权登录成功',
            ];
            return json($data);
        }else{
            return json(['status'=>'error','msg'=>'授权登录失败']);
        }
    }

    /*
    * 获取手机号码
    * @param  string  $encryptedData 在小程序中获取的encryptedData
    * @param  string  $iv 在小程序中获取的iv
    * @return array 解密后的数组
    *
    * */
    public function getphone()
    {
        $get = $this->request->param();
        $param['appid'] = 'wxc4c9eb9b2452e386';    //小程序id
        $param['secret'] = 'd2f8105a243e55196edef4805c8af04f';    //小程序密钥
        $param['js_code'] = define_str_replace($get['code']);

        $param['grant_type'] = 'authorization_code';
        $http_key = httpCurl('https://api.weixin.qq.com/sns/jscode2session', $param, 'GET');
        $session_key = json_decode($http_key, true);

        if (!empty($session_key['session_key'])) {
            $appid = $param['appid'];
            $encrypteData = urldecode($get['encrypteData']);
            $iv = define_str_replace($get['iv']);
            $errCode = decryptData($appid, $session_key['session_key'], $encrypteData, $iv);
            if (DB::name('user')->where(['open_id' => $session_key['openid']])->find()) {
                DB::name('user')->where('open_id', $session_key['openid'])->update(['publishtime'=>time(),'phone' => $errCode['phoneNumber']]);
                return json_success(1,'success',['phone'=>$errCode['phoneNumber']]);
            }else{
                return json_error(0,'error','获取失败');
            }
        }

    }
    
    /*解密token
    *   放到公共类内
    */
    protected function token($arr){
        //$arr = $this->request->header('token');
        //$arr="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJKb3V6ZXl1IiwiYXVkIjoiIiwiaWF0IjoxNjM0Mjk2NDAxLCJuYmYiOjE2MzQyOTY0MDQsImV4cCI6MTAwMzQzNTA3MjYsInVpZCI6ImFkbWluIn0.wdYQWd8RxEWd_ZR9IxF0B9WVmVbFprOABu3z2dLab3M";
     
        if($arr==null){
            http_response_code(401);
            exit(json_error(2,'登录错误',[]));
        }
        $token = json_decode(checkToken($arr),true);
        if($token['code']!=='200'){
            http_response_code(401);
            exit(json_error(2,'登录错误',$token));
        }
        $tokens=check($arr);
        return json_decode(json_encode($tokens),true)['uid'];
    }


}