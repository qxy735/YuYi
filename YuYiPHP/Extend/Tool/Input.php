<?php

/**
 * Created by PhpStorm.
 * User: qixieyu
 * Date: 15-7-3
 * Time: 下午8:41
 */
final class Input
{
    /**
     * 获取地址栏或表单提交的指定数据信息
     *
     * @param      $name
     * @param null $default
     *
     * @return null
     */
    public static function get($name, $default = null)
    {
        $inputs = self::all();

        if (isset($inputs[$name])) {
            return $inputs[$name];
        }

        return $default;
    }

    /**
     * 判断指定数据信息是否存在
     *
     * @param $name
     *
     * @return bool
     */
    public static function has($name)
    {
        $inputs = self::all();

        return isset($inputs[$name]);
    }

    /**
     * 获取所有地址栏或表单提交的数据信息
     *
     * @return array
     */
    public static function all()
    {
        return array_merge($_GET, $_POST);
    }
}