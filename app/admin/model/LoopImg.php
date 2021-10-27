<?php
    namespace app\admin\model;

    use think\Model;

    class LoopImg extends Model{
        protected $auto = ['update_time'];
        protected $type = [
            //'timestamp:Y/m/d H:m:s',
            'update_time' => 'timestamp:Y/m/d H:m:s'
        ];

        public function setNameAttr($value){
            return strtoupper($value);
        }
    }