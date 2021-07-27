<?php
ini_set("display_errors", "stderr");  //ini_set函数作用：为一个配置选项设置值，

error_reporting(E_ALL);     //显示所有的错误信息

require_once '../vendor/autoload.php';

use littlemo\tool\Zip;

$zip = new Zip();
// $zip->addFile(__DIR__ . '/../src/File.php');
if ($zip::create(__DIR__ . '/123/456/') == false) {
    var_dump($zip::getMessage());
}
$zip::addFile(__DIR__ . '/../src/Zip.php');
$zip::addFile(__DIR__ . '/../src/Git.php');
$zip::addFile(__DIR__ . '/../src/model', 'model');
var_dump($zip::save());
