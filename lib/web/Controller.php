<?php
/**
 * Windwork
 * 
 * 一个用于快速开发高并发Web应用的轻量级PHP框架
 * 
 * @copyright Copyright (c) 2008-2017 Windwork Team. (http://www.windwork.org)
 * @license   http://opensource.org/licenses/MIT
 */
namespace wf\app\web;

/**
 * 控制器基础类 
 * 
 * @package     wf.app.web
 * @author      cm <cmpan@qq.com>
 * @link        http://docs.windwork.org/manual/wf.app.web.controller.html
 * @since       0.1.0
 */
abstract class Controller
{
    /**
     * 转发器对象实例
     * @var \wf\app\web\Dispatcher
     */
    protected $dispatcher   = null;
    
    /**
     * 
     * @var \wf\app\web\Application
     */
    protected $app;
    
    /**
     * 当前访问的模块
     * @var string
     */
    protected $mod   = null;
    
    /**
     * 当前访问的控制器
     * @var string
     */
    protected $ctl   = null;

    /**
     * 当前访问的功能
     * @var string
     */
    protected $act   = null;
    
    /**
     * 是否已初始化
     * @var bool
     */
    protected $inited = false;
    
    /**
     * @var \wf\app\web\Request
     */
    protected $request = null;

    /**
     * @var \wf\app\web\Response
     */
    protected $response = null;
    
    /**
     * 
     * @var \wf\template\EngineInterface
     */
    private $view;
    
    /**
     * 控制器构造函数
     * 设置app、request、response、mod、ctl、act、view属性
     */
    public function __construct()
    {        
        // 绑定应用实例
        $this->app = $dispatcher->getApp();
        
        $dispatcher = $this->app->getDispatcher();
        $router = $dispatcher->getRouter();
        
        // 模块/控制器/操作
        $this->mod = $router->mod;
        $this->ctl = $router->ctl;
        $this->act = $router->act;
        
        // 控制器中的对象
        $this->dispatcher = $dispatcher;
        $this->request    = $dispatcher->getRequest();
        $this->response   = $dispatcher->getResponse();
        
        $this->inited = true;
    }    
    
    /**
     * 执行控制器方法
     * 
     * @param array $params
     * @throws \wf\app\web\Exception
     */
    public function invokeAction(array $params)
    {
        if (!$this->inited) {
            throw new \wf\app\web\Exception('请在'.get_called_class().'::__construct()调用parent::__construct()');
        }
        
        $action = $this->act . 'Action';
        
        if(!method_exists($this, $action)) {
            throw new \wf\app\web\NotFoundException;
        }
        
        // 执行方法
        //call_user_func_array(array($this, $action), $params);
        $method = new \ReflectionMethod($this, $action);
        if (!$method->isPublic()){
            // note：ReflectionException为“Fatal error”，不可捕获，不在这里抛出，也不直接在上一级异常中捕获而不用这个异常抛出
            // 否则不能在上一级异常处理中捕获，而直接到顶级异常处理中
            throw new \wf\app\web\NotFoundException;
        }
        
        $method->invokeArgs($this, $params); // 在这里加断点，下一步将进入当前请求的控制器业务逻辑中
    }
    
    /**
     * 初始化视图对象实例
     */
    private function initView() {
        // 默认模板文件
        if (cfg('srv.template.defaultTpl')) {
            $defaultTpl = str_replace(['{mod}', '{ctl}', '{act}'], [$this->mod, $this->ctl, $this->act], cfg('srv.template.defaultTpl'));
        } else {
            $defaultTpl = trim("{$this->mod}/{$this->ctl}.{$this->act}.html", "/ ");
        }

        // 模板配置设置        
        $this->app->getConfig()
        // 模板编译ID，不同用户本地语言使用不同模板
        ->set('srv.template.compileId',  app()->getI18n()->getLocale())
        // 默认模板文件
        ->set('srv.template.defaultTpl', $defaultTpl);
        
        // 更新服务加载器模板组件参数
        setSrv('template', cfg('srv.template.class'), [cfg('srv.template')], false);
        
        // 创建模板引擎实例
        $this->view = srv('template');

        // 设置模板配置参数
        $this->view
        ->assign('mod', $this->mod)
        ->assign('ctl', $this->ctl)
        ->assign('act', $this->act)
        
        // URL相关参数
        ->assign('themePath',  cfg('url.staticSiteUrl') . 'theme/' . cfg('theme') . '/')
        ->assign('staticPath', cfg('url.staticSiteUrl') . 'static/')
        ->assign('basePath',   cfg('url.basePath'))
        ->assign('baseUrl',    cfg('url.baseUrl'))
        ->assign('siteUrl',    cfg('url.siteUrl'));
        
        return $this->view;        
    }
        
    /**
     * 初始化视图对象
     * @return \wf\template\EngineInterface
     */
    protected function getView()
    {
        if (!$this->view) {
            $this->initView();
        }
        
        return $this->view;
    }
    
    /**
     * 获取用户输入数据
     * 
     * @param string $name = null 为空则返回所有type对应的输入数据
     * @param string $type = '' 可选输入类型：post|request|get|attribute|cookie|空，为空则按post|request|get|attribute|cookie的顺序查找
     */
    protected function getInput($name = null, $type = '')
    {        
        switch (strtolower($type)) {
            case 'post':
                $ret = $this->request->getPost($name);
                break;
            case 'request':
                // use default
            case 'get':
                $ret = $this->request->getGet($name);
                break;
            case 'attribute':
                $ret = $this->request->getAttribute($name);
                break;
            case 'cookie':
                $ret = $this->request->getCookie($name);
                break;
            default:
                $ret = $this->request->getRequest($name);
                break;
        }
        
        return $ret;
    }
    
    /**
     * 业务控制层消息，只在控制器中设置值，可在视图中显示。
     * 可跨在一个请求中跨action传递
     * 
     * @return \wf\app\web\Message
     */
    protected function message()
    {
        return $this->app->getMessage();
    }
    
    /**
     * 显示应用消息对象的内容
     * @param string $htmlTpl = '' HTML视图的模板路径
     */
    protected function showMessage($htmlTpl = '')
    {
        // json视图
        if ($this->request->isAjaxRequest()) {
            print \wf\app\web\Output::jsonCallable($this->message()->toArray());
            return;
        }
        
        // html视图
        $view = $this->getView();
        $view->assign('message', $this->message());
        
        if ($htmlTpl) {
            $view->render($htmlTpl);
            exit;
        }
        
        $code = $this->message()->getCode();
        switch ($code) {
            case 401:
                $forward = $this->request->getRequestUrl();
                $uri = url('user.account.login/forward:' . paramEncode($forward));
                $this->dispatch($uri);
                exit;
            case 403:
                $tpl = 'common/403.html';
                break;
            case 404:
                $tpl = 'common/404.html';
                break;
            default:
                $tpl = 'common/message.html';
                break;
        }
        
        $view->render($tpl);
        
        // 显示内容后即结束执行
        exit;
    }
    
    /**
     * 应用程序内转发
     * @param string $uri
     */
    protected function dispatch($uri)
    {
        return $this->dispatcher->dispatch($uri);
    }
    
    /**
     * 网络跳转
     * @param string $uri
     */
    protected function redirect($uri)
    {
        $this->response->sendRedirect($uri);
    }
    
    /**
     * 是否是提交数据（因js跨域情况下前端js可能把post换成get，因此不用isPost）
     * @return boolean
     */
    protected function isSubmit()
    {
        return (bool)$this->getInput('submit');
    }
    
    /**
     * 当前请求是否是AJAX请求
     * @return boolean
     */
    protected function isAjaxRequest()
    {
        return $this->request->isAjaxRequest();
    }
}

