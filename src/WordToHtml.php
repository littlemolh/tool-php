<?php
// +----------------------------------------------------------------------
// | Little Mo - Tool [ WE CAN DO IT JUST TIDY UP IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://ggui.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: littlemo <25362583@qq.com>
// +----------------------------------------------------------------------

namespace littlemo\tool;

use PhpOffice\PhpWord\IOFactory;

class WordToHtml
{
    static $msg = [];
    /**
     * docx文件转html内容
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-08-05
     * @version 2021-08-05
     * @param string $file  文件路径，仅支持docx类型
     * @param string $cache html缓存路径
     * @return boolean
     */
    public function parsing($file = '', $cache)
    {
        self::$msg = [
            'name' => basename($file)
        ];
        try {
            //code...

            $ext_name = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if ($ext_name != 'docx') {
                throw new \Exception('文件格式不支持，请上传docx格式word文件', 401);
            }
            $filesize = filesize($file);
            $unit = 'B';
            if ($filesize > 1024) {
                $filesize = bcdiv($filesize, 1024, 2);
                $unit = "K";
            }
            if ($filesize > 1024) {
                $filesize = bcdiv($filesize, 1024, 2);
                $unit = "M";
            }
            if ($filesize > 1024) {
                $filesize = bcdiv($filesize, 1024, 2);
                $unit = "G";
            }
            self::$msg['size'] = $filesize;
            self::$msg['unit'] = $unit;

            //创建html
            $phpWord = IOFactory::load($file);
            $xmlWriter = IOFactory::createWriter($phpWord, "HTML");
            $xmlWriter->save($cache);

            //读取html内容
            self::$msg['html'] = self::getFile($cache);
        } catch (\Exception $e) {
            self::$msg['error'] = $e->getMessage();
            self::$msg['errorCode'] = $e->getCode();
        }

        return true;
    }



    /**
     * 获取word解析内容
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-08-05
     * @version 2021-08-05
     * @return array
     */
    public function getMessage()
    {
        return self::$msg;
    }

    /**
     * 获取文件内容
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-08-05
     * @version 2021-08-05
     * @param string $filename
     * @return string
     */
    private static function getFile($filename)
    {
        $return = '';
        if ($fp = fopen($filename, 'rb')) {
            while (!feof($fp)) {
                $return .= fread($fp, 1024);
            }
            fclose($fp);
            return $return;
        } else {
            return false;
        }
    }
}
