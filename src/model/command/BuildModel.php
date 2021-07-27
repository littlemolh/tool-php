<?php

// +----------------------------------------------------------------------
// | Little Mo - Tool [ WE CAN DO IT JUST TIDY UP IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://ggui.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: littlemo <25362583@qq.com>
// +----------------------------------------------------------------------

namespace littlemo\tool\model\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\console\input\Option;
use think\console\input\Argument;
use think\Db;

class BuildModel extends Command
{
    protected function configure()
    {
        $this->setName('build-model')
            ->addArgument('app', Argument::OPTIONAL, "app name,defaule:common") //应用目录，默认：common
            ->addOption('app', 'a', Option::VALUE_REQUIRED, 'app name') //应用目录，默认：common
            ->addOption('dirpath', 'd', Option::VALUE_REQUIRED, 'dir path')
            ->addOption('prefix', 'p', Option::VALUE_REQUIRED, 'table name prefix') //表名前缀
            ->setDescription('Here is the remark ');
    }

    protected function execute(Input $input, Output $output)
    {
        $app = 'common';
        $dir = __DIR__;
        $prefix = '';
        if ($input->hasArgument('app')) {
            $app = trim($input->getArgument('app')) ?: $app;
        }

        if ($input->hasOption('app')) {
            $app = $input->getOption('app') ?: $app;
        }
        if ($input->hasOption('dir')) {
            $dir = $input->getOption('dir');
        } else {
            $dir = dirname($dir, 2) . '/' . $app . '/model/';
        }
        if ($input->hasOption('prefix')) {
            $prefix = $input->getOption('prefix');
        }

        $namespace = 'app\\' . $app . '\\model';

        $newModel = $this->getNewModel($dir, $prefix, $output);
        $this->createModel($newModel, $dir, $namespace, $output);

        $output->info("Build Successed!");
    }
    private function getNewModel($dir, $p, $output)
    {
        $model = $newModel = $files = $tables = [];
        $database = config('database.database');
        $prefix = config('database.prefix');


        //获取model列表
        if (is_dir($dir)) {
            $files = scandir($dir);
        }
        foreach ($files as $val) {
            if ($val != '.' && $val != '..' && is_file($dir . '/' . $val)) {
                $model[] = self::humpToLine(explode('.', $val)[0]);
            }
        }
        //获取数据库table_name列表
        foreach (Db::Query('select table_name from information_schema.tables where table_schema=\'' . $database . '\'') as $val) {
            $tables[] = $val['TABLE_NAME'];
        }
        foreach ($tables as $val) {
            $isnew = true;
            if (!empty($p) && substr($val, 0, strlen($p)) != $p) {
                continue;
            }
            foreach ($model as $v) {
                if ($val == $v || $val == ($prefix . $v)) {
                    $isnew = false;
                    break;
                }
            }
            if ($isnew) {
                $newModel[] = $val;
            }
        }
        return $newModel;
    }
    private function createModel($newModel, $dir, $namespace, $output)
    {
        $prefix = config('database.prefix');
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }


        foreach ($newModel as $val) {
            $createTime = 'false';
            $updateTime = 'false';
            $deleteTime = 'false';
            foreach (Db::Query('select COLUMN_NAME, column_comment from INFORMATION_SCHEMA.Columns where table_name=\'' . $val . '\'') as $v) {
                if ($v['COLUMN_NAME'] == 'createtime' || $v['COLUMN_NAME'] == 'intime') {
                    $createTime = '\'' . $v['COLUMN_NAME'] . '\'';
                }
                if ($v['COLUMN_NAME'] == 'updatetime' || $v['COLUMN_NAME'] == 'uptime') {
                    $updateTime = '\'' . $v['COLUMN_NAME'] . '\'';
                }
                if ($v['COLUMN_NAME'] == 'deletetime' || $v['COLUMN_NAME'] == 'deltime') {
                    $deleteTime = '\'' . $v['COLUMN_NAME'] . '\'';
                }
            };


            $table_name = str_replace($prefix, "", $val);
            $className = ucwords($this->convertUnderline($table_name));
            $contents = "<?php\n";
            $contents .= "\n";
            $contents .= "namespace " . $namespace . "; \n";
            $contents .= "\n";
            $contents .= "use littlemo\tool\model\BaseModel; \n";
            $contents .= "\n";
            $contents .= "class " . $className . " extends BaseModel \n{ \n";
            $contents .= "    // 表名 \n";
            $contents .= '    protected $' . (substr($val, 0, strlen($prefix)) == $prefix ? 'name' : 'table') . ' = \'' . $table_name . '\';' . " \n";
            $contents .= "    // 定义时间戳字段名 \n";
            $contents .= '    protected $createTime = ' . $createTime . ';' . " \n";
            $contents .= '    protected $updateTime = ' . $updateTime . ';' . " \n";
            $contents .= '    protected $deleteTime = ' . $deleteTime . ';' . " \n";
            $contents .= "    // 追加属性 \n";
            $contents .= '    protected $append = [];' . " \n";
            $contents .= "}";

            //要创建的两个文件
            $fileName = $dir . $className . '.php';
            //以读写方式打写指定文件，如果文件不存则创建
            if (($TxtRes = fopen($fileName, "w+")) === FALSE) {
                $output->info("创建模型 失败 " . $fileName);
                break;
            }
            if (!fwrite($TxtRes, $contents)) { //将信息写入文件
                $output->info("创建模型 失败 " . $fileName);
                fclose($TxtRes);
                break;
            }
            $output->info("创建模型 成功 " . $fileName);
            fclose($TxtRes); //关闭指针
        }
    }

    /*
     * 下划线转驼峰
     */
    static function convertUnderline($str)
    {
        $str = preg_replace_callback('/([-_]+([a-z]{1}))/i', function ($matches) {
            return strtoupper($matches[2]);
        }, $str);
        return $str;
    }

    /*
     * 驼峰转下划线
     */
    static function humpToLine($str)
    {
        $str = str_replace("_", "", $str);
        $str = preg_replace_callback('/([A-Z]{1})/', function ($matches) {
            return '_' . strtolower($matches[0]);
        }, $str);
        return ltrim($str, "_");
    }

    static function baseModelTemplate()
    {
    }
}
