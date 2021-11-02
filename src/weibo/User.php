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
 * 微博用户
 *
 * @description
 * @example
 * @author LittleMo 25362583@qq.com
 * @since 2021-11-02
 * @version 2021-11-02
 */
class User
{




    /**
     * 授权之后获取用户信息
     * 
     * 文档：https://open.weibo.com/wiki/Oauth2/access_token
     *
     * @description 根据用户ID获取用户信息
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-11-02
     * @version 2021-11-02
     * @param string $access_token  采用OAuth授权方式为必填参数，OAuth授权后获得。
     * @param int    $uid           需要查询的用户ID。
     * @param string $screen_name   需要查询的用户昵称。
     * @return array
     */

    public function show($access_token, $uid = '', $screen_name = '')
    {

        $url = "https://api.weibo.com/2/users/show.json";
        $params = [
            "access_token" => $access_token,
            "uid" => $uid,
            "screen_name" => $screen_name,

        ];
        $result =  (new HttpClient)->get($url, $params);
        $jsoninfo = json_decode($result['content'], true);
        return $jsoninfo;
    }

    /**
     * 授权之后通过个性域名获取用户信息
     * 
     * 文档：https://open.weibo.com/wiki/2/users/domain_show
     *
     * @description 通过个性化域名获取用户资料以及用户最新的一条微博
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-11-02
     * @version 2021-11-02
     * @param string $access_token  采用OAuth授权方式为必填参数，OAuth授权后获得。
     * @param int    $domain        需要查询的个性化域名。
     * @return array
     */

    public function domain_show($access_token, $domain)
    {

        $url = "https://api.weibo.com/2/users/domain_show.json";
        $params = [
            "access_token" => $access_token,
            "domain" => $domain,

        ];
        $result =  (new HttpClient)->get($url, $params);
        $jsoninfo = json_decode($result['content'], true);
        return $jsoninfo;
    }
}
