<?php
/**
 * Created by PhpStorm.
 * User: qixieyu
 * Date: 15-6-26
 * Time: 下午7:07
 */
return array(
    //----------------时区类配置-------------------------------
    'DEFAULT_TIME_ZONE' => 'PRC', // 定义默认时区为 PRC

    //----------------Session 类配置----------------------------
    'SESSION_START_STATUS' => true, // 定义默认 Session 开启状态,默认为开启状态

    //----------------错误提示类配置----------------------------
    'LOG_WRITE_START' => false, // 写入日志信息
    'WEB_ERROR_URL' => '', // 网站错误跳转地址
    'WEB_ERROR_MESSAGE' => '抱歉,服务器正在维护中...', // 网站错误提示信息

    //-----------------Url 地址类配置---------------------------
    'URL_CONTROLLER_NAME' => 'c', // 定义获取控制器名
    'URL_ACTION_NAME' => 'a', // 定义获取执行方法名
    'HEADER_DEFAULT_CHARSET' => 'utf-8', // 默认显示字符编码
    'URL_REWRITE' => false, // 是否重写 Url 地址
    'URL_SEPARATE' => '/', // Url 地址分隔符
    'URL_ERROR_CONTROLLER' => 'EmptyController', // 错误访问的处理类
    'URL_ERROR__CONTROLLER_ACTION' => 'index', // 控制器错误访问时的处理方法
    'URL_ERROR_METHOD' => '__empty', // 动作错误访问时的处理方法
    'URL_HTML_SUFFIX'=>'html|shtml', // 伪静态后缀
    'URL_SUFFIX_NAME' => '.php', // Url 地址后缀名
    'OPEN_START_DOMAIN' => false, // 是否开启域名模式

    //-----------------文件类配置--------------------------------
    'CLASS_FILE_EXTENSION_NAME' => '.php', // 定义默认类文件扩展名
    'TPL_FILE_PATH' => APP_TPL_PATH, // 页面提示信息模版文件路径
    'SUCCESS_TPL_FILE_NAME' => 'Success.html', // 成功提示跳转模版文件名
    'ERROR_TPL_FILE_NAME' => 'Error.html', // 错误提示跳转模版文件名
    'HATL_TPL_FILE_NAME' => 'Halt.html', // Halt 页面模版文件名
    'NOTICE_TPL_FILE_NAME' => 'Notice.html', // Notice 页面模版文件名
    'VIEW_FILE_EXTENSION_NAME' => '.html', // 默认试图文件扩展名
    'AUTO_LOAD_USER_FILE' => array(), // 需要自动加载的用户定义文件

    //-----------------模版引擎配置---------------------------------
    'TEMPLATE_ENGINE_START' => true, // 是否开启模版引擎
    'LEFT_DELIMITER' => '<{', // 模版左定界符
    'RIGHT_DELIMITER' => '}>', // 模版右定界符
    'CACHE_START' => false, // 是否缓存
    'CACHE_LIFE_TIME' => 60 // 缓存时间
);