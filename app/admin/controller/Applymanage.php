<?php

namespace app\admin\controller;

use think\Controller;
use app\service\ExportExcel;
use app\admin\model\BuyCourse as BcourseModel;
use Exception;

//报名管理
class Applymanage extends Controller
{
    public function index()
    {
        $model = new BcourseModel();
        $post = $this->request->post();
        //搜索参数
        if (isset($post['keywords']) and !empty($post['keywords'])) {
            $where['name'] = ['like', '%' . $post['keywords'] . '%'];
        }
        if (isset($post['create_time']) and !empty($post['create_time'])) {
            $min_time = strtotime($post['create_time']);
            $max_time = $min_time + 24 * 60 * 60;
            $where['create_time'] = [['>=', $min_time], ['<=', $max_time]];
        }
        $ret = empty($where) ? $model->order('create_time desc')->paginate(20) : $model->where($where)->order('create_time desc')->paginate(20, false, ['query' => $this->request->param()]);
        //$test = $ret->toArray();

        //dump($test);die;
        $this->assign('info', $ret);
        return $this->fetch();
 }

    public function delete(){
        
    }

    //导出Excel
    public function exportexecl()
    {
      
        $execl = new ExportExcel();
        //try{
        $tmp = $execl->connectOrder();
        //}catch(Exception $e){
       //     return json(['code' => -1, 'msg' => $e->getMessage()]);
       // }     
        return $tmp;
    }
}
