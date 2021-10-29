<?php

namespace app\api\controller;

use app\admin\model\LoopImg;
use think\Controller;

class Loopimage extends Controller
{
    public function get()
    {
        $img = new LoopImg();
        $data = $img->where('status', 1)->select();
        if (!empty($data)) {
            foreach ($data as $temp) {
                $temp['image'] = 'www.baidu.com' . str_replace('\\', '/', $temp['image']);
            }
            return json([
                'code' => 0,
                'msg' => '',
                'data' => $data
            ]);
        } else {
            return json([
                'code' => -1,
                'msg' => '请求参数不正确',
                'data' => ''
            ]);
        }
    }
}
