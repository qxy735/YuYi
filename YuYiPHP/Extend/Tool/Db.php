<?php

/**
 * Created by PhpStorm.
 * User: qixieyu
 * Date: 15-7-3
 * Time: 下午8:41
 */
class Db
{
    /**
     * 数据库链接句柄
     *
     * @var null
     */
    private static $link = null;

    /**
     * 操作返回结果
     *
     * @var null
     */
    protected static $resource = null;

    /**
     * 数据库链接配置
     *
     * @var array
     */
    protected $config = array();

    /**
     * 数据库名
     *
     * @var string
     */
    private static $database = '';

    /**
     * 数据库链接初始化操作
     */
    public function __construct()
    {
        $this->connect();

        $this->selectDatabase();

        $this->setCharset();
    }

    /**
     * 设置数据库链接配置
     *
     * @param null $config
     */
    protected function setConfig($config = null)
    {
        $this->config = $config;
    }

    /**
     * 链接 Mysql 数据库服务器
     */
    private function connect()
    {
        if (is_null(self::$link) || (self::$database != $this->config['database'])) {

            $link = mysqli_connect($this->config['host'], $this->config['username'], $this->config['password']);

            $link or Halt('数据库链接失败: ' . mysqli_error($link));

            self::$database = $this->config['database'];

            self::$link = $link;
        }
    }

    /**
     * 选择数据库
     */
    private function selectDatabase()
    {
        $result = mysqli_select_db(self::$link, $this->config['database']);

        $result or Halt('选择数据库失败: ' . mysqli_error(self::$link));
    }

    /**
     * 设置数据库字符编码
     */
    private function setCharset()
    {
        $result = mysqli_set_charset(self::$link, $this->config['charset']);

        $result or Halt('设置数据库字符编码失败: ' . mysqli_error(self::$link));
    }

    /**
     * 执行数据库操作命令
     *
     * @param string $sql
     */
    public function query($sql = '')
    {
        $result = mysqli_query(self::$link, $sql);

        $result or Halt('数据库操作失败：' . mysqli_error(self::$link));

        self::$resource = $result;
    }

    /**
     * 执行对数据库的增删改查操作命令
     *
     * @param string $sql
     *
     * @return array|int
     */
    public static function select($sql = '')
    {
        $result = mysqli_query(self::$link, $sql);

        $result or Halt('数据库操作失败：' . mysqli_error(self::$link));

        $data = array();

        if (is_bool($result)) {
            return mysqli_affected_rows(self::$link);
        }

        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        return $data;
    }

    /**
     * 获取受影响行数
     *
     * @return int
     */
    protected function getAffectedRows()
    {
        return mysqli_affected_rows(self::$link);
    }
}