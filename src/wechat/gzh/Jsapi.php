<?php

namespace littlemo\tool\wechat\gzh;

use littlemo\tool\HttpClient;
use littlemo\tool\Common;

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
    public function signature($noncestr = '', $jsapi_ticket = '', $timestamp = '', $url = '')
    {
        $params = [
            'noncestr' => $noncestr,
            'jsapi_ticket' => $jsapi_ticket,
            'timestamp' => $timestamp ?: time(),
            'url' => $url ?: ($_SERVER['HTTP_REFERER'] ?? ''),
        ];
        return Common::createSign($params, [], 'sha1');
    }
}
