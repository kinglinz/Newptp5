<?php

namespace app\service;

// use PHPExcel;
// use PHPExcel_IOFactory;

use Exception;
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

        if(empty($list) ||   !is_array($list[0]) || empty($list)){
            //throw new Exception("查询数据失败");
            return json([
                'code' => '-1','msg' => '数据导出失败'
            ]);
        }
     
        if (empty($columName) || empty($list)) {
            //throw new Exception('列名或者内容不能为空');
              return json(['code' => '-1','msg' => '数据导出失败']);
        }
        if (count($list[0]) != count($columName)) {
            //return '列名跟数据的列不一致';
            return json(['code' => '-1','msg' => '数据导出失败']);
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


    // 执行数据导入
    public function doImport($fileName)
    {

        set_time_limit(90);
       

        $objPHPExcel = \PHPExcel_IOFactory::load($fileName);

        //$sheet_count = $objPHPExcel->getSheetCount();
       // for ($s = 0; $s < $sheet_count; $s++) {
            $currentSheet = $objPHPExcel->getSheet(0); // 当前页 
            $row_num = $currentSheet->getHighestRow(); // 当前页行数 
            $col_max = $currentSheet->getHighestColumn(); // 当前页最大列号 
        
            // 循环从第二行开始，第一行往往是表头 
            for ($i = 1,$d=0; $i <= $row_num;$d++, $i++) {        
                for ($j = 'A'; $j <= $col_max; $j++) {
                    $address = $j . $i; // 单元格坐标 
                    $cell_values[$d][]= $currentSheet->getCell($address)->getFormattedValue();
                }
               
            }

            return $cell_values;
        //}
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
        $data = Db::query('select b.id,c.name as coursename,b.create_time,user.name from tplay_buy_course as b INNER JOIN tplay_course as c ON(b.course_id=c.id) INNER JOIN tplay_user as u ON(b.user_id=u.id) INNER JOIN tplay_profile as user ON(u.profile_id=user.id);');
       
        //设置表头
        $header = array('ID', '报名课程', '报名人', '创建时间');       
        //调用导出方法
        return self::exportExcel1($header, $data, '报名' . date('Y年m月d日 H.i'));

    }
}
