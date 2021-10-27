<?php

namespace app\api\controller;

use app\jk\controller\Login;
use app\admin\model\Course as courseModel;
use app\admin\model\CourseInfo;
use app\admin\model\BuyCourse as BuyPmodel;
use app\admin\model\CourseCate as CourseCateModel;
use app\admin\model\Learn as LearnModel;
use think\Db;


use function app\service\tojson;

class Course extends Login
{
    //获取课程列表
    public function getCourseList()
    {
        $coursecate = new CourseCateModel();
        $cm = new courseModel();
        $data = array();
        $catedata = $coursecate->select();
        $cmdata = $cm->select();
        for ($i = 0; $i < count($catedata); $i++) {
            for ($j = 0; $j < count($cmdata); $j++) {
                if ($cmdata[$j]['cate_id'] == $catedata[$i]['id']) {
                    $data[$catedata[$i]['name']] = $cmdata[$j];
                }
            }
        }
        if (!empty($data)) {
             return json([
                'code' => 0,
                'msg' => "",
                'data' => $data
            ]);
        } else {
            return tojson();
        }

        //return json(collection($c->all())->toArray());
    }

    //获取课程详细信息
    public function getCourseInfoList()
    {
        $cid = $this->request->get('id');
        $uid = 98;
        $cmodel = new courseModel();
        $d = $cmodel->get($cid);
        $infoModel = new CourseInfo();
        $ret = $infoModel->where('course_id', $cid)->order('name')->select();
        if (!empty($ret)) {
            $data['descrption'] = $d['descrption'];
            return json([
                'code' => 0,
                'msg' => "",
                'data' => $data
            ]);
            return json($data);
        } else {
            return json([
                'code' => -1,
                'msg' => "没有此数据",
                'data' => ""
            ]);
        }
    }


    public function getcourse()
    {
        $cid = $this->request->get('cid');
        $uid = 98;
        $infoModel = new CourseInfo();
        $learn = new LearnModel();

        $ret = $learn->field('id')->where('user_id', $uid)->where('course_info_id', $cid)->find();
        
        if (empty($ret)) {
            $learn->course_info_id = $cid;
            $learn->user_id = $uid;
            $learn->allowField(true)->save();
        }

        $ret = $infoModel->where('id', $cid)->find();
       // dump($ret);dump($cid);
        if (!empty($ret)) {
            if ($this->is_toll($uid, $cid)) {
                return json([
                    'code' => 0,
                    'msg' => "",
                    'data' => $ret
                ]);
                
            } else {
             
                return "收费";
            }
        } else {
            return tojson();
        }
    }


    // //获取培训列表
    // public function getReal(){
    //     $coursemodel = new courseModel();
    //     $data = $coursemodel->where('is_online',0)->select();
    //     return json($data);
    // }

    //培训报名
    public function apply()
    {
        // $post = $this->request->post();
        // $token = $this->request->cookie('token');
        // dump($token);die;
        //  if ($token) {
        $uid = 1;
        $b = new BuyPmodel();
        $b->name = 'test'; //$post['name'];
        $b->user_id = $uid;
        $b->phone = '13363115555'; //$post['phone'];
        $b->course_id = 44; ///$post['course_id'];
        $b->content = '真不错'; //$post['content'];
        $ret = Db::name('buy_course')->where([
            'user_id' => $uid,
            'course_id' => 74
        ])->find();
        if (!$ret) {
            if ($b->allowField(true)->save()) {
                return json(['msg' => '成功']);
            } else {
                return Db::getLastSql();
            }
        } else {
            return json(['msg' => '不能重复报名']);
        }
        // } 
    }

    //获取培训
    public function getapply()
    {
        $token = $this->request->cookie('token');
        $uid = $this->token($token);

        $result = Db::query("
            SELECT c.name,c.image,c.num,c.descrption FROM tplay_course as c INNER JOIN tplay_buycourse_profile as b
            on(c.id = b.course_id) WHERE b.user_id = $uid AND  c.is_online = 1;
        ");
        return json($result);
    }


    public function getscroe()
    {
        $token = $this->request->cookie('token');
        $uid = $this->token($token);

        $result1 = Db::query("
            SELECT c.name,c.image,c.num,c.descrption,b.score FROM tplay_course as c INNER JOIN tplay_buycourse_profile as b
            on(c.id = b.course_id) WHERE b.user_id = $uid;
        ");

        $result2 = Db::query("
        SELECT c.name,c.image,c.num,c.descrption,b.score FROM tplay_course as c INNER JOIN tplay_buycourse as b
        on(c.id = b.course_id) WHERE b.user_id = $uid;
    ");
        return json(['data1' => $result1, 'data2' => $result2]);
    }


    public function is_toll($uid,$cid)
    {
        $retsult = Db::name('buy_course')->field('id')->where('user_id', $uid)->where('course_info_id', $cid)->find();
        if (!empty($retsult)) {
            return true;
        } else {
            return false;
        }
    }
}
