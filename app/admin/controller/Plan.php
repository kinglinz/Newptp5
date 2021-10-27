<?php

namespace app\admin\controller;
use think\Db;
use think\Session;

use app\admin\model\Plan as PlanModel;
use app\admin\model\PlanCate as PlanCateModel;

class Plan extends Permissions
{
    public function index()
    {

        $model = new PlanModel();
        $post = $this->request->param();

        if (isset($post['is_top']) and ($post['is_top'] == 1 or $post['is_top'] === '0')) {
            $where['is_top'] = $post['is_top'];
        }

        if (isset($post['is_toll']) and ($post['is_toll'] == 1 or $post['is_toll'] === '0')) {
            $where['is_toll'] = $post['is_toll'];
        }

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
        $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
        $planModel = new PlanModel();
        if ($id > 0) {
            if ($this->request->isPost()) { //修改
                $post = $this->request->post();
                $check =  [
                    ['name', 'require', '标题不能为空'],
                    ['num', 'require', '请设置课时'],
                    ['is_toll', 'require', '设置收费'],
                    ['image', 'require', '请上传缩略图'],
                    ['num', 'number', '课时必须是数字']
                ];
                if ($post['is_toll'] == 1) {
                    array_push($check, ['price', 'require', '请填写金额']);
                }
                $validate = new \think\Validate($check);

                if (!$validate->check($post)) {
                    return $this->error($validate->getError());
                }
                if (false ==  $planModel->allowField(true)->save($post, ['id' => $id])) {
                    return $this->error('修改失败');
                } else {
                    return $this->success('修改成功', 'admin/plan/index');
                }
            } else {

                $momdel = new PlanCateModel();
                $data = $momdel->all();
                $this->assign("info",$data);
                $result = $planModel->get($id)->toArray();
                $this->assign('course', $result);
                return $this->fetch();
            }
        } else {

            if ($this->request->isPost()) { //新增
                //获取提交参数
                $post = $this->request->post();
                $check =  [
                    ['name', 'require', '标题不能为空'],
                    ['num', 'require', '请设置课时'],
                    ['is_toll', 'require', '设置收费'],
                    ['image', 'require', '请上传缩略图'],
                    ['num', 'number', '课时必须是数字'],
                    ['price', 'number', '价格必须是数字']
                ];
                $validate = new \think\Validate($check);
                $post['admin_id'] = Session::get('admin');
                if (!$validate->check($post)) {
                    return $this->error($validate->getError());
                }
                if (false ==  $planModel->allowField(true)->save($post)) {
                    return $this->error('添加失败');
                } else {
                    return $this->success('添加成功', 'admin/plan/index');
                }
            } else {
                $momdel = new PlanCateModel();
                $data = $momdel->all();
                $this->assign("info",$data);
                return $this->fetch();
            }
        }
       
    }



    public function is_top()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            if (false == Db::name('plan')->where('id', $post['id'])->update(['is_top' => $post['is_top']])) {
                return $this->error('设置失败');
            } else {
                return $this->success('设置成功', 'admin/plan/index');
            }
        }
    }

    //图片上传
    public function upload($module = 'admin', $use = 'admin_plan')
    {
        if ($this->request->file('file')) {
            $file = $this->request->file('file');
        } else {
            $res['code'] = 1;
            $res['msg'] = '没有上传文件';
            return json($res);
        }
        $module = $this->request->has('module') ? $this->request->param('module') : $module; //模块
        $web_config = Db::name('webconfig')->where('web', 'web')->find();
        $info = $file->validate(['size' => $web_config['file_size'] * 1024, 'ext' => $web_config['file_type']])->rule('date')->move(ROOT_PATH . 'public' . DS . 'uploads' . DS . $module . DS . $use);
        if ($info) {
            $res['image'] = DS . 'uploads' . DS . $module . DS . $use . DS . $info->getSaveName();
            $res['code'] = 2;
            return json($res);
        } else {
            // 上传失败获取错误信息
            return $this->error('上传失败：' . $file->getError());
        }
    }

    public function delete()
    {
        $course = PlanModel::get($this->request->get('id'));
        if ($course->delete()) {
            return $this->success('删除成功', 'admin/plan/index');
        } else {
            return $this->error('删除失败', 'admin/plan/index');
        }
    }
}
