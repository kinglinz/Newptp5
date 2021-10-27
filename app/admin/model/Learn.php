<?php

    namespace app\admin\model;

    use think\Model;

    class Learn extends Model{
        protected $insert = ['status' => 0, 'exam' => 0];

        public function user(){
            return $this->belongsTo('User');
        }

        public function courseinfo(){
            return $this->belongsTo('CourseInfo');
        }
    }