<?php

namespace Littlemo\Tool\wechat\mp;

class Common
{

    /****************************************************
     *  微信提交API方法，返回微信指定JSON
     *  通用请求微信接口 [ 微信通讯 Communication ]
     ****************************************************/
    protected function wxHttpsRequest($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        // curl_setopt($curl,CURLOPT_HEADER,0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );

        $output = curl_exec($curl);
        $is_errno = curl_errno($curl);
        if ($is_errno) {
            return 'Errno' . $is_errno;
        }
        curl_close($curl);
        return $output;
    }
}
