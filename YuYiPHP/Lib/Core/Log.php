<?php
/**
 * Created by PhpStorm.
 * User: qixieyu
 * Date: 15-6-27
 * Time: 下午2:42
 */
final class Log
{
    /**
     * 写入日志信息方法
     *
     * @param string $log
     * @param string $level
     * @param int $type
     * @param null $savePath
     */
    public static function write($log = '', $level = 'Error', $type = 3, $savePath = null)
    {
        // 日志写入功能是否开启
        if(false == C('LOG_WRITE_START')){
            return;
        }

        // 设置默认日志信息保存文件及位置
        if(is_null($savePath)){
            $savePath = APP_STORAGE_LOG_PATH . '/log_' . date('Y-m-d') . '.log';
        }

        // 写入日志信息
        if(is_dir(APP_STORAGE_LOG_PATH)){
            error_log("[Date]:" .date('Y-m-d H:i:s') ." {$level}:{$log} \r\n", $type, $savePath);
        }
    }
}
?>