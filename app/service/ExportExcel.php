<?php

namespace app\service;

// use PHPExcel;
// use PHPExcel_IOFactory;
use think\Db;

class ExportExcel
{
    /**
     * 导出excel表格（默认格式）
     *
     * @param   array    $columName    第一行的列名称
     * @param   array    $list         二维数组
     * @param   string   $setTitle    sheet名称
     * @return  
     * @author  Tggui <tggui@vip.qq.com>
     */
    private function exportExcel1($columName, $list, $fileName = 'demo', $setTitle = 'Sheet1')
    {

        // vendor('phpoffice.phpexcel.Classes.PHPexcel');
        // vendor('phpoffice.phpexcel.Classes.PHPexcel.IOFactory');

        if (empty($columName) || empty($list)) {
            return '列名或者内容不能为空';
        }
        if (count($list[0]) != count($columName)) {
            return '列名跟数据的列不一致';
        }
        $fileName = iconv("utf-8", "gb2312", $fileName);
        //实例化PHPExcel类
        $PHPExcel    =    new \PHPExcel();
        //获得当前sheet对象
        $PHPSheet    =    $PHPExcel->getActiveSheet();
        //定义sheet名称
        $PHPSheet->setTitle($setTitle);

        //excel的列 这么多够用了吧？不够自个加 AA AB AC ……
        $letter        =    [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
            'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
        ];
        //把列名写入第1行 A1 B1 C1 ...
        for ($i = 0; $i < count($list[0]); $i++) {
            //$letter[$i]1 = A1 B1 C1  $letter[$i] = 列1 列2 列3
            $PHPSheet->setCellValue("$letter[$i]1", "$columName[$i]");
        }
        //内容第2行开始
        foreach ($list as $key => $val) {
            //array_values 把一维数组的键转为0 1 2 3 ..
            foreach (array_values($val) as $key2 => $val2) {
                //$letter[$key2].($key+2) = A2 B2 C2 ……
                $PHPSheet->setCellValue($letter[$key2] . ($key + 2), $val2);
            }
        }
        //生成2007版本的xlsx
        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $fileName . '.xlsx');
        header('Cache-Control: max-age=0');
        $PHPWriter->save("php://output");
        exit;
    }

    /**
     * @method   导出测试方法
     */
    public function connectOrder()
    {
        //连接数据库获取数据
        //$clt_db = Db::connect('clt_db');
        //$data = $clt_db->name('admin')
        // $data = Db::name('user')
        //         ->field('id,username,password,profile_id')
        //         ->select();
        //第一行的列数据ID
        $data = Db::query('select b.id,c.name as coursename,b.buy_time,user.name from tplay_buycourse as b INNER JOIN tplay_course as c ON(b.course_id=c.id) INNER JOIN tplay_user as u ON(b.user_id=u.id) INNER JOIN tplay_profile as user ON(u.profile_id=user.id);');
      
        $header = array('ID', '报名课程', '报名人', '创建时间');

        //调用导出方法
        self::exportExcel1($header, $data, '报名'.date('Y-m-d'));
    }
}
