<?php

namespace app\api\controller;

use think\Controller;
use think\Request;
use app\admin\model\Exams as ExamsModel;

class Exams extends Controller
{
    public function getExams(Request $request){
        $id = $request->param('id');
        $examsmodel = new ExamsModel();
        $data = $examsmodel->where('course_id',$id)->select();
        if(!empty($data)){
            return tojson($data);
        }else{
            return tojson();
        }      
    }
}
