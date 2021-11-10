<?php

namespace littlemo\tool\wechat\gzh;

use littlemo\tool\HttpClient;
use littlemo\tool\qq\Base;

/**
 * TODO 企鹅OAuth2_0授权
 *
 * @author sxd
 * @Date 2019-07-25 10:43
 */
class OAuth2_0 extends Base
{

    /**
     * 通过Authorization Code获取Access Token
     * 
     * 文档：https://wiki.connect.qq.com/%e4%bd%bf%e7%94%a8authorization_code%e8%8e%b7%e5%8f%96access_token
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-11-04
     * @version 2021-11-04
     * @param string $access_token
     * @return array
     */
    public function token($code, $redirect_uri = '', $grant_type = 'authorization_code', $fmt = 'json')
    {

        $url = "https://graph.qq.com/oauth2.0/token";
        $params = [
            "grant_type" =>  $grant_type,
            "client_id" =>  $this->client_id,
            "client_secret" =>  $this->client_secret,
            "code" =>  $code,
            "redirect_uri" =>  $redirect_uri ?: ($_SERVER['HTTP_REFERER'] ?? ''),
            "fmt" =>  $fmt,
        ];
        return (new HttpClient)->get($url, $params);
    }

    /**
     * （可选）权限自动续期，获取Access Token
     * 
     * 文档：https://wiki.connect.qq.com/%e4%bd%bf%e7%94%a8authorization_code%e8%8e%b7%e5%8f%96access_token
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
    public function refresh_token($refresh_token, $grant_type = 'refresh_token', $fmt = 'json')
    {
        $url = "https://graph.qq.com/oauth2.0/token";

        $params = [
            'grant_type' => $grant_type,
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'refresh_token' => $refresh_token,
            'fmt' => $fmt,
        ];
        return (new HttpClient)->get($url, $params);
    }

    /**
     * 获取用户OpenID_OAuth2.0
     * 
     * 文档：https://wiki.connect.qq.com/%e8%8e%b7%e5%8f%96%e7%94%a8%e6%88%b7openid_oauth2-0
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
    public function me($access_token, $fmt = 'json')
    {
        $url = "https://graph.qq.com/oauth2.0/me";

        $params = [
            'access_token' => $access_token,
            'fmt' => $fmt,
        ];
        return (new HttpClient)->get($url, $params);
    }
}
