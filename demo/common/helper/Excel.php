<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/25
 * Time: 18:33
 */
namespace common\helper;

class Excel{

    /**
     * 下载excel
     * @param 字符串 $title
     * @param 一维数组 $header
     * @param 二维数组 $data
     * @param string $filename
     * @return 直接下载
     */
    static public function Download($title,$header,$data,$filename,$width_arr)
    {
        ob_clean();
        require(\Yii::getAlias('@common').'/components/phpexcel/PHPExcel.php');
        require(\Yii::getAlias('@common').'/components/phpexcel/PHPExcel/Writer/Excel2007.php');
        if (!is_array ($data) || !is_array ($header)) return false;

        $objPHPExcel = new \PHPExcel();
        // Set properties
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw");
        $objPHPExcel->getProperties()->setLastModifiedBy("Maarten Balliauw");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");
        // Add some data
        $objPHPExcel->setActiveSheetIndex(0);
        //添加头部
        $hk = 0;
        foreach ($header as $k => $v)
        {
            $colum = \PHPExcel_Cell::stringFromColumnIndex($hk);
            $objPHPExcel->getActiveSheet()->getColumnDimension($colum)->setWidth($width_arr[$k]);
            $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
            $objPHPExcel->getActiveSheet()->getStyle($colum.'1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $hk += 1;
        }
        $column = 2;
        $objActSheet = $objPHPExcel->getActiveSheet();
        foreach($data as $key => $rows)  //行写入
        {
            $span = 0;
            foreach($rows as $keyName => $value) // 列写入
            {
                $j = \PHPExcel_Cell::stringFromColumnIndex($span);
                $objActSheet->setCellValue($j.$column, $value);
                $objPHPExcel->getActiveSheet()->getStyle($j.$column)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $span++;
            }
            $column++;
        }

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle($title);
        // Save Excel 2007 file
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        header('Pragma:public');
        header("Content-Type:application/x-msexecl;name=\"{$filename}.xlsx\"");
        header("Content-Disposition:inline;filename=\"{$filename}.xlsx\"");
        $objWriter->save('php://output');
    }
}