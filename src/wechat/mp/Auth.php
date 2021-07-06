<?php

namespace Littlemo\Tool\wechat\mp;

use think\Cache;

class Auth extends Common
{

    /**
     * 获取小程序全局唯一后台接口调用凭据（access_token）
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-07-06
     * @version 2021-07-06
     * @param [type] $appid
     * @param [type] $secret
     * @param string $grant_type
     * @return void
     */
    public  function getAccessToken($appid, $secret, $grant_type = 'client_credential')
    {
        $cacheName = 'access-token-' . $appid;
        if (Cache::has($cacheName)) {
            return  Cache::get($cacheName);
        }
        //GET https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=APPID&secret=APPSECRET

        $url = "https://api.weixin.qq.com/cgi-bin/token?";
        $url .= "grant_type=" . $grant_type;
        $url .= "&appid=" . $appid;
        $url .= "&secret=" . $secret;
        $result = $this->wxHttpsRequest($url);
        $jsoninfo = json_decode($result, true);
        if (!empty($jsoninfo['errcode'])) {
            trace($jsoninfo, 'dubug');
        }
        Cache::set($cacheName, $jsoninfo['access_token'], $jsoninfo['expires_in']);
        return $jsoninfo['access_token'];
    }
}
