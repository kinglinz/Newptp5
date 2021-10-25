<?php

namespace app\admin\controller;


use app\admin\model\CoursePlan as CoursePlanModel;
use think\Request;

class CoursePlan extends Permissions
{
    public function index()
    {
            $model = new CoursePlanModel();
            $data = $model->select();
            $this->assign("course",$data);
            return $this->fetch();
    }

    public function publish(Request $request){
        $id = $request->has('id') ? $request->param('id',0,'intval') : 0;
        if($id > 0){

        }else{
            if($request->isPost()){

            }else{
                return $this->fetch();
            }
        }
    }
}