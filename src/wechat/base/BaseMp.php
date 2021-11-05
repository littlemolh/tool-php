<?php

namespace littlemo\tool\wechat\base;

use littlemo\tool\HttpClient;


/**
 * 公众号\小程序基础对象
 *
 * @description
 * @example
 * @author LittleMo 25362583@qq.com
 * @since 2021-11-05
 * @version 2021-11-05
 */
class BaseMp
{

    protected $appid = null;
    protected $secret = null;

    /**
     * 构造函数
     * @param $appid    string 小程序的appid
     * @param $secret   string 小程序唯一凭证密钥，即 AppSecret，获取方式同 appid
     */
    public function __construct($appid = null, $secret = null)
    {
        $this->appid = $appid;
        $this->secret = $secret;
    }

    /**
     * 获取全局Access token（支持：公众号、小程序）  
     * 
     * 文档：https://developers.weixin.qq.com/doc/offiaccount/Basic_Information/Get_access_token.html
     * 
     * @description access_token是公众号的全局唯一接口调用凭据，公众号调用各接口时都需使用access_token。开发者需要进行妥善保存。
     * @description access_token的存储至少要保留512个字符空间。access_token的有效期目前为2个小时，需定时刷新，重复获取将导致上次获取的access_token失效。
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-11-05
     * @version 2021-11-05
     * @param string    $grant_type     获取access_token填写client_credential
     * @return void
     */
    protected function token($grant_type = 'client_credential')
    {
        $url = "https://api.weixin.qq.com/cgi-bin/token";
        $params = [
            "grant_type" => $grant_type,
            "appid" =>  $this->appid,
            "secret" =>  $this->secret,
        ];
        $result =  (new HttpClient)->get($url, $params);
        if ($result['code'] === 0) {
            return $result['error_des'];
        }
        return json_decode($result['content'], true);
    }
}
