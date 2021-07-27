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

/**
 * 下载
 *
 * @description
 * @example
 * @author LittleMo 25362583@qq.com
 * @since 2021-06-28
 * @version 2021-06-28
 */
class Download
{
    /**
     * 下载文件
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-07-17
     * @version 2021-07-17
     * @param string $file      文件路径（文件所在磁盘的绝对路径）
     * @param string $filename  带后缀的文件名称
     * @return void
     */
    public static function file($file, $filename)
    {
        //检查文件是否存在    
        if (!file_exists($file)) {
            header('HTTP/1.1 404 NOT FOUND');
        } else {
            //以只读和二进制模式打开文件   
            $fileContent = fopen($file, "rb");

            //告诉浏览器这是一个文件流格式的文件    
            Header("Content-type: application/octet-stream");
            //请求范围的度量单位  
            Header("Accept-Ranges: bytes");
            //Content-Length是指定包含于请求或响应中数据的字节长度    
            Header("Accept-Length: " . filesize($file));
            //用来告诉浏览器，文件是可以当做附件被下载，下载后的文件名称为$file_name该变量的值。
            Header("Content-Disposition: attachment; filename=" . $filename);

            //读取文件内容并直接输出到浏览器    
            echo fread($fileContent, filesize($file));
            fclose($fileContent);
            exit();
        }
    }
}
