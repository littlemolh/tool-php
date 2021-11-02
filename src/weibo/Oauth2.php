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

namespace littlemo\tool\weibo;

use littlemo\tool\HttpClient;

/**
 * 微博身份认证2.0
 *
 * @description
 * @example
 * @author LittleMo 25362583@qq.com
 * @since 2021-11-02
 * @version 2021-11-02
 */
class Oauth2
{

    /**
     * 申请应用时分配的AppKey。
     *
     * @var string
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-11-02
     * @version 2021-11-02
     */
    static $client_id = null;

    /**
     * 	申请应用时分配的AppSecret。
     *
     * @var string
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-11-02
     * @version 2021-11-02
     */
    static $client_secret = null;

    /**
     * 构造函数
     * @param $client_id    string 申请应用时分配的AppKey。
     * @param $client_secret   string 申请应用时分配的AppSecret。
     */
    public function __construct($client_id, $client_secret)
    {
        self::$client_id = $client_id;
        self::$client_secret = $client_secret;
    }

    /**
     * 获取授权过的Access Token
     * 
     * 文档：https://open.weibo.com/wiki/Oauth2/access_token
     *
     * @description OAuth2的access_token接口
     * @example
     * @author  LittleMo 25362583@qq.com
     * @since   2021-11-02
     * @version 2021-11-02
     * @param string $code
     * @param string $redirect_uri
     * @return array
     */

    public function access_token($code, $redirect_uri)
    {

        $grant_type    = 'authorization_code';
        $url = "https://api.weibo.com/oauth2/access_token";
        $data = [
            "client_id" => self::$client_id,
            "client_secret	" => self::$client_secret,
            "grant_type" => $grant_type,
            "code" => $code,
            "redirect_uri" => $redirect_uri,
        ];
        $result =  (new HttpClient)->post($url, $data);
        $jsoninfo = json_decode($result['content'], true);
        return $jsoninfo;
    }

    /**
     * 授权信息查询接口
     *
     * 文档：https://open.weibo.com/wiki/Oauth2/get_token_info
     * 
     * @description 查询用户access_token的授权相关信息，包括授权时间，过期时间和scope权限。
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-03-11
     * @version 2021-03-11
     * @param string $access_token 用户授权时生成的access_token。
     * @return array
     */
    public function get_token_info($access_token)
    {
        $url = "https://api.weibo.com/oauth2/get_token_info";
        $data = [
            "access_token" =>  $access_token,
        ];
        $result =  (new HttpClient)->post($url, $data);
        $jsoninfo = json_decode($result['content'], true);
        return $jsoninfo;
    }

    /**
     * 授权回收接口
     * 
     * 文档：https://open.weibo.com/wiki/Oauth2/revokeoauth2
     * 
     * @description 授权回收接口，帮助开发者主动取消用户的授权。
     * @example
     * @author  LittleMo 25362583@qq.com
     * @since   2021-11-02
     * @version 2021-11-02
     * @param   string $access_token
     * @return  array
     */
    public function revokeoauth2($access_token)
    {
        $url = "https://api.weibo.com/oauth2/revokeoauth2";
        $data = [
            "access_token" =>  $access_token,
        ];
        $result =  (new HttpClient)->post($url, $data);
        $jsoninfo = json_decode($result['content'], true);
        return $jsoninfo;
    }
}
