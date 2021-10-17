<?php
    namespace app\index\controller;

use think\console\command\make\Controller;
use think\Request;

class User extends Controller{
    public function register(Request $request){
        $post = $request->param('post');
        if($post){
           
        }
    }
}