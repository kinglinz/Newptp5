<?php
    namespace app\admin\model;

    use think\Model;

    class CoursePlan extends Model{
        protected function setStartTimeAttr($value){
            return strtotime($value);
        }

        protected function getStartTimeAttr($value){
            return date('Y-m-d',$value);
        }
    }