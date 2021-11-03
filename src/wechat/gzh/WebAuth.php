<?php

namespace littlemo\tool\wechat\gzh;

use littlemo\tool\HttpClient;

/**
 * TODO 小程序网页授权
 *
 * @author sxd
 * @Date 2019-07-25 10:43
 */
class WebAuth
{

    public $appid = null;
    public $secret = null;

    /**
     * 构造函数
     * @param $appid    string 小程序的appid
     * @param $secret   string 小程序唯一凭证密钥，即 AppSecret，获取方式同 appid
     */
    public function __construct($appid, $secret)
    {
        $this->appid = $appid;
        $this->secret = $secret;
    }

    /**
     * 通过code换取网页授权access_token
     * 
     * 文档：https://developers.weixin.qq.com/doc/offiaccount/OA_Web_Apps/Wechat_webpage_authorization.html#1
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-09-15
     * @version 2021-09-15
     * @param string $code
     * @return array
     */
    public function access_token($code)
    {

        $grant_type    = 'authorization_code';
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token";
        $params = [
            "appid" =>  $this->appid,
            "secret" =>  $this->secret,
            "code" => $code,
            "grant_type" => $grant_type
        ];
        $result =  (new HttpClient)->get($url, $params);
        if ($result['code'] === 0) {
            return $result['error_des'];
        }
        return json_decode($result['content'], true);
    }

    /**
     * 刷新access_token（如果需要）
     *
     * 文档：https://developers.weixin.qq.com/doc/offiaccount/OA_Web_Apps/Wechat_webpage_authorization.html#2
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-03-11
     * @version 2021-03-11
     * @param array $refresh_token 填写通过access_token获取到的refresh_token参数
     * @return array
     */
    public function refresh_token($refresh_token)
    {
        $grant_type    = 'refresh_token';
        $url = "https://api.weixin.qq.com/sns/oauth2/refresh_token";
        $params = [
            "appid" =>  $this->appid,
            "grant_type" => $grant_type,
            "refresh_token" => $refresh_token,
        ];
        $result =  (new HttpClient)->get($url, $params);
        if ($result['code'] === 0) {
            return $result['error_des'];
        }
        return json_decode($result['content'], true);
    }

    /**
     * 拉取用户信息(需scope为 snsapi_userinfo)
     * 文档：https://developers.weixin.qq.com/doc/offiaccount/OA_Web_Apps/Wechat_webpage_authorization.html#3
     * @description 如果网页授权作用域为snsapi_userinfo，则此时开发者可以通过access_token和openid拉取用户信息了。
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-11-01
     * @version 2021-11-01
     * @param [type] $access_token
     * @param [type] $openid
     * @param string $lang
     * @return void
     */
    public function userinfo($access_token, $openid, $lang = 'zh_CN')
    {
        $url = "https://api.weixin.qq.com/sns/userinfo";
        $params = [
            "access_token" =>  $access_token,
            "openid" => $openid,
            "lang" => $lang,
        ];
        $result =  (new HttpClient)->get($url, $params);
        if ($result['code'] === 0) {
            return $result['error_des'];
        }
        return json_decode($result['content'], true);
    }
}
