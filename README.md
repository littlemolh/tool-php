
littlemo tool
===============

[![Total Downloads](https://poser.pugx.org/littlemo/tool/downloads)](https://packagist.org/packages/littlemo/tool)
[![Latest Stable Version](https://poser.pugx.org/littlemo/tool/v/stable)](https://packagist.org/packages/littlemo/tool)
[![Latest Unstable Version](https://poser.pugx.org/littlemo/tool/v/unstable)](https://packagist.org/packages/littlemo/tool)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D7.2-8892BF.svg)](http://www.php.net/)
[![License](https://poser.pugx.org/littlemo/tool/license)](https://packagist.org/packages/littlemo/tool)

### 介绍
php常用工具库

#### 软件架构
基于ThinkPHP


### 安装教程

composer.json
```json
{
    "require": {
        "php": "^7.4",
        "littlemo/tool": "*"
    }
}
```

### 使用说明

#### 统计单位时间内同一个IP请求次数

>需要安装`redis`扩展,并启动 `redis` 服务

##### 示例代码


```php
use littlemo\tool\RequestRate;
$config=[
    'prefix'=>'ip',//缓存前缀
    'time'=>'60',//单位时间（s）
    'maxCount'=>'30',//单位时间最大请求次数
    'cache'=>[
        'type' => 'redis',//缓存类型，目前仅支持redis
        'host' => '127.0.0.1',//缓存服务连接地址
        'port' => '6379',//缓存服务端口
        'select' => 0,//redis库号，一般取值范围（0-15）
    ]
]

//实例化对象
$requestRate = new RequestRate($config);

//获取错误信息
$error = $requestRate->$getMessage();

//初始化缓存服务,实例化对象时回自动初始化缓存服务
$requestRate->setCacheObj();

//验证器
$result = $requestRate->check();
if($result === false){
    echo $requestRate->$getMessage();
}else{
    echo '未达到请求次数上限';
}

```

#### 自动更新Git

> 仅支持gitee

##### 示例代码


```php
use littlemo\tool\Git;

$token = 'XXXXXXX';

//实例化对象
$Git = new Git($token);

//验证器
$error = $Git->check($token);

/**
* 拉取代码
* @param string $path   执行脚本相对路径；默认：'..'
* @param string $exec   执行脚本；默认：'git pull origin master'
*/
$Git->pull($path, $exec);

```
- 拉取代码的日志会直接在页面输出

#### 下载文件

##### 示例代码


```php
use littlemo\tool\Download;

/**
* 下载文件
* @param string $file      文件路径（文件所在磁盘的绝对路径）
* @param string $filename  带后缀的文件名称
*/

Download::file($file, $filename);

```



### 参与贡献

1.  littlemo


### 特技

- 统一、精简
