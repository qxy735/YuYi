<?php
/**
 * Created by PhpStorm.
 * User: qixieyu
 * Date: 15-6-25
 * Time: 下午10:23
 */

/**
 * 打印数据方法, print_r 打印
 */
if (!function_exists('p')) {
    function P($datas, $format = false)
    {
        if ($format) {
            echo '<pre>';
        }

        print_r($datas);

        if ($format) {
            echo '</pre>';
        }
    }
}

/**
 * 打印数据方法, var_dump 打印
 */
if (!function_exists('dump')) {
    function dump($datas, $format = false)
    {
        if ($format) {
            echo '<pre>';
        }

        var_dump($datas);

        if ($format) {
            echo '</pre>';
        }
    }
}

/**
 * 获取和动态设置配置项信息方法
 */
if (!function_exists('c')) {
    function C($config = null, $configValue = null, $flag = false)
    {
        static $configs = array();

        if (is_array($config)) {
            if ($flag) {
                $config = array_change_key_case($config, CASE_UPPER);
            }
            $configs = array_merge($configs, $config);

            return;
        }

        if (is_string($config)) {
            if (is_null($configValue)) {
                if ($flag) {
                    $config = strtoupper($config);
                }

                return isset($configs[$config]) ? $configs[$config] : null;
            }

            $configs[$config] = $configValue;

            return;
        }

        if (is_null($config) && is_null($configValue)) {
            return $configs;
        }
    }
}

/**
 * Url 地址跳转方法
 */
if (!function_exists('go')) {
    function Go($url, $time = 0, $notice = '')
    {
        if (C('OPEN_START_DOMAIN')) {
            $url = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $url;
        } else {
            $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . '/' . $url;
        }

        if (!headers_sent()) {
            0 == $time ? header('location:' . $url) : header("refresh:{$time};url={$url}");
        } else {
            echo "<meta http-equiv='refresh' content='{$time};url={$url}'>";
        }

        $time and exit($notice);
    }
}

/**
 * 错误信息显示方法
 */
if (!function_exists('halt')) {
    function Halt($log, $level = 'Error', $type = 3, $savePath = null)
    {
        if (is_array($log)) {
            Log::write($log['message'], $level, $type, $savePath);
        } else {
            Log::write($log, $level, $type, $savePath);
        }

        $error = array();

        if (APP_DEBUG) {
            if (is_array($log)) {
                $error = $log;
            } else {
                $trace = debug_backtrace();
                $error['message'] = $log;
                $error['file'] = $trace[0]['file'];
                $error['line'] = $trace[0]['line'];
                $error['function'] = isset($trace[0]['function']) ? $trace[0]['function'] : '';
                $error['class'] = isset($trace[0]['class']) ? $trace[0]['class'] : '';
                ob_start();
                debug_print_backtrace();
                $error['trace'] = htmlspecialchars(ob_get_clean());
            }
        } else {
            if ($url = C('WEB_ERROR_URL')) {
                Go($url);
            } else {
                $error['message'] = C('WEB_ERROR_MESSAGE');
            }
        }

        $haltPath = C('TPL_FILE_PATH') . '/' . C('HATL_TPL_FILE_NAME');

        file_exists($haltPath) and include($haltPath);

        exit;
    }
}

/**
 * 打印自定义的常量信息
 */
if (!function_exists('print_const')) {
    function print_const($flag = false)
    {
        $consts = get_defined_constants(true);

        P($consts['user'], $flag);
    }
}

/**
 * 获取数组元素内容
 */
if (!function_exists('array_get')) {
    function array_get($array, $key, $default = null)
    {
        if (is_null($key)) {
            return $array;
        }

        if (isset($array[$key])) {
            return $array[$key];
        }

        if (is_array($array)) {
            foreach (explode('.', $key) as $segment) {
                if (isset($array[$segment])) {
                    $array = $array[$segment];
                } else {
                    return $default;
                }
            }
        }

        return $array;
    }
}

/**
 * 获取对象或数组元素内容
 */
if (!function_exists('data_get')) {
    function data_get($object, $key, $default = null)
    {
        if (is_null($key)) {
            return $object;
        }

        if (isset($object[$key])) {
            return $object[$key];
        } elseif (isset($object->{$key})) {
            return $object->{$key};
        }

        $segments = explode('.', $key);

        if (is_array($object)) {
            foreach ($segments as $segment) {
                if (isset($object[$segment])) {
                    $object = $object[$segment];
                } else {
                    return $default;
                }
            }
        } elseif (is_object($object)) {
            foreach ($segments as $segment) {
                if (isset($object->{$segment})) {
                    $object = $object->{$segment};
                } else {
                    return $default;
                }
            }
        }

        return $object;
    }
}

/**
 * 将对象转化为数组
 */
if (!function_exists('toArray')) {
    function toArray($object)
    {
        return json_decode(json_encode($object), true);
    }
}

/**
 * 将多维数组中指定的相同元素组成一维数组
 */
if (!function_exists('array_pluck')) {
    function array_pluck($arrays, $key = null)
    {
        if (is_null($key)) {
            return $arrays;
        }

        if (is_array($arrays)) {
            $pluckArray = array();

            foreach ($arrays as $array) {
                if (isset($array[$key])) {
                    $pluckArray[] = $array[$key];
                }
            }

            return $pluckArray;
        }

        return $arrays;
    }
}
?>