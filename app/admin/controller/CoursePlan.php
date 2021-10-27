<?php

namespace app\admin\controller;


use app\admin\model\CoursePlan as CoursePlanModel;
use app\admin\model\Plan as PlanModel;
use think\Request;
//培训安排
class CoursePlan extends Permissions
{
    public function index()
    {
        $model = new CoursePlanModel();
        $data = $model->select();
        $this->assign("course", $data);
        return $this->fetch();
    }

    public function publish(Request $request)
    {

        $id = $request->has('id') ? $request->param('id', 0, 'intval') : 0;
        if ($id > 0) {
            if ($request->isPost()) {
                $post = $request->post();
                $validate = new  \think\Validate([
                    ['title','require','标题不能为空'],
                    ['class','require','班级不能为空'],
                    ['course_id','require','课程分类不能为空'],
                    ['location','require','位置不能为空'],
                    ['class_hour','require','课时不能为空'],
                    ['start_time','require','时间不能为空'],
                ]);

                if(!$validate->check($post)){
                    return $this->error($validate->getError());
                }
                $model = new CoursePlanModel();
                if($model->get($id)->allowField(true)->save($post)){
                    return $this->success('修改成功','admin\course_plan\index');
                }else{
                    return $this->error('修改失败','admin\course_plan\index');
                }
            } else {
                $cmodel = new PlanModel();
                $pmodel = new CoursePlanModel();
                $plan = $pmodel->get($id);
                $data = $cmodel->select();
                $this->assign("info",$plan);
                $this->assign("cate",$data);
                dump($plan['start_time']);
                return $this->fetch();
            }
        } else {
            if ($request->isPost()) {
                $post = $request->post();
                $validate = new  \think\Validate([
                    ['title','require','标题不能为空'],
                    ['class','require','班级不能为空'],
                    ['plan_id','require','课程分类不能为空'],
                    ['location','require','位置不能为空'],
                    ['class_hour','require','课时不能为空'],
                    ['start_time','require','时间不能为空'],
                ]);

                if(!$validate->check($post)){
                    return $this->error($validate->getError());
                }
                $model = new CoursePlanModel();
                if($model->allowField(true)->save($post)){
                    return $this->success('添加成功','admin\course_plan\index');
                }else{
                    return $this->error('添加失败','admin\course_plan\index');
                }
            } else {
                $cmodel = new PlanModel();
                $pmodel = new CoursePlanModel();
                $plan = $pmodel->get($id);
                $data = $cmodel->select();
                $this->assign("info",$plan);
                $this->assign("cate",$data);
                return $this->fetch();
            }
        }
    }

    public function delete(Request $request){
        $id = $request->param('id');
        $model = new CoursePlanModel();
        if($model->get($id)->delete()){
            return $this->success('删除成功','admin\course_plan\index');
        }else{
            return $this->error('删除失败 ','admin\course_plan\index');
        }
    }
}
