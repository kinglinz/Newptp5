<?php

namespace app\admin\controller;

use think\Controller;
use app\admin\model\Course as CourseModel;
use app\admin\model\CourseInfo as CourseinfoModel;
use think\Db;

/**
 * 课时控制器
 */
class Courseinfo extends Controller
{
    public function index()
    {
        $model = new CourseinfoModel();
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
        $courseModel = new CourseModel();
        $courseinfoModel = new CourseinfoModel();
        $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;

        if ($id > 0) {
            if ($this->request->isPost()) {
                $post = $this->request->post();
                $validate = new \think\Validate(
                    [
                        ['name', 'require', '标题不能为空'],
                        ['image', 'require', '请上传缩略图'],
                        ['video', 'require', '视频不能为空'],
                        ['course_id', 'require', '课程分类不能为空'],
                    ]
                );
                if (!$validate->check($post)) {
                    return $this->error($validate->getError());
                }

                $courseM = $courseModel->get($post['course_id']);
                if ($post['is_toll'] == 1
                 && $courseM['is_toll'] == 0) {
                    return $this->error('对应课程是免费的，不能设置收费');
                 }
                // if ($info['is_toll'] == 1
                //  && $info->course->is_toll == 0) {
                //     return $this->error('对应课程是免费的，不能设置收费');
                // }
                $info = $courseinfoModel->get($id);
                if ($info->allowField(true)->save($post)) {
                    return $this->success('修改成功', 'admin/courseinfo/index');
                } else {
                    return $this->error('修改失败', 'admin/courseinfo/index');
                }
            } else {
                $curseinfo = $courseinfoModel->get($id);
                $curmodel = $courseModel->where('is_online',1)->select();
                $coursemodel['name'] = $curseinfo->course->name;
                $coursemodel['id'] = $curseinfo->course->id;
                $this->assign('info', $coursemodel);
                $this->assign('cate', $curseinfo);
                $this->assign('cates', $curmodel);
                return $this->fetch();
            }
        } else {
            //新增
            //dump($this->request->post());   
            if ($this->request->isPost()) {
                $post = $this->request->post();
                $validate = new \think\Validate([
                    ['name', 'require', '标题不能为空'],
                    ['image', 'require', '请上传缩略图'],
                    ['video', 'require', '视频不能为空'],
                    ['course_id', 'require', '课程分类不能为空'],
                ]);

                if (!$validate->check($post)) {
                    return $this->error($validate->getError());
                }
                $courseM = $courseModel->get($post['course_id']);
                if ($post['is_toll'] == 1
                 && $courseM['is_toll'] == 0) {
                    return $this->error('对应课程是免费的，不能设置收费');
                 }
                $info = $courseinfoModel->get($id);
                if ($post['is_toll'] == 1
                 && $info->course->is_toll == 0) {
                    return $this->error('对应课程是免费的，不能设置收费');
                 }
                if ($courseinfoModel->allowField(true)->save($post)) {
                    return $this->success('添加成功', 'admin/courseinfo/index');
                } else {
                    return $this->error('添加失败');
                }
            } else {

                $cate = new CourseModel();
                $ret = $cate->where('is_online', 1)->select();
                $this->assign('cates', $ret);
                return $this->fetch();
            }
        }
    }


    public function delete()
    {
        $id = $this->request->param('id');
        $info = new CourseinfoModel();
        if ($info = $info->get($id)) {
            if ($info->delete()) {
                return $this->success('删除成功', 'admin/courseinfo/index');
            } else {
                return $this->error('删除失败');
            }
        } else {
            return $this->error('操作失败');
        }
    }

    //上传图片
    public function upload($module = 'admin', $use = 'admin_courseinfo')
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
        $info = $file->validate(['size' => $web_config['file_size'] * 1024 * 10, 'ext' => $web_config['file_type']])->rule('date')->move(ROOT_PATH . 'public' . DS . 'uploads' . DS . $module . DS . $use);
        if ($info) {
            $data = [];
            $res['src'] = DS . 'uploads' . DS . $module . DS . $use . DS . $info->getSaveName();
            $res['code'] = 2;
            return json($res);
        } else {
            return $this->error('上传失败：' . $file->getError());
        }
    }

    //上传视频
    public function uploadVideo($module = 'admin', $use = 'courseinfo_videos')
    {
        if ($this->request->file('layuiVideo')) {
            $file = $this->request->file('layuiVideo');
        } else {
            $res['code'] = 1;
            $res['msg'] = '没有上传文件';
            return json($res);
        }
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads' . DS . $module . DS . $use);
        if ($info) {
            $res['src'] = DS . 'uploads' . DS . $module . DS . $use . DS . $info->getSaveName();
            $res['code'] = 2;
            return json($res);
        } else {
            // 上传失败获取错误信息
            return $this->error('上传失败：' . $file->getError());
        }
    }
}
