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
     * @author LittleMo 25362583@qq.com
     * @since 2021-11-02
     * @version 2021-11-02
     * @param [type] $code
     * @param [type] $redirect_uri
     * @return array
     * @ApiReturn
        {
            "access_token": "ACCESS_TOKEN",     //  string	用户授权的唯一票据，用于调用微博的开放接口，同时也是第三方应用验证微博用户登录的唯一票据，第三方应用应该用该票据和自己应用内的用户建立唯一影射关系，来识别登录状态，不能使用本返回值里的UID字段来做登录识别。
            "expires_in": 1234,                 //  string	access_token的生命周期，单位是秒数。
            "remind_in":"798114",               //  string	access_token的生命周期（该参数即将废弃，开发者请使用expires_in）。
            "uid":"12341234"                    //  string	授权用户的UID，本字段只是为了方便开发者，减少一次user/show接口调用而返回的，第三方应用不能用此字段作为用户登录状态的识别，只有access_token才是用户授权的唯一票据。
        }
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
     * 文档：https://developers.weixin.qq.com/doc/offiaccount/OA_Web_Apps/Wechat_webpage_authorization.html#2
     * 
     * @description 查询用户access_token的授权相关信息，包括授权时间，过期时间和scope权限。
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-03-11
     * @version 2021-03-11
     * @param array $access_token 用户授权时生成的access_token。
     * @return array
     * @ApiReturn
        {
            "uid": 1073880650,          //  string 授权用户的uid。
            "appkey": 1352222456,   	//  string	access_token所属的应用appkey。
            "scope": null,	            //  string	用户授权的scope权限。
            "create_at": 1352267591,    //  string	access_token的创建时间，从1970年到创建时间的秒数。
            "expire_in": 157679471      //  string	access_token的剩余时间，单位是秒数，如果返回的时间是负数，代表授权已经过期。
        }
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
     * @author LittleMo 25362583@qq.com
     * @since 2021-11-02
     * @version 2021-11-02
     * @param string $access_token
     * @return array
     * @ApiReturn
        {
            "result":"true"
        }
     */
    public function userinfo($access_token)
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
