<?php

/**
 * Created by PhpStorm.
 * User: qixieyu
 * Date: 15-6-26
 * Time: 下午11:39
 */
class BaseController extends TemplateEngine
{
    /**
     * 每页显示条数，默认为 20 条
     */
    const PAGE_NUM = 20;

    protected $vars = array();

    /**
     * 构造方法,自动执行
     */
    public function __construct()
    {
        // 调用模版处理类的自动初始化方法
        if (C('TEMPLATE_ENGINE_START')) {
            parent::__construct();
        }

        // 定义自定义构造方法 __init
        if (method_exists($this, '__init')) {
            $this->__init();
        }

        // 定义自定义构造方法 __auto
        if (method_exists($this, '__auto')) {
            $this->__auto();
        }
    }

	/**
	 * 权限验证
	 */
	protected function auth()
	{
		if(Session::has('_sign') && Session::get('_sign')){
			return;
		}
		
		return $this->redirect('auth/login');
	}
	/**
	 * 获取当前登录用户信息
	 */
	protected function user()
	{
		// 获取当前登录用户 Uid
		$uid = Session::get('uid');
		
		if(!$uid){
			return null;
		}
		
		// 获取当前登录用户信息
		$user = UserModel::find($uid);
		
		return $user ? $user[0] : null;
	}
	
    /**
     * 跳转方法
     */
    protected function redirect($url = '')
    {
        if (!$url) {
            return;
        }

        Go($url);
    }

    /**
     * 操作成功跳转方法
     *
     * @param string $message
     * @param string $url
     * @param int    $time
     */
    protected function success($message = '', $url = '', $time = 3)
    {
        include C('TPL_FILE_PATH') . '/' . C('SUCCESS_TPL_FILE_NAME');exit;
    }

    /**
     * 操作失败跳转方法
     *
     * @param string $message
     * @param string $url
     * @param int    $time
     */
    protected function error($message = '', $url = '', $time = 3)
    {
        include C('TPL_FILE_PATH') . '/' . C('ERROR_TPL_FILE_NAME');exit;
    }

    /**
     * 设置模版文件路径
     *
     * @param null $templateFile
     *
     * @return string
     */
    protected function setTemplatePath($templateFile = null)
    {
        if (is_null($templateFile)) {
            $path = APP_VIEWS_PATH . '/' . CONTROLLER . '/' . ACTION . C('VIEW_FILE_EXTENSION_NAME');
        } else {
            $isExt = strrchr($templateFile, '.');

            $templateFile = empty($isExt) ? ($templateFile . C('VIEW_FILE_EXTENSION_NAME')) : $templateFile;

            $path = APP_VIEWS_PATH . '/' . $templateFile;
        }

        return $path;
    }

    /**
     * 显示模版
     *
     * @param null $templateFile
     */
    protected function display($templateFile = null)
    {
        $path = $this->setTemplatePath($templateFile);

        file_exists($path) or Halt($path . '模版文件不存在');

        $this->assign('__PUBLIC__', __PUBLIC__ . '/');

        $this->assign('__UPLOAD__', __UPLOAD__ . '/');

        if (C('TEMPLATE_ENGINE_START')) {
            parent::display($path);
        } else {
            extract($this->vars);
            include $path;
        }
    }

    /**
     * 模版传递变量信息
     *
     * @param      $var
     * @param null $value
     */
    protected function assign($var, $value = null)
    {
        if (C('TEMPLATE_ENGINE_START')) {
            parent::assign($var, $value);
        } else {
            $this->vars[$var] = $value;
        }
    }

    /**
     * 设置模版变量信息
     *
     * @param      $var
     * @param null $value
     */
    public function __set($var, $value)
    {
        $this->vars[$var] = $value;
    }

    /**
     * 处理访问不存在的方法
     *
     * @param $method
     * @param $args
     */
    public function __call($method, $args)
    {
        $defaultMethod = C('URL_ERROR_METHOD');

        if (method_exists($this, $defaultMethod)) {
            $this->$defaultMethod();

            return;
        }

        Halt($method . ' 方法不存在');
    }
}

?>