<?php

/**
 * Created by PhpStorm.
 * User: qixieyu
 * Date: 15-7-3
 * Time: 下午8:41
 */
class Model
{
    /**
     * 表名
     *
     * @var string
     */
    protected $table = '';

    /**
     * 数据库链接名
     *
     * @var string
     */
    protected $connection = '';

    /**
     * 获取数据库链接配置
     *
     * @return array|null
     */
    public function getConnection()
    {
        $config = C($this->connection);

        $config['tablename'] = $config['database'] . '.' . $config['prefix'] . $this->table;

        return $config;
    }

    /**
     * 静态方法调用
     *
     * @param      $method
     * @param null $param
     *
     * @return mixed
     */
    public static function __callStatic($method, $param = null)
    {
        $instance = new Eloquent(new static);

        switch (count($param)) {
            case 0 :
                return $instance->$method();
            case 1 :
                return $instance->$method($param[0]);
            case 2 :
                return $instance->$method($param[0], $param[1]);
            case 3 :
                return $instance->$method($param[0], $param[1], $param[2]);
        }
    }
}