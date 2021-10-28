<?php

namespace app\api\controller;

use app\admin\controller\Plan;
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

use function app\service\tojson;
use function PHPSTORM_META\map;

class Course extends Login
{
    //获取课程列表
    public function getCourseList()
    {
        $uid = 88;
        $coursecate = new CourseCateModel();
        $cm = new courseModel();
        $data1 = array();
        $data2 = array();
        $catedata = $coursecate->select();
        $cmdata = $cm->select();
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
        $cid = $this->request->get('id');
        $cmodel = new courseModel();
        $d = $cmodel->get($cid);
        $infoModel = new CourseInfo();
        $ret = $infoModel->where('course_id', $cid)->order('name')->select();
        if (!empty($ret)) {
            $data['descrption'] = $d['descrption'];
            return json([
                'code' => 0,
                'msg' => "",
                'data' => $ret
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


    public function getcourseinfo()
    {
        $cid = $this->request->get('cid');
        if (!isset($cid) || empty($cid)) {
            return json(['code' => -1, 'msg' => '参数不正确', 'data' => '']);
        }
        $uid = 98;
        $infoModel = new CourseInfo();
        $learn = new LearnModel();

        //获取课程
        $courseinfo = Db::query("SELECT * FROM tplay_course_info WHERE id=?", [$cid]);
        if (!empty($courseinfo)) {
            if ($courseinfo[0]['is_toll'] == 1) { //收费     
                //查看是否购买
                $ret = Db::query("SELECT id FROM tplay_buy_course WHERE user_id=? AND course_info_id=?", [$uid, $cid]);
                if (!empty($ret)) {
                    return json([
                        'code' => 0,
                        'msg' => "",
                        'data' => $courseinfo
                    ]);
                } else {
                    return json(['code' => -1, 'msg' => '需要购买', 'data' => '']);
                }
            } else { //不收费
                $ret = Db::query('SELECT id FROM tplay_buy_course WHERE user_id=? AND course_info_id=?', [$uid, $cid]);
                if (empty($ret)) {
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
        $planModel = new PlanModel();
        $pcateModel = new PlanCateModel();
        $data = array();
        $plan = $planModel->select();
        $pcate = $pcateModel->select();

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
        $uid = 88;

        $ret = $this->is_toll($uid, $cid, 1);
        if ($ret) {
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
    public function checkapply()
    {
        //$cid = $request->get('cid');
        $uid = 88;
        $ret1 = Db::query("SELECT id FROM tplay_course_info");
        $ret2 = Db::query("SELECT id,`status` FROM tplay_buy_course WHERE user_id=?", [$uid]);
        if (empty($ret1) || empty($ret2)) {
            return json([
                'code' => -1,
                'msg' => '数据请求出错',
                'data' => ''
            ]);
        }

        if (count($ret1) == count($ret2)) {
            foreach($ret2 as $tmp){
                if($tmp['status'] == 0){
                    return json([
                        'code' => -1,
                        'msg' => '您还没有完成线上课程',
                        'data' => ''
                    ]);
                }
            }
        } else {
            return json([
                'code' => -1,
                'msg' => '您还没有完成线上课程',
                'data' => ''
            ]);
        }

        
        return json([
            'code' => 0,
            'msg' => '',
            'data' => ''
        ]);
    }


    public function apply(){
        $uid = 77;

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

    /**
     * 检测用户是否购买
     * @param  $uid 用id
     * @param  $cid 课程id
     * @return bool
     */
    public function is_toll($uid, $cid, $status)
    {
        if ($status == 1) {
            $retsult = Db::name('buy_course')->field('id')->where('user_id', $uid)->where('course_info_id', $cid)->find();
            if (!empty($retsult)) {
                return true;
            } else {
                return false;
            }
        } elseif ($status == 0) {
            $retsult = Db::name('buy_plan')->field('id')->where('user_id', $uid)->where('plan_id', $cid)->find();
            if (!empty($retsult)) {
                return true;
            } else {
                return false;
            }
        }
    }
}
