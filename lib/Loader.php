<?php
/**
 * Windwork
 * 
 * 一个用于快速开发高并发Web应用的轻量级PHP框架
 * 
 * @copyright Copyright (c) 2008-2017 Windwork Team. (http://www.windwork.org)
 * @license   http://opensource.org/licenses/MIT
 */

/**
 * 此文件为使用框架应用应该加载的第一个文件
 */
namespace wf\app;

defined('WF_IN') or die('access denied');
defined('ROOT_DIR') or die('Please define "ROOT_DIR" const (where the site document root directory)');

// 程序执行开始时间
define('__WF_START_TIME', microtime(1));

// 开始执行占内存量
define('__WF_START_MEM', memory_get_usage());

// Windwork框架各组件所在文件夹，如{ROOT_DIR}/wf/src/或{ROOT_DIR}/vendor/windwork/等
define('__WF_BASE_DIR', substr(__DIR__, 0, -7));

/*
 * 设置系统部署环境默认值
 *   dev）开发环境；
 *   test）测试环境；
 *   product）正式产品环境
 *   自定义）自定义的运行环境，被视为正式运行环境，配置文件放在“config/自定义”文件夹中
 */
defined('WF_ENV') or define('WF_ENV', 'dev');

/**
 * Windwork加载器
 * 
 * @package     wf.app
 * @author      cm <cmpan@qq.com>
 * @link        http://docs.windwork.org/manual/wf.app.loader.html
 * @since       0.1.0
 */
final class Loader 
{
    /**
     * 是否已注册Windwork自动加载，已注册则可不再重新注册
     * 
     * @var bool
     */
    private static $isReged = false;
    
    /**
     * 是否已加载合并成一个文件的的windwork框架主要类
     * @var bool
     */
    private static $isWfInOneLoaded;
    
    /**
     * 自动加载类文件时查找类所在的文件夹
     * @var array
     */
    private static $classPath = [];
    
    /**
     * 命名空间对应文件夹
     * @var array
     */
    private static $namespaceMap = [
    ];
    
    /**
     * 添加类文件目录
     * @param array $classPath
     */
    public static function addClassPath(array $classPath) 
    {
        static::$classPath = $classPath + static::$classPath;
    }
    
    /**
     * 获取已设置的classPath
     * @return array
     */
    public static function getClassPath()
    {
        return static::$classPath;
    }
    
    /**
     * 加载类脚本
     * @param string $class
     */
    public static function import($class) 
    {
        $file = static::getClassFile($class);
        
        if ($file) {
            return include_once $file;
        }
        
        return false;
    }
    
    /**
     * 获取类文件路径
     * @param string $class
     * @return string|boolean
     */
    public static function getClassFile($class) {
        $class = '\\' . ltrim($class, '\\');
        
        // wf框架组件源码放到组件文件夹下的lib子文件夹
        if(preg_match("/^(\\\\wf\\\\)([a-z0-9]+\\\\)(.+)/i", $class, $match)) {
            // "libs/wf-{$component}/lib/{$class}.php";
            // "wf/{$component}/lib/{$class}.php";
            // "vendor/windwork/{$component}/lib/{$class}.php";
            $file = __WF_BASE_DIR . strtr("{$match[2]}lib\\{$match[3]}", '\\', '/') . '.php';
            if (is_file($file)) {
                return $file;
            }            
        }
        
        // 通用加载文件方式，命名空间与文件夹对应
        $file = strtr($class, '\\', DIRECTORY_SEPARATOR) . '.php';
        
        foreach (static::$classPath as $dir) {
            if (is_file($dir . $file)) {
                return $dir . $file;
            }
        }
        
        return false;
    }
    
    /**
     * 注册自动加载类
     */
    public static function regAutoload() 
    {
        if (static::$isReged) {
            return;
        }

        // 注册自动加载类方法
        spl_autoload_register('\wf\app\Loader::import', false, true);
        
        // 加载wf帮助函数
        require_once __DIR__ . '/helper.php';
        
        // 正式环境合并框架主文件
        if (defined('WF_ENV') && WF_ENV && !in_array(WF_ENV, ['dev', 'test'])) {
            static::loadWfInOne();
        }
    }
    
    /**
     * wf框架主要文件合并后可一次加载
     * @return boolean
     */
    public static function loadWfInOne()
    {
        if (static::$isWfInOneLoaded) {
            return true;
        }
        
        $inOneFile = ROOT_DIR . '/data/temp/wf_main_in_one.php';
        
        if (is_file($inOneFile) && date('Y-m-d', filemtime($inOneFile)) == date('Y-m-d')) {
            static::$isWfInOneLoaded = include_once $inOneFile;
            return static::$isWfInOneLoaded;
        }
        
        if (!is_writeable(dirname($inOneFile))) {
            return false;
        }
        
        // 待合并预加载的类
        $libClass = [
            // app
            '\\wf\\app\\ApplicationAbstrct',
            '\\wf\\app\\Benchmark',
            '\\wf\\app\\Config',
            '\\wf\\app\\Hook',
            '\\wf\\app\\HookInterface',
            '\\wf\\app\\I18n',
            '\\wf\\app\\Object',
            '\\wf\\app\\ServiceLocator',
            '\\wf\\app\\Session',
            '\\wf\\app\\Version',
            
            // mvc
            '\\wf\\app\\web\\Application',
            '\\wf\\app\\web\\Controller',
            '\\wf\\app\\web\\Dispatcher',
            '\\wf\\app\\web\\Message',
            '\\wf\\app\\web\\Output',
            '\\wf\\app\\web\\Request',
            '\\wf\\app\\web\\Response',
            '\\wf\\route\\RouteAbstract',
            '\\wf\\route\\strategy\\Simple',
            '\\wf\\model\\Model',
            '\\wf\\model\\Error',
            '\\wf\\model\\ActiveRecord',
            '\\wf\\template\\EngineInterface',
            '\\wf\\template\\strategy\\Wind',
            
            // cache
            '\\wf\\cache\\CacheInterface',
            '\\wf\\cache\\CacheAbstract',
            '\\wf\\cache\\strategy\\File',
            '\\wf\\cache\\strategy\\Memcache',
            '\\wf\\cache\\strategy\\Memcached',
            '\\wf\\cache\\strategy\\Redis',
            
            // db
            '\\wf\\db\\DBInterface',
            '\\wf\\db\\DBAbstract',
            '\\wf\\db\\Finder',
            '\\wf\\db\\QueryBuilder',
            '\\wf\\db\\strategy\\MySQLi',
            '\\wf\\db\\strategy\\PDOMySQL',
            
            // logger
            '\\wf\\logger\\LoggerInterface',
            '\\wf\\logger\\LoggerAbstract',
            '\\wf\\logger\\strategy\\File',
        ];
        
        $liteContent = "<?php\n";
        
        foreach ($libClass as $class) {
            $file = static::getClassFile($class);
            if (!$file) {
                continue;
            }
            
            // 类文件源码内容
            $content = file_get_contents($file);
            $content = substr(trim($content), 5); // 去掉 <?php
            $content = trim($content);
            
            
            // 提取namespace
            if(preg_match("/namespace\s+.*?;/", $content, $match)) {            
                $ns = $match[0];
                if(false !== strpos($liteContent, $ns)) {
                    $content = str_replace($ns, '', $content);
                }
            }
                        
            $liteContent .= "\n# {$class}\n". $content;
        }
        
        // 去掉文档注释
        $liteContent = preg_replace("/\\/\\*\\*(\r)?\n.*?\\*\\//is", '', $liteContent);
        
        $liteContentArr = explode("\n", $liteContent);
        
        foreach ($liteContentArr as $lineNumber => $lineText) {
            // 去掉行后单行注释
            $liteContentArr[$lineNumber] = preg_replace("/(.*;)(\s*\\/\\/.*)/", "\\1", $lineText);
            
            // 一行字符串
            $lineText = trim($lineText);
            
            // 去掉空行
            if (strlen($lineText) == 0) {
                unset($liteContentArr[$lineNumber]);
                continue;
            }
            
            // 去掉单行注释
            if (strlen($lineText) >= 2 && "{$lineText[0]}{$lineText[1]}" == '//') {
                unset($liteContentArr[$lineNumber]);
                continue;
            }
            
            // 去掉包含文件
            if (substr($lineText, 0, 12) == 'require_once' || substr($lineText, 0, 12) == 'include_once') {
                unset($liteContentArr[$lineNumber]);
                continue;
            }
        }
        
        $liteContent = implode("\n", $liteContentArr);
        
        if(file_put_contents($inOneFile, $liteContent)) {
            static::$isWfInOneLoaded = include_once $inOneFile;
            return static::$isWfInOneLoaded;
        }
        
        return false;
    }
}

// 注册自动加载类
\wf\app\Loader::regAutoload();

