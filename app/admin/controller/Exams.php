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
            $examModel = new ExamsModel();
            $examModel->saveAll($arr);
            dump($arr);
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

        dump($result);
        $examModel = new ExamsModel();
        $examModel->saveAll($result);
    }


    /**
     * 构建模型写入数组
     * @param $status 1选择题 0判断题
     * @param $data 导入二维数组
     * @return array
     */

    private function checkData($data)
    {
        //dump($data);
        $i = 0;

        foreach ($data as $arr) {
            $status = 0;
            $ret = 0;
            foreach ($arr as $str) {
                if (0 == (trim($str))) {
                    $status++;
                }
            }

            if ($status > 2) {
                $status = 0;
                $ret = 4;
            } else $status = 1;
            //dump('test=='.$status);
            if (empty($arr) || count($arr) < 4 || !$this->checkArr($arr, $status)) {
                continue;
            } else {
                if (4 == $ret) {
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
                    $result[$i]['status'] = 1;
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
    private function checkArr($array, $status)
    {

        $tmp1 = 0;
        $tmp0 = 0;

        if (1 == $status) {
            foreach ($array as $str) {
                if (0 == strlen(trim($str))) {
                    $tmp1++;
                }
            }
        }
        if (0 == $status) {
            foreach ($array as $str) {
                if (0 == strlen(trim($str))) {
                    $tmp0++;
                }
            }
        }

        if ($tmp0 > 2 || $tmp1 >= 1)
            return false;
        else
            return true;
    }
}
