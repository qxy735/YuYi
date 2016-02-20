<?php

/**
 * Created by PhpStorm.
 * User: qixieyu
 * Date: 15-7-3
 * Time: 下午8:41
 */
class Eloquent extends Db
{
    /**
     * where 条件
     *
     * @var string
     */
    protected $where = '';

    /**
     * limit 条件
     *
     * @var string
     */
    protected $forPage = '';

    /**
     * order by 条件
     *
     * @var string
     */
    protected $orderBy = '';

    /**
     * group by 条件
     *
     * @var string
     */
    protected $groupBy = '';

    /**
     * Model 类对象
     *
     * @var null
     */
    private static $model = null;

    /**
     * 默认每页返回 20 条数据
     *
     * @var int
     */
    private $pageNum = 20;

    /**
     * 表名
     *
     * @var string
     */
    private static $table = '';

    /**
     * 实例化方法
     *
     * @param null $model
     */
    public function __construct($model = null)
    {
        $this->setModelObject($model);

        parent::__construct();
    }

    /**
     * 设置 Model 类型对象
     *
     * @param Model $model
     */
    public function setModelObject(Model $model)
    {
        $config = $model->getConnection();

        self::$table = $config['tablename'];

        $this->setConfig($config);

        self::$model = $model;
    }

    /**
     * 获取数据列表信息
     *
     * @param null $fields
     *
     * @return array
     */
    public function get($fields = null)
    {
        $sql = 'select * from ' . self::$table . $this->getCondation();

        $this->query($sql);

        return $this->returnData($fields);
    }

    /**
     * 获取第一条数据信息
     *
     * @return null
     */
    public function first()
    {
        $sql = 'select * from ' . self::$table . $this->getCondation();

        $this->query($sql);

        return array_get($this->getData(), 0, array());
    }

    /**
     * 获取单条数据记录
     *
     * @param null $id
     * @param null $fields
     *
     * @return array
     */
    public function find($id = null, $fields = null)
    {
        if (is_null($id)) {
            return array();
        }

        $this->where($id);

        $sql = 'select * from ' . self::$table . $this->getCondation();

        $this->query($sql);

        return $this->returnData($fields);
    }

    /**
     * 获取数据总记录数
     *
     * @return null
     */
    public function count()
    {
        $sql = 'select count(*) as count from ' . self::$table . $this->getCondation();

        $this->query($sql);

        return array_get($this->getData(), '0.count', 0);
    }

    /**
     * 添加数据方法
     *
     * @param null $data
     *
     * @return bool|int
     */
    public function save($data = null)
    {
        if (is_null($data)) {
            return false;
        }

        $sql = 'insert into ' . self::$table . ' set ' . $this->getUpdateFiled($data);

        $this->query($sql);

        return $this->getAffectedRows();
    }

    /**
     * 添加数据方法
     *
     * @param null $data
     *
     * @return bool|int
     */
    public function create($data = null)
    {
        return $this->save($data);
    }

    /**
     * 更新数据信息
     *
     * @param null $data
     *
     * @return bool|int
     */
    public function update($data = null)
    {
        if (is_null($data)) {
            return false;
        }

        $sql = 'update ' . self::$table . ' set ' . $this->getUpdateFiled($data) . $this->getCondation();

        $this->query($sql);

        return $this->getAffectedRows();
    }

    /**
     * 删除数据信息
     *
     * @return int
     */
    public function delete()
    {
        $sql = 'delete from ' . self::$table . $this->getCondation();

        $this->query($sql);

        return $this->getAffectedRows();
    }

    /**
     * 获取更新或添加字段信息
     *
     * @param $data
     *
     * @return string
     */
    private function getUpdateFiled($data)
    {
        $field = '';

        foreach ($data as $key => $value) {
            $field .= $key . '=' . $this->dealFieldType($value) . ', ';
        }

        return rtrim($field, ', ');
    }

    /**
     * 获取返回数据
     *
     * @param null $fields
     *
     * @return array
     */
    private function returnData($fields = null)
    {
        $datas = $this->getData();

        if (is_null($fields)) {
            return $datas;
        }

        $fields = is_array($fields) ? $fields : explode(',', $fields);

        $result = array();

        foreach ($datas as $key => $data) {
            foreach ($fields as $field) {
                if (isset($data[$field])) {
                    $result[$key][$field] = $data[$field];
                }
            }
        }

        return $result;
    }

    /**
     * 获取查询结果
     *
     * @return array
     */
    private function getData()
    {
        $data = array();

        while ($row = mysqli_fetch_assoc(self::$resource)) {
            $data[] = $row;
        }

        return $data;
    }

    /**
     * 获取 Sql 条件语句部分
     *
     * @return string
     */
    private function getCondation()
    {
        return $this->where . $this->groupBy . $this->forPage . $this->orderBy;
    }

    /**
     * 字段类型处理
     *
     * @param null $value
     *
     * @return null|string
     */
    private function dealFieldType($value = null)
    {
        return is_string($value) ? ("'" . $value . "' ") : $value;
    }

    /**
     * 获取 whereIn 字段条件方法
     *
     * @param string $params
     *
     * @return string
     */
    private function getWhereInString($params = '')
    {
        if (is_array($params)) {
            $result = '';

            foreach ($params as $param) {
                $result .= $this->dealFieldType($param) . ',';
            }

            $result = rtrim($result, ',');

            return $result;
        }

        return $params;
    }

    /**
     * where and 条件组装
     *
     * @return $this
     */
    public function where()
    {
        $params = func_get_args();

        $where = $this->where ? $this->where . ' and ' : ' where ';

        switch (func_num_args()) {
            case 0 :
                $where = rtrim($where, ' and');
                break;
            case 1 :
                $where .= ' id=' . $this->dealFieldType($params[0]);
                break;
            case 2 :
                $where .= $params[0] . '=' . $this->dealFieldType($params[1]);
                break;
            case 3 :
                $where .= $params[0] . ' ' . $params[1] . ' ' . $this->dealFieldType($params[2]);
        }

        $this->where = $where;

        return $this;
    }

    /**
     * where or 条件组装
     *
     * @return $this
     */
    public function orwhere()
    {
        $params = func_get_args();

        $where = $this->where ? $this->where . ' or ' : ' where ';

        switch (func_num_args()) {
            case 0 :
                $where = rtrim($where, ' or');
                break;
            case 1 :
                $where .= ' id=' . $this->dealFieldType($params[0]);
                break;
            case 2 :
                $where .= $params[0] . '=' . $this->dealFieldType($params[1]);
                break;
            case 3 :
                $where .= $params[0] . $params[1] . $this->dealFieldType($params[2]);
        }

        $this->where = $where;

        return $this;
    }

    /**
     * where In 条件组装
     *
     * @return $this
     */
    public function whereIn()
    {
        $params = func_get_args();

        $where = $this->where ? $this->where . ' and ' : ' where ';

        switch (func_num_args()) {
            case 0 :
                $where = rtrim($where, ' and');
                break;
            case 1 :
                $where .= ' id in(' . $this->getWhereInString($params[0]) . ') ';
                break;
            default :
                $where .= $params[0] . ' in(' . $this->getWhereInString($params[1]) . ') ';
        }

        $this->where = $where;

        return $this;
    }

    /**
     * where not in 条件组装
     *
     * @return $this
     */
    public function whereNotIn()
    {
        $params = func_get_args();

        $where = $this->where ? $this->where . ' and ' : ' where ';

        switch (func_num_args()) {
            case 0 :
                $where = rtrim($where, ' and');
                break;
            case 1 :
                $where .= ' id not in(' . $this->getWhereInString($params[0]) . ') ';
                break;
            default :
                $where .= $params[0] . ' not in(' . $this->getWhereInString($params[1]) . ') ';
        }

        $this->where = $where;

        return $this;
    }

    /**
     * limit 条件组装
     *
     * @return $this
     */
    public function forPage()
    {
        $params = func_get_args();

        $forPage = '';

        switch (func_num_args()) {
            case 1 :
                $forPage = ' limit ' . ($params[0] - 1) * $this->pageNum . ',' . $this->pageNum;
                break;
            case 2 :
                $forPage = ' limit ' . ($params[0] - 1) * $params[1] . ',' . $params[1];
        }

        $this->forPage = $forPage ? : $this->forPage;

        return $this;
    }

    /**
     * order by 条件组装
     *
     * @return $this
     */
    public function orderBy()
    {
        $params = func_get_args();

        $orderBy = $this->orderBy ? $this->orderBy . ' ,' : ' order by ';

        switch (func_num_args()) {
            case 0 :
                $orderBy = rtrim($orderBy, ' ,');
                break;
            case 1 :
                $orderBy .= $params[0] . ' asc ';
                break;
            case 2 :
                $orderBy .= $params[0] . ' ' . $params[1];
        }

        $this->orderBy = $orderBy;

        return $this;
    }

    /**
     * group by 条件组装
     *
     * @return $this
     */
    public function groupBy()
    {
        $params = func_get_args();

        switch (func_num_args()) {
            case 0 :
                $groupBy = ' group by id ';
                break;
            case 1 :
                $groupBy = ' group by ' . $params[0];
                break;
            default :
                $groupBy = ' group by ' . $params[0] . ',' . $params[1];
        }

        $this->groupBy = $groupBy;

        return $this;
    }

    /**
     * between and 条件组装
     *
     * @return $this
     */
    public function betweenAnd()
    {
        $params = func_get_args();

        $betweenAnd = $this->where ? $this->where . ' and ' : ' where ';

        switch (func_num_args()) {
            case 3 :
                $betweenAnd .= $params[0] . ' between ' . $params[1] . ' and ' . $params[2];
                break;
            case 2 :
                $betweenAnd .= ' id between ' . $params[0] . ' and ' . $params[1];
                break;
            default :
                $betweenAnd = rtrim($betweenAnd, ' and');
                $betweenAnd = ltrim($betweenAnd, ' where');
        }

        $this->where = $betweenAnd;

        return $this;
    }

    /**
     * not between and 条件组装
     *
     * @return $this
     */
    public function notBetweenAnd()
    {
        $params = func_get_args();

        $notBetweenAnd = $this->where ? $this->where . ' and ' : ' where ';

        switch (func_num_args()) {
            case 3 :
                $notBetweenAnd .= $params[0] . ' not between ' . $params[1] . ' and ' . $params[2];
                break;
            case 2 :
                $notBetweenAnd .= ' id not between ' . $params[0] . ' and ' . $params[1];
                break;
            default :
                $notBetweenAnd = rtrim($notBetweenAnd, ' and');
                $notBetweenAnd = ltrim($notBetweenAnd, ' where');
        }

        $this->where = $notBetweenAnd;

        return $this;
    }
}