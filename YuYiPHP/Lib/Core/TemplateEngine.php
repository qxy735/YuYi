<?php
/**
 * Created by PhpStorm.
 * User: qixieyu
 * Date: 15-7-4
 * Time: 下午2:03
 */
class TemplateEngine
{
    private static $smarty = null;

    public function __construct()
    {
        if(is_null(self::$smarty)){
            // 实例化模版引擎对象
            $smarty = new Smarty;

            // 设置模版、缓存、编译目录
            $smarty->template_dir = APP_VIEWS_PATH . '/' . CONTROLLER ;
            $smarty->compile_dir = APP_STORAGE_COMPILE_PATH;
            $smarty->cache_dir = APP_STORAGE_CACHE_PATH;

            // 设置模版使用分隔符号
            $smarty->left_delimiter = C('LEFT_DELIMITER');
            $smarty->right_delimiter = C('RIGHT_DELIMITER');

            // 设置模版缓存信息
            $smarty->caching = C('CACHE_START');
            $smarty->cache_lifetime = C('CACHE_LIFE_TIME');

            // 将模版引擎对象保存在类属性中
            self::$smarty = $smarty;
        }
    }

    /**
     * 显示模版
     *
     * @param $tpl
     */
    protected function display($tpl)
    {
        self::$smarty->display($tpl, $_SERVER['REQUEST_URI']);
    }

    /**
     * 传递模版变量
     *
     * @param $var
     * @param $value
     */
    protected function assign($var, $value = null)
    {
        self::$smarty->assign($var, $value);
    }

    /**
     * 模版缓存
     *
     * @param null $tpl
     * @return false|string
     */
    protected function isCached($tpl = null)
    {
        C('CACHE_START') or Halt('请先开启模版缓存');

        $path = $this->setTemplatePath($tpl);

        return self::$smarty->is_cached($path, $_SERVER['REQUEST_URI']);
    }
}
?>