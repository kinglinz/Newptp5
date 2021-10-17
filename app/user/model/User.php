<?php
    namespace app\user\model;
    
    use think\Model;
    
    class User extends Model{
        protected $auto = ['create_time'];
        protected $type = [
            'create_time' => 'timestamp:Y/m/d H:m:s',
        ];
        public function profile(){
            return $this->hasOne('Profile');
        }

        public function buy(){
            return $this->hasMany('app\model\Buycourse');
        }
    }