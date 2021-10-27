<?php

namespace app\admin\model;

use think\Model;



class CourseCate extends Model
{

    protected $type = [
        'create_time' => 'timestamp:Y/m/d H:m:s',
    ];
    protected $auto = ['create_time'];
     
}