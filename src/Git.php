<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: luofei614 <weibo.com/luofei614>
// +----------------------------------------------------------------------
// | 修改者: anuo (本权限类在原3.2.3的基础上修改过来的)
// +----------------------------------------------------------------------

namespace littlemo\tool;


class Git
{

    /**
     * @var object 对象实例
     */
    protected static $errorMsg = '请先验证';

    /**
     * 构造函数
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-07-06
     * @version 2021-07-06
     * @param string $token
     */
    public function __construct($token = '')
    {
        $this->check($token);
    }

    /**
     * 鉴权
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-04-16
     * @version 2021-04-16
     * @param string $localToken
     * @return void
     */
    public function check($localToken = '')
    {
        $distalToken = $_GET['token'] ?? $_SERVER['HTTP_X_GITEE_TOKEN'] ?? 'k';
        if (empty($localToken)) {
            self::$errorMsg = '请先配置Token';
        } else {
            if ($localToken != $distalToken) {
                self::$errorMsg = '权限不足';
            } else {
                self::$errorMsg = null;
            }
        }
    }

    /**
     * 拉取代码
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-04-16
     * @version 2021-04-16
     * @param string $path
     * @param string $exec
     * @return void
     */
    public function pull($path = '..', $exec = 'git pull origin master')
    {

        $exec = "cd $path && git config core.filemode false &&  $exec";
        $this->doAction($exec);
    }

    function doAction($action)
    {
        if (self::$errorMsg) {
            self::printOut(self::$errorMsg);
            return false;
        }
        exec($action, $out, $res);
        self::printOut($out);
    }

    private static function printOut($out)
    {
        // $logFile = "./log/" . date('Y-m-d') . ".txt";
        echo "<pre>\n";
        if (is_array($out)) {
            foreach ($out as $o) {
                // file_put_contents($logFile, "{$o}\n", FILE_APPEND);
                echo $o;
                echo "\n";
            }
        } else {
            echo $out;
            echo "\n";
        }
        echo "\n";
        echo "时间：" . date('Y-m-d H:i:s');
        echo "\n";
        echo "------------------------------------------------------------------------------------\n";
        echo "</pre>";
    }
}
