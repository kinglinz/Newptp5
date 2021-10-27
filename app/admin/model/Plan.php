<?php
    namespace app\admin\model;

    use think\Model;

    class Plan extends Model{
        
    public function admin()
    {
        //关联角色表
        return $this->belongsTo('Admin');
    }
    }