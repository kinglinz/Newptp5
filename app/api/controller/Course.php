<?php

namespace app\api\controller;



use app\jk\controller\Login;
use app\admin\model\Course as courseModel;
use app\admin\model\CourseInfo;
use app\admin\model\BuyCourse as BuyPmodel;
use app\admin\model\CourseCate as CourseCateModel;
use app\admin\model\CoursePlan as CoursePlanModel;
use app\admin\model\Learn as LearnModel;
use app\admin\model\Plan as PlanModel;
use app\admin\model\PlanCate as PlanCateModel;
use think\Db;
use think\Request;



class Course extends Login
{
    //获取课程列表
    public function getCourseList()
    {
        //$uid = $this->token();
        $coursecate = new CourseCateModel();
        $cm = new courseModel();
        $data1 = array();
        $data2 = array();
        $catedata = $coursecate->select();
        $cmdata = $cm->field('id,name,num,image,cate_id')->select();
        for ($i = 0; $i < count($catedata); $i++) {
            for ($j = 0; $j < count($cmdata); $j++) {
                if ($cmdata[$j]['cate_id'] == $catedata[$i]['id']) {
                    array_push($data1, $cmdata[$j]);
                }
            }
            $data2[$i][$catedata[$i]['name']] = $data1;
            $data1 = [];
        }
        if (!empty($data2)) {
            return json([
                'code' => 0,
                'msg' => "",
                'data' => $data2
            ]);
        } else {
            return json([
                'code' => -1,
                'msg' => "没有此数据",
                'data' => ""
            ]);
        }

        //return json(collection($c->all())->toArray());
    }

    //获取课程详细信息
    public function getCourseInfoList()
    {
        $cid = $this->request->get('cid');
        if (!isset($cid) || empty($cid)) {
            return json(['code' => -1, 'msg' => '参数不正确', 'data' => '']);
        }
        $cmodel = new courseModel();
        $d = $cmodel->get($cid);
        $infoModel = new CourseInfo();
        $ret = $infoModel->field('id,name,image,price')->where('course_id', $cid)->order('name')->select();
        if (!empty($ret)) {
            $ret['descrption'] = $d['descrption'];
            return json([
                'code' => 0,
                'msg' => "",
                'data' => $ret
            ]);
            //return json($data);
        } else {
            return json([
                'code' => -1,
                'msg' => "没有此数据",
                'data' => ""
            ]);
        }
    }


    public function getcourseinfo()
    {
        $cid = $this->request->post('cid');
        if (!isset($cid) || empty($cid)) {
            return json(['code' => -1, 'msg' => '参数不正确', 'data' => '']);
        }
        //$uid = $this->token();
        $uid = 88;
        $infoModel = new CourseInfo();
        $learn = new LearnModel();

        //获取课程
        $courseinfo = Db::query("SELECT * FROM tplay_course_info WHERE id=?", [$cid]);
        //dump($courseinfo);die;
        if (!empty($courseinfo)) {
            $courseinfo[0]['image'] = 'www.baidu.com' . str_replace('\\', '/', $courseinfo[0]['image']);
            $courseinfo[0]['video'] = 'www.baidu.com' . str_replace('\\', '/', $courseinfo[0]['video']);
            if ($courseinfo[0]['is_toll'] == 1) { //收费     
                //查看是否购买
                $ret = $this->is_toll($uid, $cid, 1); //Db::query("SELECT user_id FROM tplay_buy_course WHERE user_id=? AND course_info_id=?", [$uid, $cid]);
                if ($ret) {
                    return json([
                        'code' => 0,
                        'msg' => "",
                        'data' => $courseinfo
                    ]);
                } else {
                    return json(['code' => -1, 'msg' => '需要购买', 'data' => '']);
                }
            } else { //不收费
                $ret = $this->is_toll($uid, $cid, 1); //Db::query('SELECT id FROM tplay_buy_course WHERE user_id=? AND course_info_id=?', [$uid, $cid]);
                if (!$ret) {
                    //加入
                    Db::execute('INSERT INTO tplay_buy_course (course_info_id,`user_id`,create_time,score,`status`)VALUES(
                        ?,?,?,?,?)', [$cid, $uid, time(), 0, 0]);
                }
                //加入学习计划
                $ret = Db::query("SELECT id FROM tplay_learn WHERE user_id=? AND course_info_id=?", [$uid, $cid]);
                if (empty($ret)) {
                    $learn->course_info_id = $cid;
                    $learn->user_id = $uid;
                    $learn->allowField(true)->save();
                }
                return json([
                    'code' => 0,
                    'msg' => "",
                    'data' => $courseinfo
                ]);
            }
        } else {
            return json(['code' => -1, 'msg' => '没有数据', 'data' => '']);
        }
    }



    public function getplanlist()
    {
        //$this->token();
        $planModel = new PlanModel();
        $pcateModel = new PlanCateModel();
        $data = array();
        $plan = $planModel->select();
        $pcate = $pcateModel->select();

        if (empty($plan) || empty($pcate))
            return json(['code' => -1, 'msg' => '没有数据', 'data' => '']);
        foreach ($plan as $temp) {
            $temp['image'] = 'www.baidu.com' . str_replace('\\', '/', $temp['image']);
        }
        for ($i = 0; $i < count($pcate); $i++) {
            for ($j = 0; $j < count($plan); $j++) {
                if ($plan[$j]['cate_id'] == $pcate[$i]['id']) {
                    $data[$pcate[$i]['name']] = $plan[$j];
                }
            }
        }
        if (!empty($data)) {
            return json([
                'code' => 0,
                'msg' => '',
                'data' => $data
            ]);
        } else {
            return json([
                'code' => -1,
                'msg' => '没有数据',
                'data' => ''
            ]);
        }
    }

    public function getplan(Request $request)
    {
        $cid = $request->get('cid');
        $uid = 88; //$this->token();
        if (empty($cid) || empty($uid)) {
            return json(['code' => -1, 'msg' => '没有数据', 'data' => '']);
        }
        $ret = $this->is_toll($uid, $cid, 1);
        if (!empty($ret)) {
            $coursePlanModel = new CoursePlanModel();
            $ret = $coursePlanModel->get($cid);
            if (!empty($ret)) {
                return json([
                    'code' => 0,
                    'msg' => '',
                    'data' => $ret
                ]);
            } else {
                return json([
                    'code' => -1,
                    'msg' => '没有数据',
                    'data' => ''
                ]);
            }
        } else {
            return json([
                'code' => -1,
                'msg' => '没有数据',
                'data' => ''
            ]);
        }
    }




    //培训报名资格检测 
    public function checkapply($uid)
    {
        //$cid = $request->get('cid');
        //$uid = 88; //$this->token();
        $ret1 = Db::query("SELECT id FROM tplay_course_info");
        $ret2 = Db::query("SELECT id,`status` FROM tplay_buy_course WHERE user_id=?", [$uid]);

        if (empty($ret1) || empty($ret2)) {
            return [
                'code' => -1,
                'msg' => '数据请求出错',
                'data' => ''
            ];
        }

        if (count($ret1) == count($ret2)) {
            foreach ($ret2 as $tmp) {
                if ($tmp['status'] == 0) {
                    return [
                        'code' => -1,
                        'msg' => '您还没有完成线上课程',
                        'data' => ''
                    ];
                }
            }
        } else {
            return [
                'code' => -1,
                'msg' => '您还没有完成线上课程',
                'data' => ''
            ];
        }


        return [
            'code' => 0,
            'msg' => '',
            'data' => ''
        ];
    }


    public function apply()
    {
        $uid = 88; //$this->token();
        $planid = 1; //检测是否购买
        $check = Db::query("SELECT * FROM tplay_buy_plan WHERE user_id=? AND plan_id=?", [$uid, $planid]);
        if (!empty($check)) {
            $data = Db::query("SELECT an.title,p.name,an.class,an.location,an.class_hour
            FROM tplay_course_plan as an INNER JOIN tplay_plan as p ON(an.plan_id=p.id)");
            if (!empty($data)) {
                return json([
                    'code' => 0,
                    'msg' => '',
                    'data' => $data
                ]);
            } else {
                return json([
                    'code' => 1,
                    'msg' => '内部错误',
                    'data' => ''
                ]);
            }
        }
        $result = $this->checkapply($uid);
        if ($result['code'] == 0) {
            //需要收费
            dump('收费');
            $ret = Db::query("INSERT INTO tplay_buy_plan (plan_id,`user_id`,create_time,score_o,`status`,score_r)
                    VALUES(?,?,?,?,?,?)", [$planid, $uid, time(), 0, 0, 0]);
            if (empty($ret)) {
                return json([
                    'code' => 1,
                    'msg' => '内部错误',
                    'data' => ''
                ]);
            }
            $data = Db::query("SELECT an.title,p.name,an.class,an.location,an.class_hour
                    FROM tplay_course_plan as an INNER JOIN tplay_plan as p ON(an.plan_id=p.id)");
            if (!empty($data)) {
                return json([
                    'code' => 0,
                    'msg' => '',
                    'data' => $data
                ]);
            } else {
                return json([
                    'code' => 1,
                    'msg' => '内部错误',
                    'data' => ''
                ]);
            }
        } else {
            return json($result);
        }
    }

    // //获取培训
    // public function getapply()
    // {
    //     $token = $this->request->cookie('token');
    //     $uid = $this->token($token);

    //     $result = Db::query("
    //         SELECT c.name,c.image,c.num,c.descrption FROM tplay_course as c INNER JOIN tplay_buycourse_profile as b
    //         on(c.id = b.course_id) WHERE b.user_id = $uid AND  c.is_online = 1;
    //     ");
    //     return json($result);
    // }


    // public function getscroe()
    // {
    //     $token = $this->request->cookie('token');
    //     $uid = $this->token($token);

    //     $result1 = Db::query("
    //         SELECT c.name,c.image,c.num,c.descrption,b.score FROM tplay_course as c INNER JOIN tplay_buycourse_profile as b
    //         on(c.id = b.course_id) WHERE b.user_id = $uid;
    //     ");

    //     $result2 = Db::query("
    //     SELECT c.name,c.image,c.num,c.descrption,b.score FROM tplay_course as c INNER JOIN tplay_buycourse as b
    //     on(c.id = b.course_id) WHERE b.user_id = $uid;
    // ");
    //     return json(['data1' => $result1, 'data2' => $result2]);
    // }

    /**
     * 检测用户是否购买
     * @param  $uid 用id
     * @param  $cid 课程id
     * @return bool
     */
    public function is_toll($uid, $cid, $status)
    {
        if ($status == 1) {
            $retsult = Db::name('buy_course')->field('user_id')->where('user_id', $uid)->where('course_info_id', $cid)->find();
            //dump( $retsult);die;
            if (!empty($retsult) && $retsult['user_id'] == $uid) {
                return true;
            } else {
                return false;
            }
        } elseif ($status == 0) {
            $retsult = Db::name('buy_plan')->field('user_id')->where('user_id', $uid)->where('plan_id', $cid)->find();
            if (!empty($retsult) && $retsult['user_id'] == $uid) {
                return true;
            } else {
                return false;
            }
        }
    }
}
