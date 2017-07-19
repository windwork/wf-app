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
 * 系统配置操作类
 * 
 * @package     wf.app
 * @author      cm <cmpan@qq.com>
 * @link        http://docs.windwork.org/manual/wf.app.config.html
 * @since       0.1.0
 */
final class Config 
{
    /**
     * 配置选项
     * 
     * @var array
     */
    private $configs = [];
    
    /**
     * 配置文件所在目录
     * @var string
     */
    private $cfgDir = '';
    
    /**
     * 配置文件所属运行环境
     * 
     * 包括develop/test/product，加载配置文件时，先从config文件夹中以该值命名的子文件夹找配置文件
     * @var string
     */
    private $env = 'develop';
    
    /**
     * 设置配置文件所在文件夹、运行环境
     * @param string $dir 配置文件目录
     * @param string $env = 'develop' 运行环境
     */
    public function __construct($dir, $env = 'develop') 
    {
        $this->cfgDir = rtrim($dir, '\/');
        $this->env = $env;
    }

    /**
     * 设置配置选项
     * 
     * <pre>
     * // 修改一个选项
     * // $this->configs['timezone'] = 'Asia/Shanghai'
     * $conf->set('timezone', 'Asia/Shanghai');
     * 
     * // $this->configs['cache']['enabled'] = 1
     * $conf->set('cache.enabled', 1);
     * 
     * // 一次设置多个选项使用 $this->merge($cfgs); 方法
     * 
     * </pre>
     * 
     * @param string $name 数组下标，访问多维数组使用.隔开
     * @param mixed $val
     * @return \wf\app\Config
     */
    public function set($name, $val) 
    {
        $nameArr = explode('.', $name);
        krsort($nameArr);
        
        foreach($nameArr as $key) {
            if (isset($cfgs)) {
                $cfgs = [$key => $cfgs];
            } else {
                $cfgs = [$key => $val];
            }
        }
        
        if ($this->env != 'product') {
            $this->checkKey($cfgs);
        }
        
        $this->merge($cfgs);
        
        return $this;
    }
    
    /**
     * 读取配置变量
     * 
     * <pre>
     * // 访问 $this->configs['url']
     * $conf->get('url'); 
     * 
     * // 访问 $this->configs['url']['rewrite']
     * $conf->get('url.rewrite'); 
     * 
     * // 访问 $this->configs['url']['alias']['login']
     * $conf->get('url.alias.login'); 
     * </pre>
     * 
     * @param string $name 配置参数下标，访问多层级数组用.隔开
     * @return NULL|mixed
     */
    public function get($name) 
    {
        $idx = explode('.', $name);
        
        if (isset($idx[3])) {
            $lv3 = isset($this->configs[$idx[0]][$idx[2]][$idx[2]][$idx[3]]) ? $this->configs[$idx[0]][$idx[1]][$idx[2]][$idx[3]] : null;
            
            if ($lv3 === null || (!$idx[4] || !isset($lv3[$idx[4]]))) {
                // 访问第3维
                return $lv3;
            }

            // 访问第3维以上，使用变量复制递归获取，性能稍微有影响
            // 不建议使用层级大于4的配置
            
            // 第4维
            $item = $lv3[$idx[4]];
            
            // 去掉下标数组中的0-4维下标名
            unset($idx[0], $idx[1], $idx[2], $idx[3], $idx[4]);
            
            foreach ($idx as $index) {
                if (!isset($item[$index])) {
                    return null;
                }
                $item = $item[$index];
            }
            
            return $item;
        }
        
        // 访问 0-2维
        
        if (isset($idx[2])) {
            // 访问第2维
            return isset($this->configs[$idx[0]][$idx[1]][$idx[2]]) ? $this->configs[$idx[0]][$idx[1]][$idx[2]] : null;
        } elseif (isset($idx[1])) {
            // 访问第1维
            return isset($this->configs[$idx[0]][$idx[1]]) ? $this->configs[$idx[0]][$idx[1]] : null;
        } elseif (isset($idx[0])) {
            // 访问第0维
            return isset($this->configs[$idx[0]]) ? $this->configs[$idx[0]] : null;
        }
        
        return null;
    }
    
    /**
     * 检查配置选项下标是否合法
     * @param array $arr
     * @throws \Exception
     */
    private function checkKey(array $arr)
    {
        foreach ($arr as $key => $val) {
            // 下标只允许包含字母、数字、下划线
            if (preg_match("/[^a-z0-9_]/i", $key)) {
                throw new \Exception('“' . $key . '”配置下标只允许包含字母、数字、下划线');
            }
            
            if (is_array($val)) {
                $this->checkKey($val);
            }
        }
    }
    
    /**
     * 读取所有的配置信息
     * 可使用引用调用
     *
     * @return array
     */
    public function getAll() 
    {
        return $this->configs;
    }
    
    /**
     * 合并配置
     * @param array $cfgs
     */
    public function merge(array $cfgs) 
    {
        if ($this->env != 'product') {
            $this->checkKey($cfgs);
        }
        
        $this->configs = array_replace_recursive($this->configs, $cfgs);
        
        return $this;
    }
    
    /**
     * 加载配置问的配置信息
     * @param string $name 配置文件名称，配置文件夹中的文件不包括“.php”后缀
     * @param bool $isMerge = true 是否合并到配置中
     * @return array 加载后的数组信息
     * @throws \wf\app\Exception
     */
    public function load($name, $isMerge = true) 
    {
        // 加载默认配置
        $cfgs = array();

        if (file_exists($cfgFile = $this->cfgDir . '/' . $this->env . '/' . $name . '.php')) {
            $cfgs = include $cfgFile;
        } elseif (file_exists($cfgFile = $this->cfgDir . '/' . $name . '.php')) {
            $cfgs = include $cfgFile;
        } else {
            return false;
        }
        
        if($isMerge) {
            $this->merge($cfgs);
        } elseif ($this->env != 'product') {
            $this->checkKey($cfgs);
        }
        
        return $cfgs;
    }
    
    /**
     * 获取配置运行环境参数
     * @return string
     */
    public function getEnv() 
    {
        return $this->env;
    }
}
