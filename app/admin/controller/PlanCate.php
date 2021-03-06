<?php

namespace app\admin\controller;

use app\admin\controller\Permissions;

use app\admin\model\PlanCate as PlanCateModel;



class PlanCate extends Permissions
{
    public function index()
    {
        $model = new PlanCateModel();
        $post = $this->request->param();

        if (isset($post['keywords']) and !empty($post['keywords'])) {
            $where['name'] = ['like', '%' . $post['keywords'] . '%'];
        }

        if (isset($post['create_time']) and !empty($post['create_time'])) {
            $min_time = strtotime($post['create_time']);
            $max_time = $min_time + 24 * 60 * 60;
            $where['create_time'] = [['>=', $min_time], ['<=', $max_time]];
        }

        $course = empty($where) ? $model->order('create_time desc')->paginate(20) : $model->where($where)->order('create_time desc')->paginate(20, false, ['query' => $this->request->param()]);

        $this->assign('course', $course);
        return $this->fetch();
    }

    public function publish()
    {
        $planCateModel = new PlanCateModel();
        $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;

        if ($id > 0) {
            if ($this->request->isPost()) {
                $post = $this->request->post();
                $validate = new \think\Validate(
                    [
                        ['name', 'require', '标题不能为空'],

                    ]
                );
                if (!$validate->check($post)) {
                    return $this->error($validate->getError());
                }

                if ($planCateModel->allowField(true)->save($post)) {
                    return $this->success('修改成功', 'admin/course_cate/index');
                } else {
                    return $this->error('修改失败', 'admin/course_cate/index');
                }
            } else {

                return $this->fetch();
            }
        } else {
            //新增
            //dump($this->request->post());   
            if ($this->request->isPost()) {
                $post = $this->request->post();
                $validate = new \think\Validate([
                    ['name', 'require', '标题不能为空'],
                ]);

                if (!$validate->check($post)) {
                    return $this->error($validate->getError());
                }


                if ($planCateModel->allowField(true)->save($post)) {
                    return $this->success('添加成功', 'admin/plan_cate/index');
                } else {
                    return $this->error('添加失败');
                }
            } else {
                return $this->fetch();
            }
        }
    }
}
