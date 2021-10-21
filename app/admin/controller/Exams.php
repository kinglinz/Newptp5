<?php


namespace app\admin\controller;

use think\Controller;
use think\Db;
use app\service\ExportExcel;
use app\admin\model\Course as CourseModel;
use app\admin\model\Exams as ExamsModel;
use Exception;

class Exams extends Controller
{
    public function index()
    {

        $courseModel = new CourseModel();
        $data = $courseModel->field('id,name')->select();
        $this->assign("data", $data);
        return $this->fetch();
    }



    public function publish()
    {
        
        if ($this->request->isPost()) {
            $post = $this->request->post();
            if (empty($post['cid'])) {
                return   json(['code' => -1, 'msg' => '请选择练习分类']);
            }
            $arr = array();
            $test = round((count($post) - 2) / 6);
            $j = 0;
            //获取post提交参数到数组       
            for ($i = 1; $i <= $test; $i++) {
                $arr[$j]['descrption'] = $post['txtName' . $i];
                $arr[$j]['a'] = $post['txta' . $i];
                $arr[$j]['b'] = $post['txtb' . $i];
                $arr[$j]['c'] = $post['txtc' . $i];
                $arr[$j]['d'] = $post['txtd' . $i];
                $arr[$j]['da'] = $post['da' . $i];
                $arr[$j]['cid'] = $post['cid'];
                $j++;
            }
            try {              
                $result = $this->checkData($arr, $post['cid']);
            } catch (Exception $ex) {
                return json(['code'=>-1,'msg' => $ex->getMessage()]);
            }
            $examModel = new ExamsModel();
            if ($examModel->saveAll($result)) {
                return json(['code' => 2, 'msg' => '添加成功']);
            } else {
                return json(['code' => -1, 'msg' => '添加失败']);
            }
        }
    }

    
    /**
     * 练习导入excel接口
     */
    public function uploadExecl()
    {
        $post = $this->request->post();
        if (empty($post['cid'])) {
            return   json(['code' => -1, 'msg' => '请选择练习分类']);
        }
        $execl = new ExportExcel();
        $fileinfo = request()->file('file')->getInfo();
        try {
            $data =  $execl->doImport($fileinfo['tmp_name']);
            $result = $this->checkData($data, $post['cid']);
        } catch (Exception $ex) {
            return json(['msg' => $ex->getMessage(), 'code' => $ex->getCode()]);
           // return json(['code' => -1, 'msg' => 'test']);
        }

        $examModel = new ExamsModel();
        if ($examModel->saveAll($result)) {
            return json(['code' => 2, 'msg' => '导入成功']);
        } else {
            return json(['code' => -1, 'msg' => '导入失败']);
        }
    }


    /**
     * 构建模型写入数组
     * @param $data 二维数组
     * @param $cid  课程id
     * @return array
     */

    private function checkData($data, $cid)
    {
        $i = 0;
        
        foreach ($data as $temp) {
            $arr = $this->arrk2num($temp);
            $status = $this->checkArr($arr);

            if (empty($arr) || count($arr) < 4 || $status['status']) {
                continue;
            } else {
                if (4 == $status['tmp']) {
                    $result[$i]['descrption'] = $arr[0];
                    $result[$i]['a'] = $arr[1];
                    $result[$i]['b'] = $arr[2];
                    $result[$i]['c'] = '';
                    $result[$i]['d'] = '';
                    $result[$i]['da'] = $arr[5];
                    $result[$i]['status'] = 0;
                    $result[$i]['cid'] = $cid;
                    // dump($result);
                } else {
                    $result[$i]['descrption'] = $arr[0];
                    $result[$i]['a'] = $arr[1];
                    $result[$i]['b'] = $arr[2];
                    $result[$i]['c'] = $arr[3];
                    $result[$i]['d'] = $arr[4];
                    $result[$i]['da'] = $arr[5];
                    $result[$i]['cid'] = $cid;
                    if (strlen($arr[5]) > 1) {
                        $result[$i]['status'] = 2;
                    } else {
                        $result[$i]['status'] = 1;
                    }
                }
                $i++;
            }
        }
      
        if (isset($result) && !empty($result)) {
            return $result;
        } else {
            throw new Exception('没有数据或者文件格式不正确', -1);
        }
    }

    /**
     * 检测数组内元素空值范围
     * @param $arr 被检查数组
     * @return array
     */
    private function checkArr($array)
    {


        $tmp1 = [
            'tmp' => 0,
            'count' => 0, //记录空字符个数
            'status' => false
        ];

        foreach ($array as $str) {
            if (strlen(trim($str)) == 0) {
                $tmp1['count']++;
            }
        }
        //得到有效数据个数  小于4 为无效数据
        $tmp1['tmp'] = count($array) - $tmp1['count'];
        if ($tmp1['tmp'] < 4)
            $tmp1['status'] = true;

        return $tmp1;
    }

    //转换数字下标 
    private function arrk2num($arr)
    {
        if (!is_array($arr)) {
            return $arr;
        }
        $newarr = array();
        foreach ($arr as  $tmp) {
            array_push($newarr, $tmp);
        }

        return $newarr;
    }
}
