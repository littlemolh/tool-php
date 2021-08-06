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

namespace littlemo\tool\word;

use PhpOffice\PhpWord\IOFactory;

class WordToHtml
{
    static $msg = [];

    static $creator = '';
    static $company = '';
    static $title = '';
    static $description = '';
    static $category = '';
    static $lastModifiedBy = '';
    static $created = '';
    static $modified = '';
    static $subject = '';
    static $keywords = '';
    /**
     * docx文件转html内容
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-08-05
     * @version 2021-08-05
     * @param string $file  文件路径，仅支持docx类型
     * @param string $cache html缓存路径
     * @return boolean
     */
    public function parsing($file = '', $cache)
    {
        self::$msg = [
            'name' => basename($file)
        ];
        try {
            //code...

            $ext_name = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if ($ext_name != 'docx') {
                throw new \Exception('文件格式不支持，请上传docx格式word文件', 401);
            }
            $filesize = filesize($file);
            $unit = 'B';
            if ($filesize >= 1024) {
                $filesize = bcdiv($filesize, 1024, 2);
                $unit = "K";
            }
            if ($filesize >= 1024) {
                $filesize = bcdiv($filesize, 1024, 2);
                $unit = "M";
            }
            if ($filesize >= 1024) {
                $filesize = bcdiv($filesize, 1024, 2);
                $unit = "G";
            }
            self::$msg['size'] = $filesize;
            self::$msg['unit'] = $unit;

            //创建html
            $phpWord = IOFactory::load($file);
            $properties = $phpWord->getDocInfo();
            $this->setDocinfo($properties);
            $xmlWriter = IOFactory::createWriter($phpWord, "HTML");
            $xmlWriter->save($cache);

            //读取html内容
            self::$msg['html'] = self::getFile($cache);
        } catch (\Exception $e) {
            self::$msg['error'] = $e->getMessage();
            self::$msg['errorCode'] = $e->getCode();
        }

        return true;
    }

    /**
     * 批量设置文档属性
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-08-06
     * @version 2021-08-06
     * @param array $date
     */
    public static function setProperties($date)
    {
        //创建者
        isset($date['creator']) && self::$creator = $date['creator'];
        //公司
        isset($date['company']) && self::$company = $date['company'];
        //标题
        isset($date['title']) && self::$title = $date['title'];
        //描述
        isset($date['$description']) && self::$description = $date['$description'];
        //分类
        isset($date['category']) &&  self::$category = $date['category'];
        //最后修改者
        isset($date['last_modified_by']) && self::$lastModifiedBy = $date['last_modified_by'];
        //创建时间 时间戳
        isset($date['created']) && self::$created = strtotime($date['created']);
        //修改时间 时间戳
        isset($date['modified']) && self::$modified = strtotime($date['modifie']);
        //主题
        isset($date['subject']) && self::$subject = $date['subject'];
        //关键词'my, key, word'
        isset($date['keywords']) && self::$keywords = $date['keywords'];
    }

    /**
     * 设置文档属性 - 创建者
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-08-06
     * @version 2021-08-06
     * @param string $date
     */
    public static function setCreator($date)
    {
        //创建者
        self::$creator = $date;
    }

    /**
     * 设置文档属性 - 公司
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-08-06
     * @version 2021-08-06
     * @param string $date
     */
    public static function setCompany($date)
    {
        //公司
        self::$company = $date;
    }

    /**
     * 设置文档属性 - 标题
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-08-06
     * @version 2021-08-06
     * @param string $date
     */
    public static function setTitle($date)
    {
        //标题
        self::$title = $date;
    }

    /**
     * 设置文档属性 - 描述
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-08-06
     * @version 2021-08-06
     * @param string $date
     */
    public static function setDescription($date)
    {
        //描述
        self::$description = $date;
    }

    /**
     * 设置文档属性 - 分类
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-08-06
     * @version 2021-08-06
     * @param string $date
     */
    public static function setCategory($date)
    {
        //分类
        self::$category = $date;
    }

    /**
     * 设置文档属性 - 最后修改者
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-08-06
     * @version 2021-08-06
     * @param string $date
     */
    public static function setLastModifiedBy($date)
    {
        //最后修改者
        self::$lastModifiedBy = $date;
    }

    /**
     * 设置文档属性 - 创建时间
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-08-06
     * @version 2021-08-06
     * @param string $date 日期时间格式
     */
    public static function setCreated($date)
    {
        //创建时间 时间戳
        self::$created = strtotime($date);
    }

    /**
     * 设置文档属性 - 修改时间
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-08-06
     * @version 2021-08-06
     * @param string $date 日期时间格式
     */
    public static function setModified($date)
    {
        //修改时间 时间戳
        self::$modified = strtotime($date);
    }

    /**
     * 设置文档属性 - 主题
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-08-06
     * @version 2021-08-06
     * @param string $date
     */
    public static function setSubject($date)
    {
        //主题
        self::$subject = $date;
    }

    /**
     * 设置文档属性 - 关键词
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-08-06
     * @version 2021-08-06
     * @param string $date
     */
    public static function setKeywords($date)
    {
        //关键词'my, key, word'
        self::$keywords = $date;
    }

    /**
     * 开始设置文档属性 批量写入文件
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-08-06
     * @version 2021-08-06
     * @param [type] $properties
     * @return void
     */
    private function setDocinfo($properties)
    {
        //创建者
        !empty(self::$creator) && $properties->setCreator(self::$creator);
        //公司
        !empty(self::$company) && $properties->setCompany(self::$company);
        //标题
        !empty(self::$title) && $properties->setTitle(self::$title);
        //描述
        !empty(self::$description) && $properties->setDescription(self::$description);
        //分类
        !empty(self::$category) &&  $properties->setCategory(self::$category);
        //最后修改者
        !empty(self::$lastModifiedBy) && $properties->setLastModifiedBy(self::$lastModifiedBy);
        //创建时间 时间戳
        !empty(self::$created) && $properties->setCreated(strtotime(self::$created));
        //修改时间 时间戳
        !empty(self::$modified) && $properties->setModified(strtotime(self::$modified));
        //主题
        !empty(self::$subject) && $properties->setSubject(self::$subject);
        //关键词'my, key, word'
        !empty(self::$keywords) && $properties->setKeywords(self::$keywords);
    }


    /**
     * 获取word解析内容
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-08-05
     * @version 2021-08-05
     * @return array
     */
    public function getMessage()
    {
        return self::$msg;
    }

    /**
     * 获取文件内容
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-08-05
     * @version 2021-08-05
     * @param string $filename
     * @return string
     */
    private static function getFile($filename)
    {
        $return = '';
        if ($fp = fopen($filename, 'rb')) {
            while (!feof($fp)) {
                $return .= fread($fp, 1024);
            }
            fclose($fp);
            return $return;
        } else {
            return false;
        }
    }
}
