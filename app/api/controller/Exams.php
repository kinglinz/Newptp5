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
        if(!isset($cid) || empty($cid)){
            return json([
                'code' => -1,
                'msg' => '请求参数不正确',
                'data' =>''
            ]);
        }
        $uid = 88;
        $examsmodel = new ExamsModel();
        $ret = Db::query("SELECT `status` FROM tplay_learn WHERE user_id=? AND course_info_id=? LIMIT 1",[$uid,$cid]);
        if(!empty($ret)){
            if($ret[0]['status'] == 0){
                return json([
                    'code' => -1,
                    'msg' => '请先观看完视频',
                    'data' =>''
                ]);
            }else{
                $data = $examsmodel->field('id,descrption,a,b,c,d,status,da')->where('course_info_id',$cid)->select();
              // dump(Db::getLastSql());
                if(!empty($data)){
                    $result = Db::name('examing')->where('uid',$uid)->find();
                    if(empty($result)){
                        $exa='';
                        foreach($data as $temp){
                            $exa .= $temp['id'].":".$temp['da'].",";
                            $temp['da']='';
                        }
                        Db::name('examing')->insert(['uid'=>$uid,'create_time'=>time(),'exa'=>$exa]);
                    }
                    //去除答案
                    foreach($data as $temp){
                        $temp['da']='';
                    }

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
        }else{
            return json([
                'code' => -1,
                'msg' => '没有数据',
                'data' =>''
            ]);
        }
    }
}
