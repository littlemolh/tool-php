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
     * @return array
     */
    public function get($file = '', $cache)
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
            $result = $this->wordParsing($file, $cache);
        } catch (\Exception $e) {
            self::$msg['error'] = $e->getMessage();
            //throw $th;
        }

        return $result;
    }

    //解析word内容并返回html
    private function wordParsing($source, $cache, $type = "HTML")
    {
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($source);
        $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, $type);
        $xmlWriter->save($cache);
        self::$msg['html'] = self::getFile($cache);
        return true;
    }
    public function getMessage()
    {
        return self::$msg;
    }
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
