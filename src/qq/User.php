<?php

namespace littlemo\tool\qq;

use littlemo\tool\HttpClient;
use littlemo\tool\qq\Base;

/**
 * TODO 企鹅用户信息相关
 *
 * @author sxd
 * @Date 2019-07-25 10:43
 */
class User extends Base
{

    /**
     * 获取登录用户在QQ空间的信息，包括昵称、头像、性别及黄钻信息（包括黄钻等级、是否年费黄钻等）。
     * 
     * 文档：https://wiki.connect.qq.com/get_user_info
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-11-04
     * @version 2021-11-04
     * @param string $access_token
     * @return array
     */
    public function get_user_info($access_token, $openid)
    {

        $url = "https://graph.qq.com/user/get_user_info";
        $params = [
            "access_token" =>  $access_token,
            "oauth_consumer_key" =>  $this->client_id,
            "openid" =>  $openid,
        ];
        return (new HttpClient)->get($url, $params);
    }
}
