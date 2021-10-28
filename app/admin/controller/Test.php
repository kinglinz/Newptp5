<?php   
    namespace app\admin\controller;

use app\index\controller\User;
use think\console\command\make\Controller;
use app\user\model\Buycourse as B;
use app\user\model\User as UserModel;

class Test extends Controller{
    public function index(){
        $arr1 = ['1' => 'a', '2' => 'b'];
        $arr2 = array();
        array_push($arr2,$arr1);
        dump($arr2);
    }
}