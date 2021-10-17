<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use app\admin\model\LoopImg as LoopModel;
class Loopimg extends Controller{
    public function index(){
        $loopmodel = new LoopModel();
        $images = $loopmodel->all();
        $this->assign("images",$images);
       return $this->fetch();
    }

    public function publish()
    {
        $loopmodel = new LoopModel();
        $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
        if ($id > 0) {
           
            if ($this->request->isPost()) {
                $post = $this->request->post();
                $validate = new \think\Validate(
                    [
                        ['name', 'require', '标题不能为空'],
                        ['image', 'require', '请上传图片'],
                        
                    ]
                );
                unset($post['id']);
                if(!$validate->check($post)){
                    return $this->error($validate->getError());
                }else{
                    if($ree = $loopmodel->allowField(true)->save($post,['id' => $id])){
                        return $this->success('修改成功','admin/loopimg/index');
                    }else{                   
                        return $this->error('修改失败');
                    }
                }
            } else {
                $loopmodel = new LoopModel();
                $ret = $loopmodel->get($id);
                // $ids = $ret['course_id'];
                // $result = Db::name('course')->where('id',$ids)->find();     
                //$this->assign('info',$result);
                $this->assign('cate', $ret);
                return $this->fetch();
            }
        } else {
            if ($this->request->isPost()) {
                $post = $this->request->post();
                $validate = new \think\Validate(
                    [
                        ['name', 'require', '标题不能为空'],
                        ['image', 'require', '请上传图片'],
                  
                    ]
                );

                if(!$validate->check($post)){
                    return $this->error($validate->getError());
                }else{
                    if($loopmodel->allowField(true)->save($post)){
                        return $this->success('添加成功','admin/loopimg/index');
                    }else{
                        return $this->error('添加失败');
                    }
                }
            } else {            
                $ret = $loopmodel->all();
                $this->assign('cates', $ret);
                return $this->fetch();
            }
        }
    }

    public function is_use(){
        $post = $this->request->post();
       
        if(Db::name('loop_img')->where('id',$post['id'])->update(['status'=>$post['status']])) 
        {
            $res['code'] = 1;
            $res['msg'] = "修改成功";   
            return json($res);
        }else{
            $res['code'] = 0;
            $res['msg'] = "修改失败";
            return json($res);
        }
    }

    
    public function upload($module = 'admin', $use = 'admin_loopimg')
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

    public function delete(){
        $loopmodel = new LoopModel();
        $ret = $loopmodel->get($this->request->get('id'));
        if($ret->delete()){
            return $this->success('删除成功','admin/loopimg/index');
        }else{
            return $this->error('删除失败');
        }
    }
}