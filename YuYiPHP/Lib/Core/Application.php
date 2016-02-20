<?php

/**
 * Created by PhpStorm.
 * User: qixieyu
 * Date: 15-6-25
 * Time: 下午10:24
 */
final class Application
{
    public static function run()
    {
        // 框架初始化设置
        self::initialize();

        // 自定义普通错误处理方法
        set_error_handler(array(__CLASS__, 'normalError'));

        // 自定义致命错误处理方法
        register_shutdown_function(array(__CLASS__, 'fatalError'));

        // 定义项目访问基础所需地址
        self::defineUrl();

        // 加载用户文件
        self::autoloadUserFile();

        // 自动加载类
        spl_autoload_register(array(__CLASS__, 'autoload'));

        // 创建默认控制器文件
        self::createDefaultController();

        // Url 重写
        self::urlRewrite();

        // 运行项目实例
        self::appRun();
    }

    /**
     * 框架初始化配置
     */
    private static function initialize()
    {
        // 定义禹译框架默认系统配置项文件路径
        $sysConfig = CONF_PATH . '/Config.php';

        // 系统配置项文件存在则加载
        file_exists($sysConfig) and C(
            include($sysConfig));

        // 公共配置项文件路径
        $commonConfig = COMMON_CONF_PATH . '/config.php';

        // 项目公共配置项文件存在则加载
        file_exists($commonConfig) and C(
            include($commonConfig));

        // 定义公共环境配置文件路径
        $commonEnvConfig = APP_ENV ? (COMMON_CONF_PATH . '/' . APP_ENV . '/config.php') : '';

        // 公共环境配置文件存在则加载
        if ($commonEnvConfig) {
            file_exists($commonEnvConfig) and C(
                include($commonEnvConfig));
        }

        // 定义应用项目默认配置文件路径
        $appConfig = APP_CONF_PATH . '/config.php';

        // 项目默认配置文件存在则加载
        file_exists($appConfig) and C(
            include($appConfig));

        // 定义应用项目环境配置文件路径
        $appEnvConfig = APP_ENV ? (APP_CONF_PATH . '/' . APP_ENV . '/config.php') : '';

        // 应用项目环境配置文件存在则加载
        if ($appEnvConfig) {
            file_exists($appEnvConfig) and C(
                include($appEnvConfig));
        }

        // 定义禹译框架默认数据库链接配置项文件路径
        $sysDatabase = CONF_PATH . '/Database.php';

        // 系统配置项文件存在则加载
        file_exists($sysDatabase) and C(
            include($sysDatabase));

        // 定义公共数据库链接配置文件路径
        $commonDatabase = COMMON_CONF_PATH . '/database.php';

        // 项目公共数据库链接配置文件存在则加载
        file_exists($commonDatabase) and C(
            include($commonDatabase));

        // 定义公共环境数据库链接配置文件路径
        $commonEnvDatabase = APP_ENV ? (COMMON_CONF_PATH . '/' . APP_ENV . '/database.php') : '';

        // 公共环境数据库链接配置文件存在则加载
        if ($commonEnvDatabase) {
            file_exists($commonEnvDatabase) and C(
                include($commonEnvDatabase));
        }

        // 定义应用项目默认数据库链接配置文件路径
        $appDatabase = APP_CONF_PATH . '/database.php';

        // 项目默认配置文件存在则加载
        file_exists($appDatabase) and C(
            include($appDatabase));

        // 定义应用项目环境数据库链接配置文件路径
        $appEnvDatabase = APP_ENV ? (APP_CONF_PATH . '/' . APP_ENV . '/database.php') : '';

        // 应用项目环境配置文件存在则加载
        if ($appEnvDatabase) {
            file_exists($appEnvDatabase) and C(
                include($appEnvDatabase));
        }

        // 设置时区,默认为 PRC
        function_exists('date_default_timezone_set') and date_default_timezone_set(C('DEFAULT_TIME_ZONE'));

        // Session 开启设置
        if (C('SESSION_START_STATUS')) {
            function_exists('session_start') and session_start();
        }

        // 设置显示的编码
        if (C('HEADER_DEFAULT_CHARSET')) {
            function_exists('header') and header('Content-Type:text/html;charset=' . C('HEADER_DEFAULT_CHARSET'));
        }
    }

    /**
     * 处理普通错误方法
     *
     * @param $errno
     * @param $error
     * @param $file
     * @param $line
     */
    public static function normalError($errno, $error, $file, $line)
    {
        $noticePath = C('TPL_FILE_PATH') . '/' . C('NOTICE_TPL_FILE_NAME');

        switch ($errno) {
            case E_PARSE :
            case E_ERROR :
            case E_USER_ERROR :
            case E_CORE_ERROR :
            case E_COMPILE_ERROR :
                $message = $error . $file . "第{$line}行";
                Halt($message);
                break;
            case E_STRICT :
            case E_USER_WARNING :
            case E_USER_NOTICE :
            default :
                if (APP_DEBUG) {
                    file_exists($noticePath) and
                    include($noticePath);
                }
                break;
        }
    }

    /**
     * 自定义致命错误处理方法
     */
    public static function fatalError()
    {
        if ($error = error_get_last()) {
            self::normalError($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }

    /**
     * 定义项目访问地址
     */
    private static function defineUrl()
    {
        // 基础 Url 地址
        if (C('OPEN_START_DOMAIN')) {
            $baseUrl = "http://{$_SERVER['HTTP_HOST']}";
        } else {
            $baseUrl = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['SCRIPT_NAME']}";
        }

        // 定义项目访问首页 Url 地址
        define('__APP__', $baseUrl);
        define('__URL__', $baseUrl);

        if (C('OPEN_START_DOMAIN')) {
            // 定义项目访问根 Url 地址
            define('__ROOT__', __APP__);
        } else {
            // 定义项目访问根 Url 地址
            define('__ROOT__', dirname(__APP__));
        }

        // 定义项目上传目录
        define('__UPLOAD__', __ROOT__ . '/' . APP_NAME . '/Storage/Upload');

        // 定义项目访问模版、资源 Url 地址
        define('__TPL__', __ROOT__ . '/' . APP_NAME . '/Tpl');

        define('__PUBLIC__', __TPL__ . '/Public');
    }

    /**
     * 自动加载类
     *
     * @param $className
     */
    private static function autoload($className)
    {
        // 获取要访问的项目类文件扩展名
        $extension = C('CLASS_FILE_EXTENSION_NAME');

        switch ($className) {
            //  加载控制器类
            case 'Controller' == substr($className, -10) :
                $path = APP_CONTROLLERS_PATH . "/{$className}{$extension}";

                if (!file_exists($path)) {
                    $emptyFile = APP_CONTROLLERS_PATH . '/' . C('URL_ERROR_CONTROLLER') . C('CLASS_FILE_EXTENSION_NAME');

                    file_exists($emptyFile) or Halt($className . ' 控制器未找到');

                    include($emptyFile);

                    return;
                }

                include($path);

                break;
            // 加载模型类
            case 'Model' == substr($className, -5) && strlen($className) > 5 :
                $path = APP_MODELS_PATH . "/{$className}.php";

                if (!file_exists($path)) {
                    $path = COMMON_MODEL_PATH . "/{$className}.php";
                }

                file_exists($path) or Halt($className . ' Model未找到');

                include($path);

                break;
            // 默认加载框架扩展工具类
            default :
                $path = EXTEND_TOOL_PATH . "/{$className}.php";

                file_exists($path) or Halt($className . ' 类文件未找到');

                include($path);
        }
    }

    /**
     * 创建默认控制器文件
     */
    private static function createDefaultController()
    {
        // 定义默认控制器文件路径
        $defaultControllerPath = APP_CONTROLLERS_PATH . '/IndexController.php';

        // 定义默认控制器文件内容
        $defaultContent = <<<index
<?php
    class IndexController extends BaseController
    {
        public function index()
        {
            echo '<h3 align="center">(: Welcome Use YuYi Framework!</h3>
                  <h4 style="text - align: center; font - weight: normal;">欢迎使用禹译框架!</h4>';
        }
    }
index;

        // 默认控制器文件不存在则自动创建
        file_exists($defaultControllerPath) or file_put_contents($defaultControllerPath, $defaultContent);
    }

    /**
     * Url 重写
     */
    private static function urlRewrite()
    {
        if (false == C('URL_REWRITE')) {
            return;
        }

        if (isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO']) {
            $path = $_SERVER['PATH_INFO'];
        } else {
            $uri = strstr($_SERVER['REQUEST_URI'], C('URL_SUFFIX_NAME'));

            $path = $uri ? substr($uri, strlen(C('URL_SUFFIX_NAME'))) : $_SERVER['REQUEST_URI'];
        }

        $path = trim($path, '/');

        $path = trim($path, C('URL_SEPARATE'));

        $pathSuffix = strchr($path, '.');

        if (in_array(trim($pathSuffix, '.'), explode('|', C('URL_HTML_SUFFIX')))) {
            $path = strchr($path, $pathSuffix, true);
        }

        $path = $path ? explode(C('URL_SEPARATE'), $path) : array();

        switch (count($path)) {
            case 0 :
                $_GET[C('URL_CONTROLLER_NAME')] = $_GET[C('URL_ACTION_NAME')] = 'index';
                break;
            case 1 :
                $_GET[C('URL_CONTROLLER_NAME')] = $path[0];
                $_GET[C('URL_ACTION_NAME')] = 'index';
                break;
            case 2 :
                $_GET[C('URL_CONTROLLER_NAME')] = $path[0];
                $_GET[C('URL_ACTION_NAME')] = $path[1];
                break;
            default :
                $_GET[C('URL_CONTROLLER_NAME')] = array_shift($path);
                $_GET[C('URL_ACTION_NAME')] = array_shift($path);
                for ($i = 0, $l = count($path); $i < $l; $i++) {
                    $_GET[$path[$i]] = isset($path[$i + 1]) ? $path[$i + 1] : null;
                    $i++;
                }
        }
    }

    /**
     * 运行项目实例
     */
    private static function appRun()
    {
        // 获取要访问的控制器,默认为 IndexController 控制器
        $controller = isset($_GET[C('URL_CONTROLLER_NAME')]) ? $_GET[C('URL_CONTROLLER_NAME')] : 'Index';

        define('CONTROLLER', strtolower($controller));

        $controller = ucfirst($controller) . 'Controller';

        // 获取要访问的方法,默认为 index 方法
        $action = isset($_GET[C('URL_ACTION_NAME')]) ? $_GET[C('URL_ACTION_NAME')] : 'index';

        define('ACTION', $action);

        if (class_exists($controller)) {
            // 实例化控制器类
            $object = new $controller;

            // 执行对应的方法动作
            $object->$action();
        } else {
            $controller = C('URL_ERROR_CONTROLLER');
            $object = new $controller;

            $action = C('URL_ERROR__CONTROLLER_ACTION');
            $object->$action();
        }
    }

    /**
     * 加载用户文件
     */
    private static function autoloadUserFile()
    {
        $userFiles = C('AUTO_LOAD_USER_FILE');

        if (is_array($userFiles)) {
            foreach ($userFiles as $userFile) {
                file_exists(COMMON_LIB_PATH . "/{$userFile}") and include(COMMON_LIB_PATH . "/{$userFile}");
            }
        }
    }
}

?>