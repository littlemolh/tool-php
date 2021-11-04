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

class Common
{

    /**
     * 制作随机字符串
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-09-15
     * @version 2021-09-15
     * @param int   $length     随机字符串长度
     * @param array $enum       字符串类型选择       
     * @return string
     */
    public static function createNonceStr($length = 32, $enum = ['0', 'a', 'A'])
    {
        $base = [
            '0' => '0123456789',
            'a' => 'qwertyuiopasdfghjklzxcvbnm',
            'A' => 'QWERTYUIOPASDFGHJKLZXCVBNM',
        ];

        $dict = '';
        foreach ($enum as $val) {
            if (!empty($base[$val])) {
                $dict .= $base[$val];
            }
        }

        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($dict, rand(0, (strlen($dict) - 1)), 1);
        }

        return $str;
    }

    /**
     * 制作签名
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-09-15
     * @version 2021-09-15
     * @param array $params     需要按照字段名的ASCII 码从小到大排序（字典序）后
     * @param array $params2    无需排序操作
     * @param array $type       加密方式
     * @return string
     */
    public static function createSign($params, $params2 = [], $type = 'md5')
    {
        ksort($params);
        $string = '';
        $signature = '';
        foreach ($params as $key => $val) {
            if (!empty($val)) {
                $string .= (!empty($string) ? '&' : '') . $key . '=' . $val;
            }
        }
        foreach ($params2 as $key => $val) {
            if (!empty($val)) {
                $string .= (!empty($string) ? '&' : '') . $key . '=' . $val;
            }
        }


        switch ($type) {
            case 'sha1':
                $signature =  sha1($string);
                break;
            case 'md5':
            case 'MD5':
            default:
                $signature =  MD5($string);
        }
        return $signature;
    }
}
