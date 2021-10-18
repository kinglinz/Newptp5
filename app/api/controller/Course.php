<?php
    namespace app\api\controller;

    use app\jk\controller\Login;
    use app\admin\model\Course as courseModel;
    use app\admin\model\CourseInfo;
    class Course extends Login{
        //private $course;
        public function getCourseList(){
            $c = new courseModel();
            return json(collection($c->all())->toArray());
        }

        public function getCourseInfoList(){
            $id = $this->request->get('id');
            $infoModel= new CourseInfo();           
            return json($infoModel->where('course_id', $id)->select());
        }
    }