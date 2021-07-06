<?php

namespace Littlemo\Tool\open;

use think\Cache;

/**
 * 微信第三方平台相关接口
 *
 * @description
 * @example
 * @author LittleMo 25362583@qq.com
 * @since 2021-03-25
 * @version 2021-03-25
 */
class Component
{
    /**
     * 第三方平台配置
     *
     * @var array
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-03-25
     * @version 2021-03-25
     */
    private $appid = '';
    private $appsecret = '';
    private $verifyTicket = '';

    public function __construct($config = [])
    {
        $this->appid = !empty($config['appid']) ? $config['appid'] : get_wx_component()['appid'];
        // $this->appid = !empty($config['appid']) ? $config['appid'] : Env::get('weixin_component.appid');
        $this->appsecret = !empty($config['appsecret']) ? $config['appsecret'] : get_wx_component()['appsecret'];
        // $this->appsecret = !empty($config['appsecret']) ? $config['appsecret'] : Env::get('weixin_component.appsecret');
        $this->verifyTicket = $config['verify_ticket'] ?? '';
    }
    /**
     * 生成授权地址授权
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-03-25
     * @version 2021-03-25
     * @param array $config 配置信息
     * @param string $redirect_uri get通知的url
     * @param integer $auth_type   授权的帐号类型 
     *                              1 则商户扫码后，手机端仅展示公众号
     *                              2 表示仅展示小程序
     *                              3 表示公众号和小程序都展示
     *                              如果为未指定，则默认小程序和公众号都展示。第三方平台开发者可以使用本字段来控制授权的帐号类型。
     * @return void
     */
    public function auth($redirect_uri = '', $auth_type = 1)
    {

        $url = 'https://mp.weixin.qq.com/cgi-bin/componentloginpage?';
        $url .= 'component_appid=' .  $this->appid; //第三方平台方 appid
        $url .= '&pre_auth_code=' . $this->getPreAuthCode(); //预授权码
        $url .= '&redirect_uri=' . urlencode($redirect_uri);
        $url .= '&auth_type=' . $auth_type; //授权的帐号类型 
        return  $url;
    }

    /**
     * 获取预授权码
     * 由于平台共用，所以这个暂且存在缓存
     * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/api/pre_auth_code.html
     * @description 
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-03-23
     * @version 2021-03-23
     * @param array $config 配置信息
     * @return void
     */
    private function getPreAuthCode()
    {

        $pre_auth_code = Cache::get('pre_auth_code');

        if (!empty($pre_auth_code)) {
            return $pre_auth_code;
        }
        /**
         * 调用接口获取预授权码
         * POST
         * https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token=COMPONENT_ACCESS_TOKEN
         */
        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode';
        $data = [
            'component_access_token' => $this->getComponentAccessToken(),
            'component_appid' => $this->appid
        ];
        $request_data = self::curlpost($url, $data);
        $request_data = !empty($request_data) ? json_decode($request_data, true) : [];

        $pre_auth_code = $request_data['pre_auth_code'] ?? '';
        Cache::set('pre_auth_code', $pre_auth_code, $request_data['expires_in'] ?? 0);
        return $pre_auth_code;
    }

    /**
     * 获取令牌
     * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/api/component_access_token.html
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-03-23
     * @version 2021-03-23
     * @param array $config
     * @return void
     */
    public  function getComponentAccessToken()
    {
        $component_access_token = Cache::get('component_access_token');

        if (!empty($component_access_token)) {
            return $component_access_token;
        }

        /**
         * 调用接口获取预授权码
         * POST
         * https://api.weixin.qq.com/cgi-bin/component/api_component_token
         */

        $request_data = self::curlpost('https://api.weixin.qq.com/cgi-bin/component/api_component_token', [
            'component_appid' => $this->appid, //第三方平台 appid
            'component_appsecret' =>  $this->appsecret, //第三方平台 appsecret
            'component_verify_ticket' => $this->verifyTicket
        ]);
        $request_data = !empty($request_data) ? json_decode($request_data, true) : [];
        $component_access_token = $request_data['component_access_token'] ?? '';
        if (isset($request_data['expires_in'])) {
            Cache::set('component_access_token', $component_access_token, $request_data['expires_in'] - 10);
        }
        return $component_access_token;
    }
    /**
     * 获取授权方的帐号基本信息
     * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/api/api_get_authorizer_info.html
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-03-23
     * @version 2021-03-23
     * @param array $config 第三方平台配置
     * @param string $authorizer_appid //授权账号的appid
     * @return void
     */
    public function getAuthorizerInfo($authorizer_appid = '')
    {


        /**
         * 获取授权方的帐号基本信息
         * POST
         * https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info
         */

        $request_data =  self::curlpost('https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info', [
            'component_access_token' => $this->getComponentAccessToken(),
            'component_appid' => $this->appid,
            'authorizer_appid' => $authorizer_appid,
        ]);
        $request_data = !empty($request_data) ? json_decode($request_data, true) : [];
        return $request_data;
    }
    /**
     * 使用授权码获取授权信息
     * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/api/authorization_info.html
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-03-25
     * @version 2021-03-25
     * @param array $config 第三方平台配置文件
     * @param string $authorization_code 授权账号的code
     * @return void
     */
    public function getQueryAuth($authorization_code = '')
    {
        /**
         * POST
         * https://api.weixin.qq.com/cgi-bin/component/api_query_auth

         * @param string $component_access_token 第三方平台component_access_token，不是authorizer_access_token
         * @param string $component_appid   第三方平台 appid
         * @param string $authorization_code 授权码, 会在授权成功时返回给第三方平台，详见第三方平台授权流程说明
         * @return void
         */
        $component_appid =  $this->appid; //第三方平台 appid
        $request_data =  self::curlpost('https://api.weixin.qq.com/cgi-bin/component/api_query_auth', [
            'component_access_token' => $this->getComponentAccessToken(),
            'component_appid' => $component_appid,
            'authorization_code' => $authorization_code,
        ]);

        return !empty($request_data) ? json_decode($request_data, true) : [];;
    }


    private static function curlpost($url = '', $params = [], $header = [], $retry = 0)
    {

        $retry_max = 10; //最大失败重试次数
        $new_url = $url;
        $new_url .= '?';
        foreach ($params as $key => $val) {
            $new_url .= urlencode($key) . '=' . urlencode($val) . '&';
        }
        $headers = [
            'Content-Type: application/json',
        ];
        foreach ($header as $val) {
            $headers[]  = $val;
        }
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $new_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        if (!$response && ++$retry < $retry_max) {
            return self::curlpost($url, $params, $header, $retry);
        } else {
            return $response;
        }
    }

    public static function post($url = '', $params = [],  $header = [])
    {
        return self::sendRequest($url, $params, 'POST', $header);
    }
    public static function get($url = '', $params = [],  $header = [])
    {
        return self::sendRequest($url, $params, 'GET', $header);
    }

    private static function sendRequest($url = '', $params = [], $method = 'POST', $header = [])
    {

        $headers = [
            'Content-Type: application/json',
        ] + $header;

        $method = strtoupper($method);
        $protocol = substr($url, 0, 5);

        if ($method == 'GET') {
            $query_string = is_array($params) ? http_build_query($params) : $params;
            $url = $query_string ? $url . (stripos($url, "?") !== false ? "&" : "?") . $query_string : $url;
        }


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
}
