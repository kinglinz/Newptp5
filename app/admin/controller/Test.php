<?php   
    namespace app\admin\controller;

use app\index\controller\User;
use think\console\command\make\Controller;
use app\user\model\Buycourse as B;
use app\user\model\User as UserModel;

class Test extends Controller{
    public function index(){
        
        for($i=0;$i<1000;$i++){
             $user = new UserModel();
             $user->allowField(true)->save(['username'=>'admin'.$i,'password'=>'123456','profile_id'=>1]);
           
        }
    }
}