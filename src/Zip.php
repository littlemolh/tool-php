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
     * 添加一个文件 可自定义文件在压缩包的路径和名称
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-09-16
     * @version 2021-09-16
     * @param string $filename      文件路径
     * @param string $zipFilename   添加至压缩包指定目录或者路径
     * @return void
     */
    public static function addFile($filename = null, $zipFilename = '')
    {

        try {
            if (self::$zip == null) {
                self::create();
            }
            if (self::$error != null) {
                throw new \Exception();
            }

            if (!is_file($filename)) {
                throw new \Exception('文件[' . $filename . '] 不存在');
            }

            $filename = File::getRealPath($filename);
            $zipFilename = File::getRealPath($zipFilename);

            $filenameInfo = pathinfo($filename);

            $zipFilename = rtrim($zipFilename, '/');
            $zipFilename = rtrim($zipFilename, '\\');
            if (empty($zipFilename)) {
                //无自定义路径或者文件名时，默认使用文件的basename
                $zipPath = $filenameInfo['basename'];
            } else {
                $zipPath = $zipFilename;
                if (pathinfo($zipPath, PATHINFO_EXTENSION) == '') {
                    $zipPath .= '/' . $filenameInfo['basename'];
                }
            }

            if (self::$zip->addFile($filename, $zipPath) != true) {
                throw new \Exception('文件[' . $filename . ']添加至压缩包[' . $zipPath . ']失败');
            }

            return true;
        } catch (\Exception $e) {
            self::addErrormsg($e->getMessage());
            return false;
        }
    }

    /**
     * 添加一个文件夹下的所有文件 可自定义文件在压缩包的根目录
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-09-16
     * @version 2021-09-16
     * @param string $dir           要添加文件的文件夹路径
     * @param string $zip_root_path 添加至压缩包指定目录下
     * @return void
     */
    public static function addFiles($dir = null, $zip_root_path = '')
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
     * 保存文件
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

    /**
     * 获取指定目录下文件列表
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-09-16
     * @version 2021-09-16
     * @param string $dir 文件夹路径
     * @return array
     */
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

    private static function addErrormsg($msg = '未知错误')
    {
        self::$error[] = $msg;
    }
}
