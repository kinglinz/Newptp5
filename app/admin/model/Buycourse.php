<?php
    namespace app\admin\model;
    use think\Model;

    class Buycourse extends Model{

        protected $auto = ['buytime'];

        protected $type = [
            'buy_time' => 'timestamp:Y/m/d H:m:s',
        ];

        public function course(){
            return $this->belongsTo('Course','course_id','id');
        }

        public function user(){
            return $this->belongsTo('app\user\model\User');
        }
    }