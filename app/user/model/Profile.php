<?php
    namespace app\user\model;

    use think\Model;

    class Profile extends Model{

        protected $autoWriteTimestamp= false;
        
        public function user(){
            return $this->belongsTo('User');
        }
    }