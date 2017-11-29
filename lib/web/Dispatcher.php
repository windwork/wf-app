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
 * <b>调度器类</b>
 * 
 * 前端控制器模式的实现，使用一个调度器对象来调度请求到相应的具体处理程序（action）。
 * 
 * @package     wf.app.web
 * @author      cm <cmpan@qq.com>
 * @link        http://docs.windwork.org/manual/wf.app.web.dispatcher.html
 * @since       1.0.0
 */
class Dispatcher {
    /**
     * 
     * @var \wf\app\web\Request
     */
    private $request;
    
    /**
     * 
     * @var \wf\app\web\Response
     */
    private $response;
    
    /**
     * 
     * @var \wf\route\RouteAbstract
     */
    private $router;
    
    /**
     * 
     * @var \wf\app\web\Application
     */
    private $app;
    
    /**
     * 
     * @var \wf\app\web\Controller
     */
    private $controller;
    
    /**
     * 是否已完成调度
     * @var bool
     */
    private $isDispatched = false;
    
    /**
     * @return \wf\app\web\Request
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * @return \wf\app\web\Response
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * @return \wf\app\web\Controller
     */
    public function getController() {
        return $this->controller;
    }
    
    /**
     * 
     * @return \wf\route\RouteAbstract
     */
    public function getRouter() {
        return $this->router;
    }
    
    /**
     * 
     * @return \wf\app\web\Application
     */
    public function getApp() {
        return $this->app;
    }
    
    /**
     * 
     * @param \wf\app\web\Application $app
     * @return \wf\app\web\Dispatcher
     */
    public function setApp(\wf\app\web\Application $app) {
        $this->app = $app;
        
        return $this;
    }

    /**
     * @param \wf\app\web\Controller $controller
     * @return \wf\app\web\Dispatcher
     */
    public function setController(\wf\app\web\Controller $controller) {
        $this->controller = $controller;        
        return $this;
    }

    /**
     * 
     * @param \wf\route\RouteAbstract $router
     * @return \wf\app\web\Dispatcher
     */
    public function setRouter(\wf\route\RouteAbstract $router) {
        $this->router = $router;
        return $this;
    }
    
    /**
     * 
     * @param string $uriArg = ''
     * @throws Exception
     * @throws NotFoundException
     */
    public function dispatch($uriArg = '') {        
        // 初始化router（router保存URL请求参数，不影响$_GET,$_POST的使用）
        if ($uriArg) {
            $uri = $uriArg;

            if($uri[0] != '/') {
                // http://xxx
                if(preg_match("/(^http[s]?:\\/\\/.*?)(\\/.*)/i", $uri, $match)) {
                    $uri = $match[2];
                } else {
                    $uri = cfg('url.basePath') . cfg('url.baseUrl') . $uri;
                }
            }

            $this->request->setRequestUri($uri);
        } else {
            $uri = $this->request->getRequestUri();
        }
        
        // 执行调度次数限制
        $this->dispatchLimit($uri);
        
        // 当前请求路由参数
        $this->router->parse($uri);
        
        // 将router请求参数复制到request对象，在控制器中可通过$this->request->getRequest('var_name')调用router请求变量
        if ($this->router->attributes) {
            $this->request->setAttributes($this->router->attributes);
        }
        
        // 在$uriArg中传递GET变量，需重置$_GET变量
        if($uriArg && $this->router->query) {
            $_GET = [];
            parse_str($this->router->query, $_GET);
        }
                
        // 初始化控制器实例前触发的钩子
        $this->app->getHook()->call('dspNewControllerFore');
        \wf\app\Benchmark::mark('dspControllerFore');
        
        // 控制器实现类
        $ctlCalss = $this->router->ctlClass;
        if (!class_exists($ctlCalss)) {
            throw new \wf\app\web\NotFoundException;
        }
        
        $this->controller = new $ctlCalss($this);
        
        // 控制器继承约束
        if(!$this->controller instanceof \wf\app\web\Controller) {
            throw new \wf\app\web\NotFoundException;
        }
        
        if($this->response->isSentHeader()) {
            logging('error', "'{$uri}' 发生多次响应！");
            return;
        }
        
        // 创建控制器实例后，执行action前触发的钩子
        $this->app->getHook()->call('dspRunActionFore');        
        \wf\app\Benchmark::mark('dspActionFore');
        
        // 执行 action
        $this->controller->invokeAction($this->router->actParams);
        
        // 响应内容未发送则发送
        if(!$this->response->isSentHeader()) {
            // 内容输出前触发的钩子，可对输出内容进行处理过滤
            $this->app->getHook()->call('dspOutputFore');
        
            $this->response->setBody(ob_get_clean());
            $this->response->send();
        }
        
        $this->app->getHook()->call('dspResponseAft');        
        \wf\app\Benchmark::mark('dspResponseAft');
        
        $this->isDispatched = true;
    }
    
    /**
     * 
     * @param string $uri
     * @throws Exception
     */
    private function dispatchLimit($uri) {        
        // 防止dispatch死循环
        static $dispatchChain = [];
        $dispatchChain[] = $uri;

        if ($this->isDispatched) {
            // 程序本计划已执行完成，但还在重复dispatch
            logging('error', 'multiple dispatch on begin dispatch:');
            logging('error', $dispatchChain);
            return false;
        }
        
        $dispatchCount = count($dispatchChain);
        
        if($dispatchCount == 5) {
            logging('error', "连续 dispatch {$dispatchCount} 次了！");
            logging('error', $dispatchChain);
            throw new Exception("发生多次转发！");
        }
        
        return $dispatchCount;
    }
    
    /**
     * 
     * @param \wf\app\web\Application $app
     */
    public function __construct(\wf\app\web\Application $app) {
        $this->setApp($app);
        
        // http请求初始化
        $this->request  = new \wf\app\web\Request();
        
        // http响应实例
        $this->response = new \wf\app\web\Response();
        
        $config = $app->getConfig();
        
        // 加载URL设置
        $urlCfg = $config->get('url');
        
        // 网站请求url查询串之前，站点路径之后的部分，web入口文件只允许是index.php
        $urlCfg['baseUrl'] = $urlCfg['rewrite'] ? '' : 'index.php?';


        if (PHP_SAPI == 'cli') {
            // 命令行下需要有siteUrl参数
            if (!preg_match("/^(http[s]?:\\/\\/.+?)(\\/.*)/i", $urlCfg['siteUrl'], $siteUrlMatch)) {
                throw new \wf\app\web\Exception('请在config/url.php中设置siteUrl选项');
            }
        
            // 从站点siteUrl配置获取hostInfo、basePath
            $urlCfg['hostInfo'] = $siteUrlMatch[1];  // 主机信息，包含协议信息+主机名+访问端口信息（http://www.windwork.org:8080）
            $urlCfg['basePath'] = $siteUrlMatch[2];  // 站点根目录文件夹，如： /demo/
        } else {
            $urlCfg['hostInfo'] = $this->request->getHostInfo(); // 主机信息，包含协议信息+主机名+访问端口信息（http://www.windwork.org:8080）
            $urlCfg['basePath'] = $this->request->getBasePath(); // 站点根目录文件夹，如： /demo/
            
            // 不设置siteUrl才设置
            if (empty($urlCfg['siteUrl'])) {
                $urlCfg['siteUrl'] = $urlCfg['hostInfo'] . $urlCfg['basePath'];
            }
        }
        
        // 静态文件路径
        if (empty($urlCfg['staticSiteUrl'])) {
            $urlCfg['staticSiteUrl'] = $urlCfg['siteUrl'];
        }
        
        if (empty($urlCfg['topDomain'])) {
            $urlCfg['topDomain'] = preg_replace("/^http[s]?:\\/\\/(.+?)/i", "\\1", $urlCfg['hostInfo']);
        }
        
        // URL 保存URL配置到全局配置中
        $config->set('url', $urlCfg);
        
        // 确定baseUrl后设置到 响应对象
        $this->response->baseUrl = $urlCfg['basePath'] . $urlCfg['baseUrl'];
        
        // （每次dispatch都需要）初始化路由设置，生成URL依赖于配置中的hostInfo、basePath
        $routeClass = (empty($urlCfg['class']) ? '\\wf\\route\\adapter\\Simple': $urlCfg['class']);
        $this->setRouter(new $routeClass($urlCfg));
        
        unset($urlCfg);
    }
}
