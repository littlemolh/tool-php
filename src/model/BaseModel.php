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

namespace littlemo\tool\model;

use think\Model;
use think\Cache;

class BaseModel extends Model
{
    // 表名

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $tablePrimary = 'id';
    protected $deleteTime = false;

    protected $page = 1;
    protected $pagesize = 10;

    // 追加属性
    protected $append = [];


    /**
     * 获取列表数据
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-04-02
     * @version 2021-04-02
     * @param array $params 筛选条件
     * @return void
     */
    public function getListData($params = [], $_wsql = '')
    {
        $data = [];
        $wsql = $this->commonWsql($params);
        $data['data'] = $this->where($wsql)
            ->where($_wsql)
            ->page($params['page'] ?? $this->page, $params['pagesize'] ?? $this->pagesize)
            ->order($params['orderby'] ?? $this->tablePrimary, $params['orderway'] ?? 'desc')
            ->select();
        $data['total'] = $this->totalCount($params)['count'] ?? 0;
        return $data;
    }



    /**
     * 统计数量
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-04-02
     * @version 2021-04-02
     * @param array $params 筛选条件
     * @return void
     */
    public function totalCount($params = [])
    {
        $data = [];

        $wsql = $this->commonWsql($params);

        //处理时间-若不限定时间则查询数据的开始和结束时间
        $start_time = !empty($params['start_date']) ? strtotime($params['start_date']) : 0;
        $end_time = !empty($params['end_date']) ? strtotime($params['end_date']) : 0;

        if (empty($start_time)) {
            $start_time = $this->where($wsql)->min($this->createTime);
        } else {
            $wsql .= !empty($start_time) ? ' AND ' . $this->createTime . ' >' . $start_time  : null;
        }
        if (empty($end_time)) {
            $end_time = $this->where($wsql)->max($this->createTime);
        } else {
            $wsql .= !empty($end_time) ? ' AND ' . $this->createTime . ' <' . $end_time  : null;
        }

        $data['count'] = $this->where($wsql)->count();

        $data['start_time'] = $start_time;
        $data['start_date'] = date('Y-m-d H:i:s', $start_time);
        $data['end_time'] = $end_time;
        $data['end_date'] = date('Y-m-d H:i:s', $end_time);
        return $data;
    }

    /**
     * 统计某个字段数字总和
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-04-02
     * @version 2021-04-02
     * @param array $params 筛选条件
     * @param string $field 字段名
     * @return void
     */
    public function totalSum($params = [], $field = '')
    {
        $data = [];

        $wsql = $this->commonWsql($params);

        //处理时间-若不限定时间则查询数据的开始和结束时间
        $start_time = !empty($params['start_date']) ? strtotime($params['start_date']) : 0;
        $end_time = !empty($params['end_date']) ? strtotime($params['end_date']) : 0;

        if (empty($start_time)) {
            $start_time = $this->where($wsql)->min($this->createTime);
        } else {
            $wsql .= !empty($start_time) ? ' AND ' . $this->createTime . ' >' . $start_time  : null;
        }
        if (empty($end_time)) {
            $end_time = $this->where($wsql)->max($this->createTime);
        } else {
            $wsql .= !empty($end_time) ? ' AND ' . $this->createTime . ' <' . $end_time  : null;
        }

        $data[$field . '_sum'] = $this->where($wsql)->sum($field);

        $data['start_time'] = $start_time;
        $data['start_date'] = date('Y-m-d H:i:s', $start_time);
        $data['end_time'] = $end_time;
        $data['end_date'] = date('Y-m-d H:i:s', $end_time);
        return $data;
    }


    protected  function commonWsql($params = [])
    {
        $wsql = '1=1';
        return $wsql;
    }
    /**
     * 分组统计
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-04-02
     * @version 2021-04-02
     * @param array $params 筛选条件
     * @param string $group 分组字段
     * @param array $field  查询字段
     * @return void
     */
    public function getGroupListData($params = [], $group = '', $field = [])
    {
        $data = [];

        $wsql  = $this->commonWsql($params);

        //处理时间-若不限定时间则查询数据的开始和结束时间
        $start_time = !empty($params['start_date']) ? strtotime($params['start_date']) : 0;
        $end_time = !empty($params['end_date']) ? strtotime($params['end_date']) : 0;

        if (empty($start_time)) {
            $start_time = $this->where($wsql)->min('createtime');
        } else {
            $wsql .= !empty($start_time) ? ' AND createtime >' . $start_time  : null;
        }
        if (empty($end_time)) {
            $end_time = $this->where($wsql)->max('createtime');
        } else {
            $wsql .= !empty($end_time) ? ' AND createtime <' . $end_time  : null;
        }

        //整理字段
        $fields = [];
        $fields[] = ['count(*)' => 'count'];
        $fields[] = $group;
        foreach ($field as $val) {
            $fields[] = $val;
        }

        $data['list'] = $this
            ->field($field)
            ->where($wsql)
            // ->order('createtime desc')
            ->group($group)
            ->page($params['page'] ?? $this->page, $params['pagesize'] ?? $this->pagesize)
            ->select();

        $data['total'] = $this->where($wsql)->group($group)->count();

        $data['start_time'] = $start_time;
        $data['start_date'] = !empty($start_time) ? date('Y-m-d H:i:s', $start_time) : null;
        $data['end_time'] = $end_time;
        $data['end_date'] = !empty($end_time) ? date('Y-m-d H:i:s', $end_time) : null;
        return $data;
    }

    /**
     * 获取一条缓存数据
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-07-03
     * @version 2021-07-03
     * @param [type] $id
     * @return void
     */
    public function getRowDataCache($id)
    {
        $name = $this->getRowDataCacheName($id);
        if (Cache::has($name)) {
            return Cache::get($name);
        }
        $data = self::get($id);
        Cache::set($name, $data, 3600);
        return $data;
    }
    /**
     * 删除一条缓存数据
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-07-03
     * @version 2021-07-03
     * @param [type] $id
     * @return void
     */
    public function rmRowDataCache($id)
    {
        $name = $this->getRowDataCacheName($id);
        if (Cache::has($name)) {
            return Cache::rm($name);
        }
    }

    /**
     * 生成单挑信息缓存名称
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-07-07
     * @version 2021-07-07
     * @param [type] $id
     * @return void
     */
    private function getRowDataCacheName($id)
    {
        return str_replace('_', '-', $this->table) . '-row-data-' . $id;
    }
    /**
     * 添加一条记录
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-07-01
     * @version 2021-07-01
     * @param array $params
     * @return void
     */
    public function add($params = [], $allowField = [])
    {
        if ($this->allowField($allowField ?: true)->save($params)) {
            return $this->id;
        } else {
            return false;
        }
    }

    /**
     * 编辑一条记录
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-07-01
     * @version 2021-07-01
     * @param int $id 主键ID
     * @param array $params 编辑内容
     * @return void
     */
    public function edit($id = 0, $params = [])
    {
        //清除缓存
        $this->rmRowDataCache($id);

        $detail = $this->get($id);
        return $detail->save($params);
    }

    /**
     * 删除一条记录
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-07-01
     * @version 2021-07-01
     * @param int $id 主键ID
     * @return void
     */
    public function del($id = 0)
    {
        $detail = $this->get($id);
        return $detail->delete();
    }
}
