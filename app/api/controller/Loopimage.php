<?php

namespace app\api\controller;

use app\admin\model\LoopImg;
use think\Controller;

class Loopimage extends Controller
{
    public function get()
    {      
        $img = new LoopImg();
        return json($img->where('status', 1)->select());
    }
}
