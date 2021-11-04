<?php

namespace littlemo\tool\wechat\gzh;

use littlemo\tool\HttpClient;

/**
 * TODO 小程序网页授权
 *
 * @author sxd
 * @Date 2019-07-25 10:43
 */
class Jsapi
{



    /**
     * 获得jsapi_ticket
     * 
     * 文档：https://developers.weixin.qq.com/doc/offiaccount/OA_Web_Apps/JS-SDK.html#62
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-11-04
     * @version 2021-11-04
     * @param string $access_token
     * @return array
     */
    public function ticket($access_token)
    {

        $grant_type    = 'authorization_code';
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket";
        $params = [
            "access_token" =>  $access_token,
            "type" =>  'jsapi',
        ];
        $result =  (new HttpClient)->get($url, $params);
        if ($result['code'] === 0) {
            return $result['error_des'];
        }
        return json_decode($result['content'], true);
    }

    /**
     * JS-SDK使用权限签名算法
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-11-04
     * @version 2021-11-04
     * @param string $noncestr      随机字符串
     * @param string $jsapi_ticket
     * @param int    $timestamp     时间戳
     * @param string $url           当前网页的URL，不包含#及其后面部分
     * @return void
     */
    public function sdkSignature($noncestr = '', $jsapi_ticket = '', $timestamp = time(), $url)
    {
        $params = [
            'noncestr' => $noncestr,
            'jsapi_ticket' => $jsapi_ticket,
            'timestamp' => $timestamp,
            'url' => $url,
        ];
        return $this->createSign($params, [], 'sha1');
    }

    /**
     * 制作随机字符串，不长于32位
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-09-15
     * @version 2021-09-15
     * @return string
     */
    protected function createNonceStr()
    {
        $data = '0123456789qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
        $str = '';
        for ($i = 0; $i < 32; $i++) {
            $str .= substr($data, rand(0, (strlen($data) - 1)), 1);
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
     * @param array $params2   无需排序操作
     * @return string
     */
    protected function createSign($params, $params2 = [], $type = 'md5')
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
