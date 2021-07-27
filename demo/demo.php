<?php
ini_set("display_errors", "stderr");  //ini_set函数作用：为一个配置选项设置值，

error_reporting(E_ALL);     //显示所有的错误信息

require_once './vendor/autoload.php';

use littlemo\tool\RequestRate;

$requestRate = new RequestRate([
    'prefix' => 'ip:',
    'cache' => [
        'host' => 'redis',
        'select' => 10
    ]
]);

if ($requestRate->check() == false) {
    print_r($requestRate->getMessage());
} else {
    echo '次数未达到警报值';
}
