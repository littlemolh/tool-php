<?php

namespace littlemo\tool\wechat\mp\common;

use think\Cache;

class Auth extends Common
{

    /**
     * 获取小程序全局唯一后台接口调用凭据（access_token）
     *
     * @description
     * 官方文档：https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/access-token/auth.getAccessToken.html
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-07-06
     * @version 2021-07-06
     * @param string $appid
     * @param string $secret
     * @param string $grant_type
     * @return string
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

        $access_token = '';
        if (!empty($jsoninfo['errcode'])) {
            trace($jsoninfo, 'dubug');
        } else {
            Cache::set($cacheName, $jsoninfo['access_token'], $jsoninfo['expires_in']);
            $access_token = $jsoninfo['access_token'];
        }
        return $access_token;
    }
}
