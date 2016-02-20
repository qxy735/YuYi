<?php

/**
 * Created by PhpStorm.
 * User: qixieyu
 * Date: 15-6-25
 * Time: 下午7:57
 */
final class YuYi
{
    public static function run()
    {
        // 定义项目所需目录路径常量
        self::defineConst();

        // 判断是否开启了调试模式,开启则做重复检测创建和加载文件工作,否则直接加载生成的 compile 合成文件
        if (APP_DEBUG) {
            // 加载框架所需文件
            self::importFile();

            // 自动创建默认应用项目目录
            self::createAppDirectory();

            // 生成默认 Tpl 模版文件
            self::generateTplFile();
        } else {
            // 加载合成的 compile 运行文件
            include ROOT_PATH . '/~compile.php';
        }

        // 定义项目配置读取环境
        self::defineEnvironment();

        // 应用初始化
        Application::run();
    }

    /**
     * 定义项目所需目录路径常量
     */
    private static function defineConst()
    {
        // 定义禹译框架版本
        define('YUYI_VERSION', 'v.1.0');

        // 定义错误调试模式，默认为调试模式下
        defined('APP_DEBUG') or define('APP_DEBUG', true);

        // 定义应用项目名称,默认为 Home 应用项目
        defined('APP_NAME') or define('APP_NAME', 'Home');

        // 定义禹译框架根目录路径
        define('YUYI_PATH', strtr(__DIR__, '\\', '/'));

        // 定义禹译框架配置目录路径
        define('CONF_PATH', YUYI_PATH . '/Conf');

        // 定义禹译框架数据信息存放目录路径及相关子目录路径
        define('DATA_PATH', YUYI_PATH . '/Data');
        define('DATA_TPL_PATH', DATA_PATH . '/Tpl');

        // 定义禹译框架核心文件存放目录路径及相关文件目录路径
        define('LIB_PATH', YUYI_PATH . '/Lib');
        define('LIB_CORE_PATH', LIB_PATH . '/Core');
        define('LIB_FUNC_PATH', LIB_PATH . '/Func');

        // 定义整体项目根目录路径
        define('ROOT_PATH', dirname(YUYI_PATH));

        // 定义项目目录路径及各子目录路径
        define('APP_PATH', ROOT_PATH . '/' . APP_NAME);
        define('APP_CONF_PATH', APP_PATH . '/Conf');
        define('APP_CONTROLLERS_PATH', APP_PATH . '/Controllers');
        define('APP_MODELS_PATH', APP_PATH . '/Models');
        define('APP_TPL_PATH', APP_PATH . '/Tpl');
        define('APP_TPL_PUBLIC_PATH', APP_TPL_PATH . '/Public');
        define('APP_VIEWS_PATH', APP_PATH . '/Views');
        define('APP_LANG_PATH', APP_PATH . '/Lang');

        // 定义日志、上传文件、下载目录、缓存、编译路径
        define('APP_STORAGE_PATH', APP_PATH . '/Storage');
        define('APP_STORAGE_LOG_PATH', APP_STORAGE_PATH . '/Log');
        define('APP_STORAGE_UPLOAD_PATH', APP_STORAGE_PATH . '/Upload');
        define('APP_STORAGE_DOWNLOAD_PATH', APP_STORAGE_PATH . '/Download');
        define('APP_STORAGE_CACHE_PATH', APP_STORAGE_PATH . '/Cache');
        define('APP_STORAGE_COMPILE_PATH', APP_STORAGE_PATH . '/Compile');

        // 定义数据提交的请求方式 GET、POST
        define('IS_GET', ('GET' == $_SERVER['REQUEST_METHOD'] ? true : false));
        define('IS_POST', ('POST' == $_SERVER['REQUEST_METHOD'] ? true : false));

        // 定义数据提交的请求方式 AJAX
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 'xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            define('IS_AJAX', true);
        } else {
            define('IS_AJAX', false);
        }

        // 定义请求是否合法
        define('IS_VALID', (isset($_SERVER["HTTP_REFERER"]) && $_SERVER['HTTP_REFERER'] ? true : false));

        // 定义项目前后台公用目录路径
        define('COMMON_PATH', ROOT_PATH . '/Common');
        define('COMMON_MODEL_PATH', COMMON_PATH . '/Models');
        define('COMMON_LIB_PATH', COMMON_PATH . '/Lib');
        define('COMMON_CONF_PATH', COMMON_PATH . '/Conf');

        // 定义禹译框架扩展工具目录
        define('EXTEND_PATH', YUYI_PATH . '/Extend');
        define('EXTEND_ORG_PATH', EXTEND_PATH . '/Org');
        define('EXTEND_TOOL_PATH', EXTEND_PATH . '/Tool');

        // 定义是否创建 Common 公共目录
        defined('IS_BUILD_COMMON') or define('IS_BUILD_COMMON', false);
    }

    /**
     * 加载框架所需文件
     */
    private static function importFile()
    {
        // 定义框架需要加载的文件路径
        $filePaths = array(
            LIB_CORE_PATH . '/TemplateEngine.php',
            EXTEND_ORG_PATH . '/Smarty/Smarty.class.php',
            LIB_CORE_PATH . '/Application.php',
            LIB_CORE_PATH . '/BaseController.php',
            LIB_CORE_PATH . '/Log.php',
            LIB_FUNC_PATH . '/Function.php',
        );

        $content = "<?php \r\n";

        // 根据定义的框架所需加载文件路径来加载对应文件
        foreach ($filePaths as $filePath) {
            // 如果加载的文件不存在, 则不做加载操作, 否则自动加载对应文件
            file_exists($filePath) and include($filePath);

            $content .= trim(substr(file_get_contents($filePath), 5, -2));
        }

        file_put_contents(ROOT_PATH . '/~compile.php', $content) or exit('File Is Not Allowed To Access');
    }

    /**
     * 自动创建默认应用目录
     */
    private static function createAppDirectory()
    {
        // 需要创建的应用目录路径
        $appDirPaths = array(
            APP_PATH,
            APP_CONF_PATH,
            APP_CONTROLLERS_PATH,
            APP_MODELS_PATH,
            APP_TPL_PATH,
            APP_TPL_PUBLIC_PATH,
            APP_VIEWS_PATH,
            APP_LANG_PATH,
            APP_STORAGE_PATH,
            APP_STORAGE_LOG_PATH,
            APP_STORAGE_UPLOAD_PATH,
            APP_STORAGE_DOWNLOAD_PATH,
            APP_STORAGE_CACHE_PATH,
            APP_STORAGE_COMPILE_PATH,
        );

        // 是否创建公共应用目录
        if (IS_BUILD_COMMON) {
            $appDirPaths = array_merge($appDirPaths, array(
                COMMON_PATH,
                COMMON_LIB_PATH,
                COMMON_CONF_PATH,
                COMMON_MODEL_PATH
            ));
        }

        // 根据需要创建的应用目录路径配置来循环创建
        foreach ($appDirPaths as $dirPath) {
            // 如果该应用目录已存在, 则不再重复创建, 否则自动创建拥有最高权限的目录
            is_dir($dirPath) or mkdir($dirPath, 0777, true);
        }
    }

    /**
     * 自动生成默认 Tpl 模版文件
     */
    private static function generateTplFile()
    {
        // 默认需要生成的模版文件名
        $srcTplFiles = array(
            'Error.html',
            'Success.html',
            'Halt.html',
            'Notice.html'
        );

        // 循环生成各模版文件
        foreach ($srcTplFiles as $tplFile) {
            // 应用项目中的模版文件如果不存在则将禹译框架的默认模版文件拷贝到应用项目中
            file_exists(APP_TPL_PATH . "/{$tplFile}") or copy(DATA_TPL_PATH . "/{$tplFile}", APP_TPL_PATH . "/{$tplFile}");
        }
    }

    /**
     * 定义项目配置读取环境
     */
    private static function defineEnvironment()
    {
        // 定义线上环境、预发布环境、压力测试环境、测试环境、本地环境 Code 码
        $envs = array(
            'production',
            'pre-release',
            'presure-test',
            'test',
            'local'
        );

        // 获取需要读取的环境信息
        $env = getenv('YUYI_ENV');

        // 设置环境信息值,默认为空
        $setEnvValue = in_array($env, $envs) ? $env : '';

        // 定义应用项目读取配置环境变量
        define('APP_ENV', $setEnvValue);
    }
}

YuYi::run();