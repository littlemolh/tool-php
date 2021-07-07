# littlmeo-tool-php

### 介绍
php常用工具库

#### 软件架构
基于ThinkPHP


### 安装教程

1.  在项目根目录找到并打开 `composer.json` 
2.  在 `composer.json` 中 `require` 节点添加 `"littlemo/tool": "1.0.0"`
3.  执行脚本 `composer update` 

### 使用说明

#### 统计单位时间内同一个IP请求次数

>需要安装`redis`扩展,并启动 `redis` 服务

##### 示例代码


```php
use Littlemo\Tool\RequestRate;
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
if($result === true){
    echo $error;
}else{
    echo '未达到请求次数上限';
}

```

#### 自动更新Git

> 仅支持gitee

##### 示例代码


```php
use Littlemo\Tool\Git;
$token = 'XXXXXXX';

//实例化对象
$git = new Git($token);

//验证器
$error = $git->check();

//拉取代码
$path = '..';//执行脚本相对路径
$exec = 'git pull origin master';//执行脚本
$requestRate->pull($path, $exec);

```
- 拉取代码的日志会直接在页面输出



### 参与贡献

1.  littlemo


### 特技

1.  
