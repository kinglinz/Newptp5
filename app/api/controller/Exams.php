<?php

namespace app\api\controller;

use think\Controller;
use think\Request;
use app\admin\model\Exams as ExamsModel;
use think\Db;

class Exams extends Controller
{
    public function get(Request $request){
        $cid = $request->param('cid');
        $uid = 98;
        $examsmodel = new ExamsModel();
        $ret = Db::query("SELECT `status` FROM tplay_learn WHERE user_id=? AND course_info_id=? LIMIT 1",[$uid,$cid]);
        if($ret[0]['status'] == 0){
            return json([
                'code' => -1,
                'msg' => '请先观看视频',
                'data' =>''
            ]);
        }else{
            $data = $examsmodel->field('id,descrption,a,b,c,d,status')->where('course_info_id',$cid)->select();
          // dump(Db::getLastSql());
            if(!empty($data)){
                return json([
                    'code' => 0,
                    'msg' => '',
                    'data' =>$data
                ]);
            }else{
                return json([
                    'code' => -1,
                    'msg' => '没有数据',
                    'data' =>''
                ]);
            }  
        }
           
    }
}
