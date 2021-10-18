<?php
    namespace app\admin\model;
    use think\Model;
    class BuycourseProfile extends Model{

        public function buycourse(){
            return $this->belongsTo('Buycourse');
        }
    }