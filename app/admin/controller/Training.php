<?php
    namespace app\admin\controller;
    use app\admin\model\CoursePlan as CoursePlanModel;

    //培训管理
    class Training extends Permissions{
        public function index(){
            $model = new CoursePlanModel();
            $data = $model->select();
            $this->assign("course", $data);
            return $this->fetch();
        }

        public function sign(){
            return $this->fetch();
        }

        public function score(){
            return $this->fetch();
        }

        public function grade(){
            return $this->fetch();
        }

        public function teacher(){
            return $this->fetch();
        }
    }