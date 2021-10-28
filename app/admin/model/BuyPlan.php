<?php
    namespace app\admin\model;

    use think\Model;

    class BuyPlan extends Model{
        protected function getStatusAttr($status){
            if($status == 0){
                return '未完成';
            }else{
                return '已完成';
            }
        }

        public function course(){
            return $this->belongsTo('Plan','plan_id','id');
        }

        public function user(){
            return $this->belongsTo('app\user\model\User');
        }
    }