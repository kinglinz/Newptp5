<?php

namespace app\api\controller;

use app\admin\model\BuyCourse as BuyCourseModel;

class MyCourse extends \app\jk\controller\Login
{
    /**
     * 获取培训课程
     * @return json  
     */
    public function get()
    {
        $token = $this->request->cookie('token');
        $uid = $this->token($token);
        $buy = new BuyCourseModel();
        $data1 = $buy->where('user_id', $uid)
            ->where('is_online', 0)
            ->select();      
        return json(['data' => $data1]);
    }
}
