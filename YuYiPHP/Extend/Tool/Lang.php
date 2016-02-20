<?php

/**
 * Created by PhpStorm.
 * User: qixieyu
 * Date: 15-7-3
 * Time: 下午8:41
 */
final class Lang
{
    /**
     * 获取 Lang 目录中的配置文件信息
     *
     * @param null $name
     *
     * @return mixed|null
     */
    public static function get($name = null)
    {
        if (is_null($name)) {
            return null;
        }

        $params = explode('.', $name);

        $langFileName = $params[0] . '.php';

        $langs = self::getFileData($langFileName);

        unset($params[0]);

        foreach ($params as $param) {
            if (isset($langs[$param])) {
                $langs = $langs[$param];
            } else {
                return $name;
            }
        }

        return $langs;
    }

    /**
     * 获取配置文件中的数据信息
     *
     * @param $fileName
     *
     * @return mixed
     */
    private static function getFileData($fileName)
    {
        $langFilePath = APP_LANG_PATH . '/' . $fileName;

        file_exists($langFilePath) or Halt('Lang 目录中无 ' . $fileName . ' 文件');

        return include($langFilePath);
    }
}