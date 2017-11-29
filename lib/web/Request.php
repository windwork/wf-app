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
 * 客户端向服务器发出请求类
 * 
 * 客户端请求相关信息，包括用户提交的信息以及客户端的一些信息。
 *
 * @package     wf.app.web
 * @author      cm <cmpan@qq.com>
 * @link        http://docs.windwork.org/manual/wf.app.web.request.html
 * @since       0.1.0
 */
class Request 
{
    
    const METHOD_OPTIONS  = 'OPTIONS';
    const METHOD_GET      = 'GET';
    const METHOD_HEAD     = 'HEAD';
    const METHOD_POST     = 'POST';
    const METHOD_PUT      = 'PUT';
    const METHOD_DELETE   = 'DELETE';
    const METHOD_TRACE    = 'TRACE';
    const METHOD_CONNECT  = 'CONNECT';
    const METHOD_PATCH    = 'PATCH';
    const METHOD_PROPFIND = 'PROPFIND';

    protected $method = null;
    
    /**
     * 是否允许自定义请求方法
     * @var bool
     */
    protected $allowCustomMethods = true;
    
    /**
     * 访问的端口号
     *
     * @var int
     */
    protected $port = null;
    
    /**
     * 请求路径信息
     *
     * @var string
     */
    protected $hostInfo = null;
    
    /**
     * 语言
     *
     * @var string
     */
    protected $language = null;
    
    /**
     * 请求脚本url
     * 
     * @var string
     */
    private $scriptUrl = null;
    
    /**
     * 请求参数uri
     * 
     * @var string
     */
    private $uri = null;
    
    /**
     * 基础路径信息
     * 
     * @var string
     */
    private $baseUrl = null;
    
    /**
     * 自定义变量，如请求URL的$key:$value键值对参数值
     * @var array
     */
    private $attributes = [];
    
    /**
     * 
     * @param string $name
     * @param mixed $value
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
        return $this;
    }
    
    public function setAttributes($attributes = [])
    {
        $this->attributes = $attributes;
        return $this;
    }
    
    /**
     * @return array
     */
    public function getAttributes() 
    {
        return $this->attributes;
    }
    
    /**
     * 
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getAttribute($name, $default = null) 
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }
        
        return $default;
    }

    /**
     * 初始化Request对象
     *
     */
    public function __construct() 
    {
    }

    /**
     * Set the method for this request
     *
     * @param  string $method
     * @return Request
     * @throws Exception\InvalidArgumentException
     */
    public function setMethod($method)
    {
        $method = strtoupper($method);
        if (!defined('static::METHOD_' . $method) && !$this->getAllowCustomMethods()) {
            throw new \InvalidArgumentException('Invalid HTTP method passed');
        }
        $this->method = $method;
        return $this;
    }
    
    /**
     * 获取当前请求方法（OPTIONS,GET,HEAD,POST,PUT,DELETE,TRACE,CONNECT,PATCH,PROPFIND或自定义方法）
     * @return string
     */
    public function getMethod() 
    {
        $this->method || $this->method = strtoupper($this->getServer('REQUEST_METHOD'));
        
        return $this->method;
    }

    /**
     * 获得用户请求的数据
     * 
     * 获取请求变量的值,未设置则返回$defaultValue
     * @param string $key = null 获取的参数name,默认为null将获得$this->attributes, $_GET, $_REQUEST, $_POST合并的值
     * @param mixed $defaultValue = null 当获取值失败的时候返回缺省值,默认值为null
     * @return mixed
     */
    public function getRequest($key = null, $defaultValue = null) 
    {
        if (!$key) {
            static $all = null;
            if ($all === null) {
                $all = array_merge($this->attributes, $_GET, $_REQUEST, $_POST);
            }
            return $all;
        }
        
        if (isset($_POST[$key])) {
            return $_POST[$key];
        }
        
        if (isset($_REQUEST[$key])) {
            return $_REQUEST[$key];
        }

        if (isset($_GET[$key])) {
            return $_GET[$key];
        }
        
        if (isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }
        
        if (isset($_COOKIE[$key])) {
            return $_COOKIE[$key];
        }
        
        return $defaultValue;
    }

    /**
     * 获取请求的表单数据
     * 
     * 从$_POST获得值
     * @param string $name = null 获取的变量名,默认为null,当为null的时候返回$_POST数组
     * @param string $defaultValue = null 当获取变量失败的时候返回该值,默认为null
     * @return mixed
     */
    public function getPost($name = null, $defaultValue = null) 
    {
        if ($name === null) {
            return $_POST;
        }
        
        return isset($_POST[$name]) ? $_POST[$name] : $defaultValue;
    }

    /**
     * 获得$_GET值
     * 
     * @param string $name = null 待获取的变量名,默认为空字串,当该值为null的时候将返回$_GET数组
     * @param string $defaultValue = null 当获取的变量不存在的时候返回该缺省值,默认值为null
     * @return mixed
     */
    public function getGet($name = null, $defaultValue = null) 
    {
        if ($name === null) {
            return $_GET;
        }
        return (isset($_GET[$name])) ? $_GET[$name] : $defaultValue;
    }

    /**
     * 返回cookie的值
     * 
     * 如果$name=null则返回所有Cookie值
     * @param string $name = null 获取的变量名,如果该值为null则返回$_COOKIE数组,默认为null
     * @param string $defaultValue = null 当获取变量失败的时候返回该值,默认该值为null
     * @return mixed
     */
    public function getCookie($name = null, $defaultValue = null) 
    {
        if ($name === null) {
            return $_COOKIE;
        }
        return (isset($_COOKIE[$name])) ? $_COOKIE[$name] : $defaultValue;
    }

    /**
     * 返回session的值
     * 
     * 如果$name=null则返回所有SESSION值
     * @param string $name = null 获取的变量名,如果该值为null则返回$_SESSION数组,默认为null
     * @param string $defaultValue = null 当获取变量失败的时候返回该值,默认该值为null
     * @return mixed
     */
    public function getSession($name = null, $defaultValue = null)
    {
        if ($name === null) {
            return $_SESSION;
        }
        return (isset($_SESSION[$name])) ? $_SESSION[$name] : $defaultValue;
    }

    /**
     * 返回Server的值
     * 
     * 如果$name为空则返回所有Server的值
     * @param string $name = null 获取的变量名,如果该值为null则返回$_SERVER数组,默认为null
     * @param string $defaultValue = null 当获取变量失败的时候返回该值,默认该值为null
     * @return mixed
     */
    public function getServer($name = null, $defaultValue = null)
    {
        if ($name === null) {
            return $_SERVER;
        }
        
        $value = (isset($_SERVER[$name])) ? $_SERVER[$name] : $defaultValue;
        return $value;
    }

    /**
     * 返回ENV的值
     * 
     * 如果$name为null则返回所有$_ENV的值
     * @param string $name = null 获取的变量名,如果该值为null则返回$_ENV数组,默认为null
     * @param string $defaultValue = null 当获取变量失败的时候返回该值,默认该值为null
     * @return mixed
     */
    public function getEnv($name = null, $defaultValue = null) 
    {
        if ($name === null) {
            return $_ENV;
        }
        return (isset($_ENV[$name])) ? $_ENV[$name] : $defaultValue;
    }

    /**
     * 获取请求链接协议
     * 
     * 如果是安全链接请求则返回https否则返回http
     * @return string 
     */
    public function getScheme()
    {
        return ($this->getServer('HTTPS') == 'on') ? 'https' : 'http';
    }

    /**
     * 返回请求页面时通信协议的名称和版本
     * 
     * @return string
     */
    public function getProtocol() 
    {
        return $this->getServer('SERVER_PROTOCOL', 'HTTP/1.0');
    }

    /**
     * 获得请求方式（cli|web）
     * 
     * 如果是web请求将返回web
     * @return string  
     */
    public function getRequestType() 
    {
        return PHP_SAPI == 'cli' ? 'cli' : 'web';
    }

    /**
     * 判断该请求是否是AJAX请求
     * 
     * @return bool
     */
    public function isAjaxRequest()
    {
        return (
            (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
            || !empty($this->getRequest('ajax'))
            || !empty($this->getRequest('ajax_cb'))
            || !empty($this->getRequest('if_cb'))
        );
    }
    
    /**
     * 是否是XMLHttpRequest请求
     *
     * @return bool
     */
    public function isXmlHttpRequest()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
    }
    
    /**
     * 是否是Flash客户端请求
     *
     * @return bool
     */
    public function isFlashRequest()
    {
        $agent = $this->getUserAgent();
        return $agent && false !== stripos($agent, ' flash');
    }

    /**
     * 请求是否使用的是HTTPS安全链接
     * 
     * 如果是安全请求则返回true否则返回false
     * @return bool
     */
    public function isSecure()
    {
        return !strcasecmp($this->getServer('HTTPS'), 'on');
    }

    /**
     * 当前请求方法是否是GET
     * 
     * 如果请求是GET方式请求则返回true，否则返回false
     * @return bool 
     */
    public function isGet()
    {
        return ($this->getMethod() === self::METHOD_GET);
    }

    /**
     * 当前请求方法是否是POST
     * 
     * 如果请求是POST方式请求则返回true,否则返回false
     * 
     * @return bool
     */
    public function isPost()
    {
        return ($this->getMethod() === self::METHOD_POST);
    }

    /**
     * 当前请求方法是否是PUT
     * 
     * 如果请求是PUT方式请求则返回true,否则返回false
     * 
     * @return bool
     */
    public function isPut()
    {
        return ($this->getMethod() === self::METHOD_PUT);
    }

    /**
     * 当前请求方法是否是DELETE
     * 
     * 如果请求是DELETE方式请求则返回true,否则返回false
     * 
     * @return bool
     */
    public function isDelete()
    {
        return ($this->getMethod() === self::METHOD_DELETE);
    }
    
    /**
     * 当前请求方法是否是OPTIONS
     *
     * @return bool
     */
    public function isOptions()
    {
        return ($this->getMethod() === self::METHOD_OPTIONS);
    }
    
    /**
     * 当前请求方法是否是PROPFIND
     *
     * @return bool
     */
    public function isPropFind()
    {
        return ($this->getMethod() === self::METHOD_PROPFIND);
    }
    
    /**
     * 当前请求方法是否是HEAD
     *
     * @return bool
     */
    public function isHead()
    {
        return ($this->getMethod() === self::METHOD_HEAD);
    }
    
    /**
     * 当前请求方法是否是TRACE
     *
     * @return bool
     */
    public function isTrace()
    {
        return ($this->getMethod() === self::METHOD_TRACE);
    }
    
    /**
     * 当前请求方法是否是CONNECT
     *
     * @return bool
     */
    public function isConnect()
    {
        return ($this->getMethod() === self::METHOD_CONNECT);
    }
    /**
     * 当前请求方法是否是PATCH
     *
     * @return bool
     */
    public function isPatch()
    {
        return ($this->getMethod() === self::METHOD_PATCH);
    }    

    /**
     * 初始化请求的资源标识符
     * 
     * 这里的uri是去除协议名、主机名的
     * <pre>Example:
     * 请求： http://www.windwork.org/demo/index.php?app=xx
     * 则返回: /demo/index.php?app=xx
     * </pre>
     * 
     * @return string 
     * @throws Exception 当获取失败的时候抛出异常
     */
    public function getRequestUri() 
    {
        if (!$this->uri) {
            $queryString = (PHP_SAPI == 'cli' && isset($_SERVER['argv'][1])) ? $_SERVER['argv'][1] : $this->getServer('QUERY_STRING');
            if (!$this->getServer('REQUEST_URI')) {
                $_SERVER['REQUEST_URI'] = $this->getScriptUrl() . ($queryString ? '?'  : '') . $queryString;
            } else if (strpos($this->getServer('REQUEST_URI'), '?') === false && $queryString) {
                $_SERVER['REQUEST_URI'] .= '?' . $queryString;
            }
            
            $this->uri = &$_SERVER['REQUEST_URI'];    
        }
        
        return $this->uri;
    }
    
    /**
     * 设置请求URI
     * @param string $uri
     * @return \wf\app\web\Request
     */
    public function setRequestUri($uri)
    {
        $this->uri = $uri;
        return $this;
    }
    
    /**
     * 获取当前请求URL
     * @return string
     */
    public function getRequestUrl()
    {
        $url = rtrim($this->getHostInfo(), '/') . $this->getRequestUri();
        return $url;
    }

    /**
     * 返回当前执行脚本的绝对路径
     * 
     * <pre>Example:
     * 请求: http://www.windwork.org/demo/index.php?mod=xx&ctl=xx&act=xx
     * 返回: /demo/index.php
     * </pre>
     * 
     * @return string
     */
    public function getScriptUrl()
    {
        if (!$this->scriptUrl) {
            $scriptName = 'index.php';
            if(false != $scriptUrl = $this->getServer('PHP_SELF')) {
                $scriptName = $this->getServer('SCRIPT_FILENAME');
                // 去掉 index.php之后的字符
                if (false !== ($pos = stripos($scriptUrl, basename($scriptName)))) {
                    $scriptUrl = substr($scriptUrl, 0, $pos + strlen(basename($scriptName)));
                }
                
                if($scriptName && $scriptName == $scriptUrl) {
                    $docRoot = $this->getServer('DOCUMENT_ROOT');
                    if($docRoot && strpos($scriptName, $docRoot) === 0) {
                        $scriptUrl = str_replace('\\','/', str_replace($docRoot, '', $scriptName));
                    } else {
                        $scriptUrl = '/'.basename($scriptName);
                    }
                } elseif ($scriptName && strlen($scriptName) < strlen($scriptUrl)) {
                    $scriptUrl = '/'.basename($scriptName);
                } else {
                    //$scriptUrl = $scriptUrl;
                }
            } else {
                $scriptUrl = '/'.basename($scriptName);
            }
            
            $this->scriptUrl = htmlentities($scriptUrl);
        }
        
        return $this->scriptUrl;
    }
    
    /**
     * 返回系统所在的文件夹相对于根目录的路径
     * 
     * <pre>
     * Example:
     * 请求: http://www.windwork.org/demo/index.php?app=xx
     * 返回: /demo/
     * </pre>
     * 
     * @return string
     */
    public function getBasePath() 
    {
        $basePath = str_replace($this->getScript(), '', $this->getScriptUrl());
        $basePath = '/' . ltrim($basePath, '/');
        return $basePath;
    }

    /**
     * 返回执行脚本名称
     * 
     * <pre>
     * Example:
     * 请求: http://www.windwork.org/demo/index.php?app=xx
     * 返回: index.php
     * </pre>
     * 
     * @return string
     * @throws Exception 当获取失败的时候抛出异常
     */
    public function getScript()
    {
        if (($pos = strrpos($this->getScriptUrl(), '/')) === false) {
            $pos = -1;
        }
        
        $script = substr($this->getScriptUrl(), $pos + 1);
        return $script;
    }

    /**
     * 获取Http头信息
     * 
     * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @param string $header 头部名称 
     * @param string $default 获取失败将返回该值,默认为null
     * @return string
     */
    public function getHeader($header, $default = null) 
    {
        $name = strtoupper(str_replace('-', '_', $header));
        if (substr($name, 0, 5) != 'HTTP_') {
            $name = 'HTTP_' . $name;
        }
        
        if (($header = $this->getServer($name)) != null) {
            return $header;
        }
        
        return $default;
    }

    /**
     * 获得主机信息，包含协议信息，主机名，访问端口信息
     * 
     * <pre>Example:
     * 请求: http://www.windwork.org/demo/index.php?app=xx
     * 返回： http://www.windwork.org
     * 
     * 请求: http://www.windwork.org:8080/demo/index.php?app=xx
     * 返回： http://www.windwork.org:8080
     * </pre>
     * @return string
     * @throws Exception 获取主机信息失败的时候抛出异常
     */
    public function getHostInfo() 
    {
        if ($this->hostInfo === null) {
            $http = $this->isSecure() ? 'https' : 'http';
            if (($httpHost = $this->getServer('HTTP_HOST')) != null) {
                // fix hosts xss
                $httpHost = preg_replace("/[^a-z0-9\\-\\.]/i", '', $httpHost);
                $this->hostInfo = "{$http}://{$httpHost}";
            } elseif (($httpHost = $this->getServer('SERVER_NAME')) != null) {
                // fix hosts xss
                $httpHost = preg_replace("/[^a-z0-9\\-\\.]/i", '', $httpHost);
                $this->hostInfo = "{$http}://{$httpHost}";
                if (($port = $this->getServerPort()) != null) {
                    $this->hostInfo .= ':' . $port;
                }
            } elseif (isset($_SERVER['argc'])) {
                $this->hostInfo = '';
            } else {
                //$this->hostInfo = $http . "://localhost";
                throw new \Exception('Determine the entry host info failed!!');
            }
        }
        
        return $this->hostInfo;
    }

    /**
     * 返回当前运行脚本所在的服务器的主机名。
     * 
     * 如果脚本运行于虚拟主机中
     * 该名称是由那个虚拟主机所设置的值决定
     * @return string
     */
    public function getServerName() 
    {
        return $this->getServer('SERVER_NAME', '');
    }

    /**
     * 返回服务端口号
     *
     * https链接的默认端口号为443
     * http链接的默认端口号为80
     * @return int
     */
    public function getServerPort()
    {
        if (!$this->port) {
            $default = $this->isSecure() ? 443 : 80;
            $this->setServerPort($this->getServer('SERVER_PORT', $default));
        }
        return $this->port;
    }

    /**
     * 设置服务端口号
     * 
     * https链接的默认端口号为443
     * http链接的默认端口号为80
     * @param int $port 设置的端口号
     */
    public function setServerPort($port)
    {
        $this->port = (int) $port;
    }

    /**
     * 返回浏览当前页面的用户的主机名
     * 
     * DNS 反向解析不依赖于用户的 REMOTE_ADDR
     * 
     * @return string
     */
    public function getRemoteHost() 
    {
        return $this->getServer('REMOTE_HOST');
    }

    /**
     * 返回浏览器发送Referer请求头
     * 
     * 可以让服务器了解和追踪发出本次请求的起源URL地址
     * 
     * @return string
     */
    public function getRefererUrl() 
    {
        return $this->getServer('HTTP_REFERER');
    }

    /**
     * 获得用户机器上连接到 Web 服务器所使用的端口号
     * 
     * @return number
     */
    public function getRemotePort() 
    {
        return $this->getServer('REMOTE_PORT');
    }

    /**
     * 返回User-Agent头字段用于指定浏览器或者其他客户端程序的类型和名字
     * 
     * 如果客户机是一种无线手持终端，就返回一个WML文件；如果发现客户端是一种普通浏览器，
     * 则返回通常的HTML文件
     * 
     * @return string
     */
    public function getUserAgent()
    {
        return $this->getServer('HTTP_USER_AGENT', '');
    }

    /**
     * 返回当前请求头中 Accept: 项的内容，
     * 
     * Accept头字段用于指出客户端程序能够处理的MIME类型，例如 text/html,image/*
     * 
     * @return string
     */
    public function getAcceptTypes() 
    {
        return $this->getServer('HTTP_ACCEPT', '');
    }

    /**
     * 返回客户端程序可以能够进行解码的数据编码方式
     * 
     * 这里的编码方式通常指某种压缩方式
     * @return string|''
     */
    public function getAcceptCharset() 
    {
        return $this->getServer('HTTP_ACCEPT_ENCODING', '');
    }

    /**
     * 返回客户端程序期望服务器返回哪个国家的语言文档
     *
     * Accept-Language: en-us,zh-cn
     * @return string
     */
    public function getAcceptLanguage() 
    {
        if (!$this->language) {
            $language = explode(',', $this->getServer('HTTP_ACCEPT_LANGUAGE', ''));
            $this->language = $language[0] ? $language[0] : 'zh-cn';
        }
        
        return $this->language;
    }
    
    /**
     * @return boolean
     */
    public function getAllowCustomMethods() 
    {
        return $this->allowCustomMethods;
    }
    
    /**
     * @param boolean $strictMethods
     */
    public function setAllowCustomMethods($strictMethods)
    {
        $this->allowCustomMethods = (bool) $strictMethods;
    }
    
    /**
     * 客户端是否是搜索引擎的爬虫 ，如果是已知爬虫，则返回爬虫类型
     *
     * @return bool|string
     */
    public static function checkRobot()
    {
        static $robot = null;
        if($robot !== null) {
            return $robot;
        }
        
        $spiders  = 'bot|crawl|spider|slurp|sohu-search|lycos|robozilla';
        $browsers = 'MSIE|Netscape|Opera|Konqueror|Mozilla';
        $agent    = @$_SERVER['HTTP_USER_AGENT'];
        
        if(strpos($agent, 'http://') === false && preg_match("/($browsers)/i", $agent)) {
            $robot = false;
        } elseif(preg_match("/($spiders)/i", $agent)) {
            $robot = 'UnknowSpider';
            
            $botList = [
                'GoogleBot',
                'mediapartners-google',
                'BaiduSpider',
                '360Spider',
                'msnbot',
                'bingbot',
                'yodaobot',
                'yahoo! slurp',
                'yahoo! slurp china',
                'iaskspider',
                'Sogou web spider',
                'Sogou push spider',
                'YisouSpider'
            ];
            
            foreach ($botList as $bot) {
                if(false !== stripos($agent, $bot)) {
                    $robot = $bot;
                    break;
                }
            }
        } else {
            $robot = false;
        }
        
        return $robot;
    }
    
    /**
     * 获得浏览器名称和版本
     *
     * @return string
     */
    public static function getUserBrowser()
    {
        static $userBrowser = '';
        if($userBrowser) {
            return $userBrowser;
        }
        
        $agent    = @$_SERVER['HTTP_USER_AGENT'];
        $browser  = '';
        $version  = '';
        
        if (preg_match('/MSIE\s([^\s|;]+)/i', $agent, $regs)) {
            $browser = 'Internet Explorer';
            $version = $regs[1];
        } elseif (preg_match('/Chrome\/([^\s]+)/i', $agent, $regs)) {
            $browser = 'Chrome';
            $version = $regs[1];
        } elseif (preg_match('/FireFox\/([^\s]+)/i', $agent, $regs)) {
            $browser = 'FireFox';
            $version = $regs[1];
        } elseif (preg_match('/Maxthon/i', $agent, $regs)) {
            $browser = '(Internet Explorer ' .$version. ') Maxthon';
            $version = '';
        } elseif (preg_match('/Opera[\s|\/]([^\s]+)/i', $agent, $regs)) {
            $browser = 'Opera';
            $version = $regs[1];
        } elseif (preg_match('/OmniWeb\/(v*)([^\s|;]+)/i', $agent, $regs)) {
            $browser = 'OmniWeb';
            $version = $regs[2];
        } elseif (preg_match('/Netscape([\d]*)\/([^\s]+)/i', $agent, $regs)) {
            $browser = 'Netscape';
            $version = $regs[2];
        } elseif (preg_match('/safari\/([^\s]+)/i', $agent, $regs)) {
            $browser = 'Safari';
            $version = $regs[1];
        } elseif (preg_match('/NetCaptor\s([^\s|;]+)/i', $agent, $regs)) {
            $browser = '(Internet Explorer ' .$version. ') NetCaptor';
            $version = $regs[1];
        } elseif (preg_match('/Lynx\/([^\s]+)/i', $agent, $regs)) {
            $browser = 'Lynx';
            $version = $regs[1];
        }
        
        if (!empty($browser)) {
            return $userBrowser = addslashes($browser . ' ' . $version);
        } else {
            return $userBrowser = 'Unknow Browser';
        }
    }
    
    
    /**
     * 获得客户端的操作系统
     *
     * @return string
     */
    public static function getUserOS()
    {
        static $os = '';
        if ($os) {
            return $os;
        }
        
        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            return 'Unknown';
        }
        
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $os    = '';
        
        if (strpos($agent, 'win') !== false) {
            $os = 'Windows';
        } elseif (strpos($agent, 'android') !== false) {
            $os = 'Android';
        } elseif(preg_match("/(iPhone|iPad)/i", $agent)) {
            $os = 'iOS';
        } elseif (strpos($agent, 'blackberry') !== false) {
            $os = 'BlackBerry';
        } elseif (strpos($agent, 'hpwos') !== false) {
            $os = 'WebOS';
        } elseif (strpos($agent, 'symbian') !== false) {
            $os = 'Symbian';
        } elseif (strpos($agent, 'linux') !== false) {
            $os = 'Linux';
        } elseif (strpos($agent, 'unix') !== false) {
            $os = 'Unix';
        } elseif (strpos($agent, 'sunos') !== false) {
            $os = 'SunOS';
        } elseif (strpos($agent, 'os/2') !== false) {
            $os = 'IBM OS/2';
        } elseif (strpos($agent, 'mac') !== false) {
            $os = 'Mac';
        } elseif (strpos($agent, 'powerpc') !== false) {
            $os = 'PowerPC';
        } elseif (strpos($agent, 'aix') !== false) {
            $os = 'AIX';
        } elseif (strpos($agent, 'hpux') !== false) {
            $os = 'HPUX';
        } elseif (strpos($agent, 'netbsd') !== false) {
            $os = 'NetBSD';
        } elseif (strpos($agent, 'bsd') !== false) {
            $os = 'BSD';
        } elseif (strpos($agent, 'osf1') !== false) {
            $os = 'OSF1';
        } elseif (strpos($agent, 'irix') !== false) {
            $os = 'IRIX';
        } elseif (strpos($agent, 'freebsd') !== false) {
            $os = 'FreeBSD';
        } elseif (strpos($agent, 'teleport') !== false) {
            $os = 'teleport';
        } elseif (strpos($agent, 'flashget') !== false) {
            $os = 'flashget';
        } elseif (strpos($agent, 'webzip') !== false) {
            $os = 'webzip';
        } elseif (strpos($agent, 'offline') !== false) {
            $os = 'offline';
        } elseif(preg_match("/(Bot|Crawl|Spider)/i", $agent)) {
            $os = 'Spiders'; // 爬虫
        } else {
            $os = 'Other';
        }
        
        return $os;
    }
    
    /**
     * 客户端手机型号
     *
     * @return string|bool
     */
    public static function checkMobile()
    {
        static $mobile = null;
        if($mobile !== null) {
            return $mobile;
        }
        
        $mobile = false;
        if(empty($_SERVER['HTTP_USER_AGENT'])) {
            return false;
        }
        
        $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
        
        // 平板（不是酷派）
        if((strpos($userAgent, 'pad') && false === strpos($userAgent, 'coolpad')) || strpos($userAgent, 'gt-p1000')) {
            return false;
        }
        
        $mobileBrowserList = ['iphone', 'android', 'phone', 'mobile', 'wap', 'netfront', 'java', 'opera mobi', 'opera mini',
            'ucweb', 'windows ce', 'symbian', 'series', 'webos', 'sony', 'blackberry', 'dopod', 'nokia', 'samsung', 'palmsource',
            'xda', 'pieplus', 'meizu', 'midp', 'cldc', 'motorola', 'foma', 'docomo', 'up.browser',
            'up.link', 'blazer', 'helio', 'hosin', 'huawei', 'novarra', 'coolpad', 'webos', 'techfaith', 'palmsource',
            'alcatel', 'amoi', 'ktouch', 'nexian', 'ericsson', 'philips', 'sagem', 'wellcom', 'bunjalloo', 'maui', 'smartphone',
            'iemobile', 'spice', 'bird', 'zte-', 'longcos', 'pantech', 'gionee', 'portalmmm', 'jig browser', 'hiptop',
            'benq', 'haier', '^lct', '320x320', '240x320', '176x220', 'coolpad', 'MicroMessenger'];
        
        foreach($mobileBrowserList as $browser) {
            if (false !== stripos($userAgent, $browser, true)){
                $mobile = $browser;
                break;
            }
        }
        
        
        return $mobile;
    }
    
    /**
     * 获得用户操作系统的换行符
     * @return string
     */
    public static function getUserCrlf()
    {
        static $crlf = null;
        if ($crlf !== null) {
            return $crlf;
        }
        
        if(empty($_SERVER['HTTP_USER_AGENT'])) {
            return $crlf = "\n";
        }
        
        if (stristr($_SERVER['HTTP_USER_AGENT'], 'Win')) {
            $crlf = "\r\n";
        } elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'Mac')) {
            $crlf = "\r"; // for old MAC OS
        } else {
            $crlf = "\n";
        }
        
        return $crlf;
    }
    
    /**
     * 返回访问IP
     *
     * 如果获取请求IP失败,则返回0.0.0.0
     * @return string
     */
    public static function getUserIp()
    {
        static $clientIp;
        
        if ($clientIp) {
            return $clientIp;
        }
        
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(",", $_SERVER['HTTP_X_FORWARDED_FOR']);
                foreach ($arr as $ip) {
                    $ip = trim($ip);
                    if ($ip != "unknown") {
                        $clientIp = $ip;
                        break;
                    }
                }
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $clientIp = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $clientIp = $_SERVER['REMOTE_ADDR'];
            } else {
                $clientIp = "0.0.0.0";
            }
        } elseif (getenv("HTTP_X_FORWARDED_FOR")) {
            $clientIp = getenv("HTTP_X_FORWARDED_FOR");
        } elseif (getenv("HTTP_CLIENT_IP")) {
            $clientIp = getenv("HTTP_CLIENT_IP");
        } else {
            $clientIp = getenv("REMOTE_ADDR");
        }
        
        preg_match( "/[\\d\\.]{7,15}/", $clientIp, $onlineIp);
        $clientIp = !empty($onlineIp[0]) ? $onlineIp[0] : '0.0.0.0';
        
        $clientIp = preg_replace("/[^0-9\\.]/", '', $clientIp);
        
        return $clientIp;
    }
}
