<?php

namespace app\admin\controller;

use think\Controller;
use app\service\ExportExcel;
use app\admin\model\Buycourse as BcourseModel;


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
       // dump(Db::getLastSql());
        // if(isset($where)&&!empty($where)){          
        //     // $buco = Db::query('select b.id,c.name as coursename,b.buy_time,user.name from tplay_buycourse as b 
        //     // INNER JOIN tplay_course as c ON(b.course_id=c.id) 
        //     // INNER JOIN tplay_user as u ON(b.user_id=u.id) 
        //     // INNER JOIN tplay_profile as user ON(u.profile_id=user.id) WHERE b.buy_time '.$where['create_time'])->order('create_time desc')->paginate(20);    
        // }
    
        $this->assign('info', $ret);
        return $this->fetch();


        // $buco =  BcourseModel::all();
        // $usermodel = new UserModel();
        // $course = new CourseModel();    
        //$usermodel->get($b['user_id'])->profile->name;
        // $i  = 0;
        // $tmp = array();
        // foreach($buco as $b){
        //     $tmp[$i]['id'] = $b['id'];
        //     $tmp[$i]['buy_time'] = $b['buy_time'];
        //     $tmp[$i]['name'] = $usermodel->get($b['user_id'])->profile->name;
        //     $tmp[$i]['course'] = $course->where('id',$b['course_id'])->value('name');//get($b['course_id'])->info->name;
        //     $i++;
        // }
        //select b.id,c.name,b.buy_time,user.name from tplay_buycourse as b INNER JOIN tplay_course as c ON(b.course_id=c.id) INNER JOIN tplay_user as u ON(b.user_id=u.id) INNER JOIN tplay_profile as user ON(u.profile_id=user.id);
        //select b.id,c.name,b.buy_time,u.id from tplay_buycourse as b INNER JOIN tplay_course as c ON(b.course_id=c.id) INNER JOIN tplay_user as u ON(b.user_id=u.id);
        //Db::name('buycourse b')->join('course c','b.course_id=c.id')->join('user u','b.user_id=u.id')->field('b.id,c.name,buy_time,u.name')->select();

    }

    //导出Excel
    public function exportexecl()
    {

        $execl = new ExportExcel();
        $execl->connectOrder();
    }
}
