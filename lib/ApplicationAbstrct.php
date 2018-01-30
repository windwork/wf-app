<?php
/**
 * Windwork
 *
 * 一个用于快速开发高并发Web应用的轻量级PHP框架
 *
 * @copyright Copyright (c) 2008-2017 Windwork Team. (http://www.windwork.org)
 * @license   http://opensource.org/licenses/MIT
 */
namespace wf\app;

/**
 * 应用容器基类
 *
 * @package     wf.app
 * @author      cm <cmpan@qq.com>
 * @link        http://docs.windwork.org/manual/wf.app.app.html
 * @since       0.1.0
 */
abstract class ApplicationAbstrct
{

    /**
     * app实例
     * @var \wf\app\Application
     */
    private static $instance;

    /**
     * 应用配置信息
     * @var \wf\app\Config
     */
    protected $config;

    /**
     * 应用钩子实例
     * @var \wf\app\Hook
     */
    protected $hook;

    /**
     * 服务定位器实例
     * @var \wf\app\ServiceLocator
     */
    private $srv;

    /**
     *
     * @var \wf\app\Message
     */
    private $message;


    /**
     * 取得前端控制器实例，只允许实例化一次
     *
     * @return \wf\app\Application
     */
    public static function app()
    {
        if (!self::$instance){
            \wf\app\Benchmark::start();
            self::$instance = new static();
            self::$instance->initRuntime();
            \wf\app\Benchmark::mark('appInstanceAft');
        }

        return self::$instance;
    }

    /**
     * 限制只能使用单例模式创建实例，通过 Application::app()创建/获取实例
     */
    private function __construct()
    {
        // 自定义异常处理
        set_exception_handler('exceptionHandler');

        // 配置文件在 ./config文件夹，不支持自定义配置文件夹
        $this->config = new \wf\app\Config(ROOT_DIR . '/config', WF_ENV);
        $this->config->load('app');
    }

    /**
     * 运行时 ini设置
     */
    protected function initRuntime()
    {
        // 防止出现循环初始化
        static $inited;
        if ($inited) {
            return;
        }
        $inited = true;

        @ini_set('zend.script_encoding', 'UTF-8');  // php脚本使用UTF-8字符集
        @ini_set('default_charset',      'UTF-8');  // 输出默认使用UTF-8字符集

        // 加载config后，初始化运行时 ini设置
        @ini_set('memory_limit',   '128M'); // 如需更大内存，可在控制器中覆盖设置
        @ini_set('date.timezone',  $this->config->get('timezone')); // 默认时区, 可在用户登录后重设时区
        @ini_set('error_log',      $this->config->get('log.dir') . '/php_error.log');
        @ini_set('log_errors',     1);

        if (defined('WF_DEBUG') && WF_DEBUG) {
            // 调试模式设置
            @ini_set('error_reporting', E_ALL|E_STRICT);
            @ini_set('display_errors',  1);
        } else {
            // 非调试模式
            @ini_set('error_reporting', E_ALL ^ (E_NOTICE | E_WARNING | E_STRICT));
            @ini_set('display_errors',  0);
        }

        // 设置自动加载类查找类文件的文件夹
        \wf\app\Loader::addClassPath($this->config->get('classPath'));

        // 清除输出缓冲
        while (ob_get_level()) {
            @ob_end_clean();
        }

        // 启用压缩，服务器端支持压缩并且客户端支持解压缩则启用压缩
        @ob_start($this->config->get('gzcompress') ? 'ob_gzhandler' : null);

        // 响应头信息设置
        header('Content-Type: text/html;charset=UTF-8');
        header('Cache-Control: private, max-age=0, no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('X-Powered-By: Windwork');

        // 初始化hook，加载hook配置
        $this->hook = new \wf\app\Hook($this->config->get('hooks'));
        // hook 1 Web运行环境初始化后的钩子
        $this->hook->call('appRuntimeAft');
    }

    /**
     * 获取服务定位器实例。
     *
     * 必须加载config/app.php配置文件以后才能调用该方法，否则将无法根据配置加载框架组件。
     *
     * @return \wf\app\ServiceLocator
     */
    public function getSrv()
    {
        if ($this->srv) {
            return $this->srv;
        }

        $this->srv = new \wf\app\ServiceLocator();

        // 框架组件从配置文件注入服务定位器
        $srvCfgs = cfg('srv');
        if (!$srvCfgs) {
            return $this->srv;
        }

        foreach ($srvCfgs as $srvKey => $srvArgs) {
            if (isset($srvArgs['class'])) {
                $isShare = !isset($srvArgs['srvShare']) || $srvArgs['srvShare'] ? true : false;
                $this->srv->set($srvKey, $srvArgs['class'], [$srvArgs], $isShare);
            } else {
                foreach ($srvArgs as $subKey => $subSrvArgs) {
                    if (empty($subSrvArgs['class'])) {
                        continue;
                    }
                    $isShare = !isset($subSrvArgs['srvShare']) || $subSrvArgs['srvShare'] ? true : false;
                    $this->srv->set("{$srvKey}.{$subKey}", $subSrvArgs['class'], [$subSrvArgs], $isShare);
                }
            }
        }

        return $this->srv;
    }

    /**
     * 获取app创建的钩子实例
     * @return \wf\app\Hook
     */
    public function getHook()
    {
        return $this->hook;
    }

    /**
     * 消息对象实例，支持在多个控制器间（dsp()->dispatch()时）共享消息
     * @return \wf\app\Message
     */
    public function getMessage()
    {
        if (!$this->message) {
            $this->message = new \wf\app\Message();
        }

        return $this->message;
    }

    /**
     * 应用创建的配置信息类实例
     * @return \wf\app\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * 删除创建的应用实例
     */
    public static function destroy()
    {
        static::$instance = null;
    }

    /**
     * 获取国际化多语言支持实例
     * @return \wf\app\I18n
     */
    public function getI18n()
    {
        static $i18n;
        if ($i18n) {
            return $i18n;
        }

        $i18n = new \wf\app\I18n();

        // 语言文件在 ./config文件夹，不支持自定义语言文件夹
        $i18n->setDir(ROOT_DIR . '/i18n');

        if (!empty($_SESSION['locale']) && $i18n->setLocale($_SESSION['locale'])) {
            // 优先级1、从session获取地区
            return $i18n;
        } elseif (!empty($_COOKIE['locale']) && $i18n->setLocale($_COOKIE['locale'])) {
            // 优先级2、从cookie获取地区
            return $i18n;
        } elseif (class_exists('\\Locale') && $i18n->setLocale(\Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']))) {
            // 优先级3、从客户端浏览器识别地区
            return $i18n;
        } elseif ($i18n->setLocale($this->getConfig()->get('locale'))) {
            // 优先级4、从配置文件获取地区
            return $i18n;
        } else {
            $i18n->setLocale('zh_CN');
            return $i18n;
        }
    }

    /**
     * 应用执行入口
     */
    abstract public function run();

}
