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

namespace littlemo\tool\qq;


/**
 * 公众号\小程序基础对象
 *
 * @description
 * @example
 * @author LittleMo 25362583@qq.com
 * @since 2021-11-05
 * @version 2021-11-05
 */
class Base
{

    /**
     * 申请QQ登录成功后，分配给网站的appid。
     *
     * @var string
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-11-10
     * @version 2021-11-10
     */
    protected $client_id = null;

    /**
     * 申请QQ登录成功后，分配给网站的appkey。
     *
     * @var [type]
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-11-10
     * @version 2021-11-10
     */
    protected $client_secret = null;

    /**
     * 构造函数
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-11-05
     * @version 2021-11-05
     * @param string $client_id         申请QQ登录成功后，分配给网站的appid。
     * @param string $client_secret     申请QQ登录成功后，分配给网站的appkey。
     */
    public function __construct($client_id = null, $client_secret = null)
    {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
    }
}
