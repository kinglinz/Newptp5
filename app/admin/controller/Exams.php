<?php


namespace app\admin\controller;

use think\Controller;
use think\Db;
use app\service\ExportExcel;
use app\admin\model\Exams as ExamsModel;
use Exception;

class Exams extends Controller
{
    public function index()
    {
        return $this->fetch();
    }



    public function publish()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $arr = array();
            $test = (count($post) - 1) / 6;
            $j = 0;
            for ($i = 1; $i <= $test; $i++) {
                $arr[$j]['descrption'] = $post['txtName' . $i];
                $arr[$j]['a'] = $post['txta' . $i];
                $arr[$j]['b'] = $post['txta' . $i];
                $arr[$j]['c'] = $post['txta' . $i];
                $arr[$j]['d'] = $post['txta' . $i];
                $arr[$j]['da'] = $post['txta' . $i];
                $arr[$j]['status'] = $post['status'];
                $j++;
            }
            // $examModel = new ExamsModel();
            // $examModel->saveAll($arr);
            dump($post);
        }
    }

    public function uploadExecl()
    {
        $execl = new ExportExcel();

        try {
            $data =  $execl->doImport();
            $result = $this->checkData($data);
        } catch (Exception $ex) {
            return json(['msg' => $ex->getMessage(), 'code' => $ex->getCode()]);
        }

        $examModel = new ExamsModel();
        $examModel->saveAll($result);
    }


    /**
     * 构建模型写入数组
     * @param $data 导入二维数组
     * @return array
     */

    private function checkData($data)
    {
        
        $i = 0;

        foreach ($data as $arr) {
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
                    $result[$i]['da'] = $arr[3];
                    $result[$i]['status'] = 0;
                    // dump($result);
                } else {
                    $result[$i]['descrption'] = $arr[0];
                    $result[$i]['a'] = $arr[1];
                    $result[$i]['b'] = $arr[2];
                    $result[$i]['c'] = $arr[3];
                    $result[$i]['d'] = $arr[4];
                    $result[$i]['da'] = $arr[5];
                    if (strlen($arr[5]) > 1) {
                        $result[$i]['status'] = 2;
                    } else {
                        $result[$i]['status'] = 1;
                    }
                }
                $i++;
            }
            // dump(11111);
        }

        if (isset($result) && !empty($result)) {
            return $result;
        } else {
            throw new Exception('文件没有数据或者格式不正确', -1);
        }
    }

    /**
     * 检测数组内元素空值范围
     * @param $status 1选择题 0判断题
     * @param $arr 被检查数组
     * @return bool
     */
    private function checkArr($array)
    {
        $tmp1 = [
            'tmp' => 0,
            'count' => 0,
            'status' => false
        ];

        foreach ($array as $str) {
            if (strlen(trim($str)) == 0) {
                $tmp1['count']++;
            }
        }
        $tmp['tmp'] = count($array) - $tmp1['count'];
        if ($tmp['tmp'] < 4)
            $tmp1['status'] = true;
       
        return $tmp1;
    }
}
