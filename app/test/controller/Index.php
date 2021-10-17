<?php
namespace app\test\controller;

use think\Controller;
//use app\jk\controller\Login;
use think\Cookie;

class Index extends Controller
{
  public function index()
  {

     $id = Request()->param('id');
    if(!Cookie::get('token')){
    Cookie::set('token',createToken($id),3600);}
    return $this->token();
  }

  protected function token(){
    $arr = $this->request->cookie('token');
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