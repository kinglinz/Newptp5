<?php
    namespace app\api\controller;

    use app\jk\controller\Login;
    use app\admin\model\Course as courseModel;

    class Course extends Login{
        //private $course;
        public function getCourseList(){
            $c = new courseModel();
            return json($c->all());
        }

        public function getCourseInfoList(){
            $c = new courseModel();
            return json($c->get(33)->info()->where('course_id',33));
        }
    }