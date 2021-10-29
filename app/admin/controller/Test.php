<?php

namespace app\admin\controller;

use app\index\controller\User;
use think\Controller;
use app\user\model\Buycourse as B;
use app\user\model\User as UserModel;
use app\admin\model\CourseInfo as CourseInfoModel;
use app\admin\model\Course as CourseModel;
use app\admin\model\Exams as ExamsModel;
class Test extends Controller
{


    public function index()
    {

        $post = $this->request->post();
        if (isset($post['keywords']) && !empty($post['keywords'])) {
            $where['descrption'] =  ["like", "%" . $post['keywords'] . "%"];
        }

        if (isset($post['course_id']) && !empty($post['course_id'])) {
            $where['course_id'] = $post['course_id'];
        }

        if (isset($post['exam'])) {
            if (!empty($post['exam']) || $post['exam'] == 0)
                $where['status'] = $post['exam'];
        }
        $courseModel = new CourseModel();
        $data1 = $courseModel->all();
        $exModel = new ExamsModel();
        $data = empty($where) ? $exModel->field('id,descrption,status,course_id')->paginate(20) :
            $exModel->field('id,descrption,status,course_id')->where($where)->paginate(20);
        $this->assign("data1", $data1);
        $this->assign("data", $data);
        return $this->fetch();
    }
}
