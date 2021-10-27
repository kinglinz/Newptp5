<?php
    namespace app\admin\model;
    use think\Model;

    class BuyCourse extends Model{

        protected $auto = ['buytime'];


        protected $type = [
            'buy_time' => 'timestamp:Y/m/d H:m:s',
        ];


        protected function getIsOnlineAttr($status){
            if($status == 0){
                return '培训';
            }else{
                return '线上';
            }
        }

        protected function getStatusAttr($status){
            if($status == 0){
                return '未完成';
            }else{
                return '已完成';
            }
        }

        public function course(){
            return $this->belongsTo('Course','course_id','id');
        }

        public function user(){
            return $this->belongsTo('app\user\model\User');
        }
    }