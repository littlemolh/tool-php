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

namespace littlemo\tool\wechat\pay;


/**
 * 现金红包
 * 微信公众号专用 小程序不能用
 * @description
 * @example
 * @author LittleMo 25362583@qq.com
 * @since 2021-09-25
 * @version 2021-09-25
 */
class Redpack extends Common
{


    /**
     * 当前服务器IP地址
     */
    static $ip = null;

    /**
     * 构造函数
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-09-15
     * @version 2021-09-15
     * @param string $mchid     商户号
     * @param string $key       支付密钥
     * @param string $certPath  证书路径
     * @param string $keyPath   证书密钥路径
     * @param string $appid     应用appid
     * @param string $ip        发情请求的IP地址（服务端IP地址）
     */
    public function __construct($mchid, $key, $certPath, $keyPath, $appid, $ip)
    {
        self::$mchid = $mchid;
        self::$key = $key;

        $this->sslCertPath = $certPath;
        $this->sslKeyPath = $keyPath;

        self::$appid = $appid;
        self::$ip = $ip;
    }

    /**
     * 发放微信红包
     * 文档 https://pay.weixin.qq.com/wiki/doc/api/tools/cash_coupon.php?chapter=13_4&index=3
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-09-25
     * @version 2021-09-25
     * @param string    $openid     用户openid
     * @param string    $money      红包金额，单位元
     * @param string    $no         订单编号
     * @param array     $redpack    红包内容:活动名称、祝福语和备注      
     * @param int       $total_num  红包发放人数   
     * @param string    $type       类型(gzh:公众号;mp:小程序)   
     * @return void
     */
    public function create($openid,  $money = '0.00', $no, $redpack = [], $total_num = 1)
    {
        /**
         * 是否需要证书	是（证书及使用说明详见商户证书）
         * 请求方式	POST
         * 超时时间（同笔订单最短重试时间）	1s
         */
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';


        $params = [];

        $params['nonce_str'] = $this->createNonceStr(); //随机字符串

        $params['mch_billno'] = $no; //商户订单号	
        $params['mch_id'] = self::$mchid; //商户号
        $params['wxappid'] = self::$appid; //公众账号appid 公众号的appid或小程序的appid（在mp.weixin.qq.com申请的）或APP的appid（在open.weixin.qq.com申请的）
        $params['send_name'] = $redpack['send_name']; //商户名称 红包发送者名称 注意：敏感词会被转义成字符*
        $params['re_openid'] = $openid; //用户openid	
        $params['total_amount'] = bcmul($money, 100); //付款金额，单位分
        $params['total_num'] = $total_num; //红包发放总人数

        $params['client_ip'] = self::$ip; //调用接口的机器Ip地址

        $params['wishing'] = $redpack['wishing']; //红包祝福语 注意：敏感词会被转义成字符*
        $params['act_name'] = $redpack['act_name']; //活动名称 注意：敏感词会被转义成字符*
        $params['remark'] = $redpack['remark']; //备注信息
        /**
         * 发放红包使用场景，红包金额大于200或者小于1元时必传
         * 1 PRODUCT_1:商品促销
         * 2 PRODUCT_2:抽奖
         * 3 PRODUCT_3:虚拟物品兑奖 
         * 4 PRODUCT_4:企业内部福利
         * 5 PRODUCT_5:渠道分润
         * 6 PRODUCT_6:保险回馈
         * 7 PRODUCT_7:彩票派奖
         * 8 PRODUCT_8:税务刮奖
         */
        !empty($redpack['scene_id']) && $params['scene_id'] = 'PRODUCT_' . $redpack['scene_id'];

        $params['sign'] = $this->createSign($params); //签名

        $result = $this->request($url, $this->data_to_xml($params), true);
        $obj = simplexml_load_string($result, "SimpleXMLElement", LIBXML_NOCDATA);
        $result = json_decode(json_encode($obj), true);
        self::$result = $result;
        if ($result['result_code'] !== 'SUCCESS') {
            return false;
        }
        return true;
    }


    /**
     * 查询红包记录
     * 文档 https://pay.weixin.qq.com/wiki/doc/api/tools/cash_coupon.php?chapter=13_6&index=5
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-09-25
     * @version 2021-09-25
     * @param [type] $openid
     * @param [type] $money
     * @param [type] $no
     * @param string $desc
     * @param string $userName
     * @return void
     */
    public function get($no, $bill_type = 'MCHT')
    {
        /**
         * 是否需要证书	是（证书及使用说明详见商户证书）
         * 请求方式	POST
         * 超时时间（同笔订单最短重试时间）	1s
         */
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/gethbinfo';


        $params = [];

        $params['nonce_str'] = $this->createNonceStr(); //随机字符串
        $params['mch_billno'] = $no; //商户订单号	
        $params['mch_id'] = self::$mchid; //商户号
        $params['appid'] = self::$appid; //公众账号appid 公众号的appid或小程序的appid（在mp.weixin.qq.com申请的）或APP的appid（在open.weixin.qq.com申请的）
        $params['bill_type'] = $bill_type; //MCHT:通过商户订单号获取红包信息。

        $params['sign'] = $this->createSign($params); //签名

        $result = $this->request($url, $this->data_to_xml($params), true);
        $obj = simplexml_load_string($result, "SimpleXMLElement", LIBXML_NOCDATA);
        $result = json_decode(json_encode($obj), true);
        self::$result = $result;
        if ($result['result_code'] !== 'SUCCESS') {
            return false;
        }
        return true;
    }

    public function getResultData()
    {
        return self::$result;
    }
}
