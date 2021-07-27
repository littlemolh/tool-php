<?php

// +----------------------------------------------------------------------
// | Little Mo - Tool [ WE CAN DO IT JUST TIDY UP IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://ggui.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: littlemo <25362583@qq.com>
// +----------------------------------------------------------------------

namespace littlemo\tool;

use Redis;

/**
 * 通过IP地址统计用户在指定时间内请求次数
 *
 * @description
 * @example
 * @author LittleMo 25362583@qq.com
 * @since 2021-06-28
 * @version 2021-06-28
 */
class RequestRate
{
    /**
     * 缓存对象
     */
    static $cacheObj = null;

    /**
     * 缓存字段前缀
     */
    static $prefix = 'ip:';

    /**
     * 缓存配置
     */
    static $cacheConfig = [
        'type' => 'redis',
        'host' => '127.0.0.1',
        'port' => '6379',
        'select' => 0,
    ];

    /**
     * 统计时间周期(单位：s)
     */
    static $time = 60;

    /**
     * 一个周期内最大安全访问次数
     */
    static $maxCount = 30;

    /**
     * 错误信息存放容器
     */
    static $message = '';

    /**
     * 缓存字段完整名称
     */
    static $key = '';

    public function __construct($config = [])
    {
        self::$prefix = $config['prefix'] ?? self::$prefix;

        self::$time = $config['time'] ?? self::$time;

        self::$maxCount = $config['maxCount'] ?? self::$maxCount;

        self::$cacheConfig = array_merge(self::$cacheConfig, $config['cache']);

        $this->setCacheObj();

        self::$key = self::$prefix . $this->getIp();
    }

    /**
     * 验证器
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-06-28
     * @version 2021-06-28
     * @return void
     */
    public function  check()
    {
        if (self::$time <= 0 || self::$maxCount <= 0) {
            return true;
        }

        $ip = self::getIp();
        if (self::has()) {
            self::inc();
            $num =  self::get();
            if ($num >= self::$maxCount) {
                self::$message = 'IP: ' . $ip . '；' . self::$time . 's 内请求次数大于 ' . self::$maxCount . '次；共计：' . $num;
                return false;
            }
        } else {
            self::set();
        }
        return true;
    }

    /**
     * 初始化缓存对象
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-06-28
     * @version 2021-06-28
     * @param array $config
     * @return void
     */
    public  function setCacheObj()
    {
        switch (self::$cacheConfig['type']) {
            case 'redis':
                self::$cacheObj = new Redis();
                self::$cacheObj->connect(self::$cacheConfig['host'], self::$cacheConfig['port']);
                self::$cacheObj->select(self::$cacheConfig['select']);
                break;
        }
    }
    /**
     * 获取错误信息
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-06-28
     * @version 2021-06-28
     * @return string
     */
    public function getMessage()
    {
        return self::$message;
    }

    /**
     * 获取缓存次数
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-06-28
     * @version 2021-06-28
     * @return int
     */
    private static function get()
    {
        return   (int)self::$cacheObj->get(self::$key);
    }

    /**
     * 设置缓存次数
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-06-28
     * @version 2021-06-28
     * @return void
     */
    private static function set()
    {
        self::$cacheObj->set(self::$key, 1);
        self::$cacheObj->expire(self::$key, self::$time);
    }

    /**
     * 缓存自增
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-06-28
     * @version 2021-06-28
     * @return void
     */
    private static function inc()
    {
        self::$cacheObj->incr(self::$key);
    }

    /**
     * 判断缓存是否存在
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-06-28
     * @version 2021-06-28
     * @return bool
     */
    private static function has()
    {
        return  self::$cacheObj->exists(self::$key);
    }

    /**
     * 获取请求者真实有效的IP地址
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-06-28
     * @version 2021-06-28
     * @return string
     */
    private static function getIp()
    {
        if (isset($_SERVER["HTTP_CLIENT_IP"]) && strcasecmp($_SERVER["HTTP_CLIENT_IP"], "unknown")) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        } else {
            if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && strcasecmp($_SERVER["HTTP_X_FORWARDED_FOR"], "unknown")) {
                $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } else {
                if (isset($_SERVER["REMOTE_ADDR"]) && strcasecmp($_SERVER["REMOTE_ADDR"], "unknown")) {
                    $ip = $_SERVER["REMOTE_ADDR"];
                } else {
                    if (
                        isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp(
                            $_SERVER['REMOTE_ADDR'],
                            "unknown"
                        )
                    ) {
                        $ip = $_SERVER['REMOTE_ADDR'];
                    } else {
                        $ip = "unknown";
                    }
                }
            }
        }
        return ($ip);
    }
}
