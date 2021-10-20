<?php

namespace app\admin\model;

use think\Model;



class Course extends Model
{

    protected $type = [
        'create_time' => 'timestamp:Y/m/d H:m:s',
    ];
    protected $auto = ['create_time'];
    protected $insert = ['is_top' => 0, 'is_online' => 1];
    // protected function setCreateTimeAttr(){
    //     return time();
    // }
        
    public function info()
    {
        return $this->hasMany('CourseInfo');
    }

    public function admin()
    {
        //关联角色表
        return $this->belongsTo('Admin');
    }
}
