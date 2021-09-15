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

namespace littlemo\tool;

use ZipArchive;
use littlemo\tool\File;

class Zip
{

    /**
     * @var string 压缩包完整路径
     */
    static $zipFileName = '';

    /**
     * @var array 被压缩文件路径
     */

    static $fileDir = [];

    /**
     * @var array 被压缩文件名
     */
    static $file = [];

    /**
     * @var array 被压缩文件类型
     */
    static $fileTyle = '*';

    /**
     * @var array 被忽略的文件
     */
    static $ignored = [];

    /**
     * @var object
     */
    static $zip = null;

    /**
     * @var string 错误信息
     */
    static $error = null;


    /**
     * 构造函数
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-07-26
     * @version 2021-07-26
     * @param array $config
     */
    public function __construct($config = [])
    {
        if (isset($config['zip_file_name']) && !empty($config['zip_file_name']) || (isset($config['file_dir']) && !empty($config['file_dir']))) {
            self::create($config['zip_file_name']);
        }

        if (isset($config['file_dir']) && !empty($config['file_dir'])) {
            self::addFile($config['file_dir']);
        }
    }

    /**
     * 设置压缩包存放完整路径
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-07-26
     * @version 2021-07-26
     * @param string $zipFileName
     * @return void
     */
    public static function create($fileName = '')
    {
        if (empty($fileName)) {
            $fileName =  __DIR__ . '/'; //dirname(__DIR__, 3);
        }
        if (substr($fileName, -1) == '/') {
            $fileName .= date('Y-m-d_His') . '_' . rand(10000, 99999);
        }
        if (substr($fileName, -4) != '.zip') {
            $fileName .= '.zip';
        }

        self::$zipFileName = $fileName;
        self::$zip =  new ZipArchive();

        if (self::$zip->open(self::$zipFileName, ZipArchive::CREATE) != true) {
            self::addErrormsg('文件打开或创建失败');
            return false;
        }
        return true;
    }

    /**
     * 设置被忽略的文件
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-07-26
     * @version 2021-07-26
     * @return void
     */
    public static function setIgnored($ignored = [])
    {
        if (empty($ignored)) {
            $ignored = [];
        }
        if (!is_array($ignored)) {
            $ignored = [$ignored];
        }

        self::$ignored = $ignored;
    }

    /**
     * 设置文件类型
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-07-26
     * @version 2021-07-26
     * @param array $fileType
     * @return void
     */
    public static function setFileType($fileType = null)
    {
        if (!empty($fileType)) {
            if (!is_array($fileType)) {
                $fileType = [$fileType];
            }
        }
        self::$fileTyle = $fileType;
    }

    /**
     * 添加文件
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-07-26
     * @version 2021-07-26
     * @return void
     */
    public static function addFile($dir = null, $zip_root_path = '')
    {
        try {
            if (self::$zip == null) {
                self::create();
            }
            if (self::$error != null) {
                throw new \Exception();
            }

            $fileData = self::getFile($dir);
            $fileList = $fileData['list'];
            $fileDir = $fileData['dir'];
            $ignoreLength =  strlen($fileDir) + 1;

            $dir = rtrim($dir, '/');
            $dir = rtrim($dir, '\\');

            foreach ($fileList as $file) {
                $zipPath = (!empty($zip_root_path) ? $zip_root_path . '/' : '') . substr($file, $ignoreLength);
                if (self::$zip->addFile($file, $zipPath) != true) {
                    throw new \Exception('文件[' . $file . ']添加至压缩包[' . $zipPath . ']失败');
                }
            }

            return true;
        } catch (\Exception $e) {
            self::addErrormsg($e->getMessage());
            return false;
        }
    }

    /**
     * 添加文件
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-07-26
     * @version 2021-07-26
     * @return void
     */
    public static function save()
    {
        try {
            $dir = dirname(self::$zipFileName);
            if (!file_exists($dir)) {
                if (!mkdir($dir, 0777, true)) {
                    throw new \Exception('创建文件夹[' . $dir . ']失败');
                }
            }

            if (self::$zip->close() != true) {
                throw new \Exception('文件压缩失败');
            }
        } catch (\Exception $e) {
            self::addErrormsg($e->getMessage());
            return false;
        }

        return self::$zipFileName;
    }

    public static function getMessage()
    {
        return implode("\n", self::$error ?: []);
    }


    private static function getFile($dir = null)
    {
        $dir = File::getRealPath($dir);
        $dir = rtrim($dir, '/');
        $dir = rtrim($dir, '\\');

        $fileList = [];
        if (is_file($dir)) {
            $fileList[] = $dir;
            $newDir = dirname($dir);
        } elseif (is_dir($dir)) {
            $newDir = $dir;

            $temp  = File::scandirFolder($dir, false);
            foreach ($temp as $val) {
                $fileList[] = $val;
            }
        }

        return ['dir' => $newDir, 'list' => $fileList];
    }

    private static function addErrormsg($msg)
    {
        self::$error[] = '文件打开或创建失败';
    }
}
