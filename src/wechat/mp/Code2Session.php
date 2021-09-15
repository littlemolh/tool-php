<?php

namespace littlemo\tool\wechat\mp;

use littlemo\tool\wechat\mp\common\Common;
use littlemo\tool\wechat\mp\common\WXBizDataCrypt;

/**
 * TODO 小程序登录凭证校验。通过 wx.login 接口获得临时登录凭证 code 换取openid。
 *
 * @author sxd
 * @Date 2019-07-25 10:43
 */
class Code2Session extends Common
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
     * 获取用户小程序openid
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-09-15
     * @version 2021-09-15
     * @param string $code
     * @return array
     */
    public function Code2Openid($code)
    {

        $grant_type    = 'authorization_code';
        $url = "https://api.weixin.qq.com/sns/jscode2session?";
        $url .= "appid=" .  $this->appid;
        $url .= "&secret=" .  $this->secret;
        $url .= "&js_code=" . $code;
        $url .= "&grant_type=" . $grant_type;
        $result = $this->wxHttpsRequest($url);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /**
     * 解 加密后的敏感数据
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-03-11
     * @version 2021-03-11
     * @param array $config
     * @return array
     */
    public function Code2Data($config, $iv)
    {
        $pc = new WXBizDataCrypt($this->appid, $config['sessionKey']);

        if (!empty($iv)) {
            $encryptedData = $config;
        } else {
            $iv = $config['iv'];
            $encryptedData = $config['encryptedData'];
        }
        $errCode = $pc->decryptData($encryptedData, $iv, $data); // 其中$data包含用户的所有数据
        $date = [];
        if ($errCode == 0) {
            $data = json_decode($data, true);
            $data['status'] = 200; //即将遗弃，请勿使用
        } else {
            $date['err_code'] = $errCode;
            $date['status'] = 500; //即将遗弃，请勿使用
            $date['msg'] = $errCode; //即将遗弃，请勿使用
        }
        return $date;
    }
}
