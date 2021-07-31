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

namespace littlemo\tool\sms;

class IpyySms
{

    /**
     * string 实际账户名
     */
    static $account = '';

    /**
     * string 实际短信发送密码
     */
    static $password = '';

    /**
     * string 扩展子号
     * 请先询问配置的通道是否支持扩展子号，如果不支持，请填空。子号只能为数字，且最多5位数。
     */
    static $extno = '';


    /**
     * string 模板内容
     */
    static $template = '';

    /**
     * 一般用于暂存错误内容
     */
    static $msg = '';

    /**
     * 构造函数
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-07-31
     * @version 2021-07-31
     * @param array $config 平台配置：账号和密码
     * @param string $template  短信的模板，内容需要UTF-8编码，提交内容格式：内容+【签名】。示例：您的验证码：1439【腾飞】。【】是签名的标识符。
     */
    public function __construct($config = [], $template)
    {
        self::$account  = $config['account'] ?? '';
        self::$password  = $config['password'] ?? '';
        self::$template  = $template;
    }

    /**
     * 普通发送短信接口
     * 
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-07-30
     * @version 2021-07-30
     * @param string $mobiles   手机号码，多个号码之间用半角逗号隔开
     * @param string $data      模板变量数据
     * @param string $sendtime  为空表示立即发送，定时发送格式    2019-12-14 09:08:10
     * @return array
     */
    public static function send($mobiles, $data, $sendtime = '')
    {
        // https://dx.ipyy.net/sms.aspx        返回xml格式		
        // https://dx.ipyy.net/smsJson.aspx    返回json格式
        $url = 'https://dx.ipyy.net/smsJson.aspx';
        $body = array(
            'action' => 'send',
            'userid' => '',
            'account' => self::$account,
            'password' => self::$password,
            'mobile' => $mobiles,
            'extno' => self::$extno,
            'content' => self::getSmsContent($data),
            'sendtime' => $sendtime
        );
        $result = self::curl($url, $body);
        if ($result === false) {
            return false;
        }
        $result = json_decode($result, true);
        if ($result['returnstatus'] != 'Success') {
            self::$msg = $result;
            return false;
        }
        return true;
        /* array(5) {
            ["returnstatus"]=>string(7) "Success" 成功返回Success 失败返回：Fail	返回状态值
            ["message"]=>string(12) "操作成功" message	操作成功	相关的返回描述
            ["remainpoint"]=>string(2) "22" 56321	返回余额
            ["taskID"]=>string(16) "2107303937047390" 1912114154381322	返回本次任务的序列ID
            ["successCounts"]=>string(1) "1" 1000	当成功后返回提交成功短信数
          } */
    }
    /**
     * 返回描述内容
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-07-31
     * @version 2021-07-31
     * @return array
     */
    public static function getMessage()
    {
        return self::$msg;
    }


    private static function getSmsContent($data)
    {
        $template = self::$template;
        foreach ($data as $k => $v) {
            $template =  str_replace('${' . $k . '}', $v, $template);
        }
        return $template;
    }

    /**
     * 发起curl请求
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-07-31
     * @version 2021-07-31
     * @param string $url
     * @param array $body
     * @return string
     */
    private static function curl($url = 'https://dx.ipyy.net/smsJson.aspx', $body = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            self::$msg['curl_error'] = curl_error($ch);
        }
        curl_close($ch);
        return $result;
    }
}
