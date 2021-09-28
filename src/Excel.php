<?php

// +----------------------------------------------------------------------
// | Little Mo - Tool [ WE CAN DO IT JUST TIDY UP IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2021 http://ggui.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: littlemo <25362583@qq.com>
// +----------------------------------------------------------------------

namespace littlemo\tool;

use Exception;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;




class Excel
{
    static $msg = [];

    public function load($filePath = '')
    {
        try {

            $reader = new Xlsx();
            if (!$PHPExcel = $reader->load($filePath)) {
                throw new Exception('文件不存在或无法打开[' . $filePath . ']');
            }
            $currentSheets = $PHPExcel->getAllSheets();  //读取文件中所有的工作表

            $data = [];
            for ($pIndex = 0; $pIndex <= count($currentSheets); $pIndex++) {
                $sheetsData = [];
                if (!$currentSheet = $currentSheets[$pIndex] ?? null) {
                    break;
                }
                // $this->output->info($currentSheet);
                $allColumn = $currentSheet->getHighestDataColumn(); //取得最大的列号
                $allRow = $currentSheet->getHighestRow(); //取得一共有多少行
                $maxColumnNumber = Coordinate::columnIndexFromString($allColumn);

                $fields = [];
                for ($currentRow = 1; $currentRow <= 1; $currentRow++) {
                    for ($currentColumn = 1; $currentColumn <= $maxColumnNumber; $currentColumn++) {
                        $val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
                        $fields[] = $val;
                    }
                }
                // $this->output->info(print_r($fields));
                $insert = [];
                for ($currentRow = 1; $currentRow <= $allRow; $currentRow++) {
                    $values = [];
                    for ($currentColumn = 1; $currentColumn <= $maxColumnNumber; $currentColumn++) {
                        $val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();

                        $values[] = is_null($val)  ? '' : $val;
                    }

                    $insert[] = $values;
                }
                // $this->output->info(print_r($insert[9])); //第一行数据第一列
                // die;

                foreach ($insert as $key => $val) {
                    $sheetsData[$key] = [];
                    foreach ($val as $k => $v) {
                        if (is_object($v)) {
                            $sheetsData[$key][] = $v->getPlainText();
                        } elseif (!empty($v) || $v === 0) {
                            $sheetsData[$key][] = $v;
                        }
                    }
                }
                $data[] = $sheetsData;
            }
            //code...
        } catch (\Exception $e) {
            //throw $th;
            self::$msg['error'] = $e->getMessage();
            self::$msg['errorCode'] = $e->getCode();
            return false;
        }
        self::$msg['data'] = $data;
        return true;
    }

    public static function getData()
    {
        return self::$msg;
    }
}
