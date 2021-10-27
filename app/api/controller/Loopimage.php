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
        if(!empty($data))
            return tojson($data);
        else 
            return tojson();
    }
}
