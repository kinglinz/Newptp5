<?php
    namespace app\admin\model;
    use think\Model;

    class CourseInfo extends Model{
        public function course(){
            return $this->belongsTo('Course');
        }
    }