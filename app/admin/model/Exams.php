<?php
    namespace app\admin\model;
    use think\Model;

    class Exams extends Model{
        protected $autoWriteTimestamp = false;


        protected function course(){
            return $this->belongsTo('Course');
        }

        protected function getStatusAttr($key){
            switch($key){
                case 0 :
                    return "判断题";
                    break;
                case 1 :
                    return "单选题";
                    break;
                case 2:
                    return "多选题";
                    break;
                default: break;
            }
        }
    }