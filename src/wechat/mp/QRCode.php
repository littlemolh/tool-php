<?php

namespace littlemo\tool\wechat\mp;

use littlemo\tool\wechat\mp\common\Common;
use littlemo\tool\wechat\mp\common\Auth;

/**
 * TODO 小程序码
 * 注意事项 https://developers.weixin.qq.com/miniprogram/dev/framework/open-ability/qr-code.html#%E6%B3%A8%E6%84%8F%E4%BA%8B%E9%A1%B9
 * @author sxd
 * @Date 2019-07-25 10:43
 */
class QRCode extends Common
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
     * 获取小程序二维码
     *
     * @description 适用于需要的码数量较少的业务场景。通过该接口生成的小程序码，永久有效，有数量限制。
     * 官方文档：https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/qr-code/wxacode.createQRCode.html
     * 
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-07-06
     * @version 2021-07-06
     * @param array $config
     * @return array
     */
    public function createQRCode(array $config)
    {

        //POST https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token=ACCESS_TOKEN

        //请求参数
        $params = [
            /**
             * 必填
             * 扫码进入的小程序页面路径
             * 最大长度 128 字节，不能为空；对于小游戏，可以只传入 query 部分，来实现传参效果，
             * 如：传入 "?foo=bar"，即可在 wx.getLaunchOptionsSync 接口中的 query 参数获取到 {foo:"bar"}。
             */
            'path' => $config['path'] ?: '/',
            /**
             * 非必填
             * 默认值：430
             * 二维码的宽度
             * 单位 px。最小 280px，最大 1280px
             */
            'width' => $config['width'] ?: '',
        ];

        $access_token =  (new Auth)->getAccessToken($this->appid, $this->secret); //接口调用凭证
        $url = "https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token=" . $access_token;
        $result = $this->wxHttpsRequest($url, $params);
        return self::returnData($result);
    }

    /**
     * 获取小程序码
     *
     * @description 适用于需要的码数量较少的业务场景。通过该接口生成的小程序码，永久有效，有数量限制
     * 官方文档：https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/qr-code/wxacode.get.html
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-03-11
     * @version 2021-03-11
     * @param array $config
     * @return array   
     */
    public function get(array $config)
    {
        //POST https://api.weixin.qq.com/wxa/getwxacode?access_token=ACCESS_TOKEN

        //请求参数
        $params = [
            /**
             * 必填
             * 扫码进入的小程序页面路径
             * 最大长度 128 字节，不能为空；对于小游戏，可以只传入 query 部分，来实现传参效果，
             * 如：传入 "?foo=bar"，即可在 wx.getLaunchOptionsSync 接口中的 query 参数获取到 {foo:"bar"}。
             */
            'path' => $config['path'] ?: '/',
            /**
             * 非必填
             * 默认值：430
             * 二维码的宽度
             * 单位 px。最小 280px，最大 1280px
             */
            'width' =>  $config['width'] ?: '',
            /**
             * 非必填
             * 默认值：false
             * 自动配置线条颜色
             * 如果颜色依然是黑色，则说明不建议配置主色调
             */
            'auto_color' => $config['auto_color'] == true ? true : false,
            /**
             * 非必填
             * 默认值：{"r":0,"g":0,"b":0}
             * auto_color 为 false 时生效
             * 使用 rgb 设置颜色 例如 {"r":"xxx","g":"xxx","b":"xxx"} 十进制表示
             */
            'line_color' =>  $config['line_color'] ?: null,
            /**
             * 非必填
             * 默认值：false
             * 是否需要透明底色
             * 为 true 时，生成透明底色的小程序码
             */
            'is_hyaline' => $config['is_hyaline'] == true ? true : false,
        ];

        $access_token =  (new Auth)->getAccessToken($this->appid, $this->secret);

        $url = "https://api.weixin.qq.com/wxa/getwxacode?access_token=" . $access_token;
        $result = $this->wxHttpsRequest($url, $params);
        return self::returnData($result);
    }
    /**
     * 获取小程序码
     * 
     * @description 适用于需要的码数量极多的业务场景。通过该接口生成的小程序码，永久有效，数量暂无限制
     * 官方文档：https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/qr-code/wxacode.getUnlimited.html
     * 
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-07-06
     * @version 2021-07-06
     * @param array     $config                     全部参数
     * @param string    $config['scene']            最大32个可见字符，只支持数字，大小写英文以及部分特殊字符：!#$&'()*+,/:;=?@-._~，其它字符请自行编码为合法字符（因不支持%，中文无法使用 urlencode 处理，请使用其他编码方式）
     * @param string    $config['page']	            必须是已经发布的小程序存在的页面（否则报错），例如 pages/index/index, 根路径前不要填加 /,不能携带参数（参数请放在scene字段里），如果不填写这个字段，默认跳主页面
     * @param int       $config['width']            二维码的宽度，单位 px，最小 280px，最大 1280px
     * @param boolean   $config['auto_colorr']      自动配置线条颜色，如果颜色依然是黑色，则说明不建议配置主色调，默认 false
     * @param Object    $config['line_colorr']      auto_color 为 false 时生效，使用 rgb 设置颜色 例如 {"r":"xxx","g":"xxx","b":"xxx"} 十进制表示
     * @param boolean   $config['is_hyalineline']   是否需要透明底色，为 true 时，生成透明底色的小程序
     * @return array
     */
    public function getUnlimited(array $config)
    {
        //POST https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=ACCESS_TOKEN
        $access_token = (new Auth)->getAccessToken($this->appid, $this->secret); // cloudbase_access_token	string		是	接口调用凭证

        $params = [
            /**
             * 必填
             * 最大32个可见字符，只支持数字，大小写英文以及部分特殊字符：!#$&'()*+,/:;=?@-._~，
             * 其它字符请自行编码为合法字符（因不支持%，中文无法使用 urlencode 处理，请使用其他编码方式）
             * 例如：a=1,b=2
             */
            'scene' => $config['scene'] ?: null,
            /**
             * 非必填
             * 默认值：主页
             * 必须是已经发布的小程序存在的页面（否则报错），
             * 例如 pages/index/index, 根路径前不要填加 /,不能携带参数（参数请放在scene字段里），如果不填写这个字段，默认跳主页面
             */
            'page' => $config['page'] ?: '',
            /**
             * 非必填
             * 默认值：430
             * 二维码的宽度，单位 px，最小 280px，最大 1280px
             */
            'width' => $config['width'] ?: '',
            /**
             * 非必填
             * 默认值：false
             * 自动配置线条颜色，如果颜色依然是黑色，则说明不建议配置主色调，默认 false
             */
            'auto_color' => $config['auto_colorr'] == true ? true : false,
            /**
             * 非必填
             * 默认值：{"r":0,"g":0,"b":0}
             * auto_color 为 false 时生效，使用 rgb 设置颜色 例如 {"r":"xxx","g":"xxx","b":"xxx"} 十进制表示
             */
            'line_color' => $config['line_colorr'] ?: null,
            /**
             * 非必填
             * 默认值：false
             * 是否需要透明底色，为 true 时，生成透明底色的小程序
             */
            'is_hyaline' => $config['is_hyalineline'] == true ? true : false,
        ];

        $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=" . $access_token;
        $result = $this->wxHttpsRequest($url, json_encode($params));
        return self::returnData($result);
    }
    /**
     * 处理返回数据
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-07-07
     * @version 2021-07-07
     * @param string $result
     * @return array 
     */
    private static function returnData($result = '')
    {
        $jsoninfo = json_decode($result, true);
        if (empty($jsoninfo) && !empty($result)) {
            if ($result == 'Errno6') {
                return [
                    "errcode" => 1,
                    "errmsg" => "Errno6",
                ];
            }
            return [
                "errcode" => 0,
                "errmsg" => "ok",
                "contentType" => "image/jpeg",
                "buffer" => $result
            ];
        }
        return $jsoninfo;
    }
}
