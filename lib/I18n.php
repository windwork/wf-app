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
 * 国际化支持类 
 * 
 * 在不修改内部代码的情况下，能根据不同语言及地区显示相应的界面。
 * 
 * @package     wf.app
 * @author      cm <cmpan@qq.com>
 * @link        http://docs.windwork.org/manual/wf.app.i18n.html
 * @since       0.1.0
 */
final class I18n 
{
    /**
     * 语言值列表
     * @var array
     */
    private $langs = [];
    
    /**
     * 访客使用的语言
     * @var string
     */
    private $locale = 'zh_CN';
    
    /**
     * 本地化语言文件所在文件夹
     * @var string
     */
    private $dir;
    
    /**
     * 设置本地化语言
     * @param string $locale
     * @return bool
     * @link http://www.icu-project.org/apiref/icu4c/uloc_8h.html#details
     */
    public function setLocale($locale) 
    {
        // 语言包存在则设置为访客使用的语言
        if(is_dir("{$this->dir}/{$locale}")) {
            $this->locale = $locale;
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 获取语言
     * @return string
     */
    public function getLocale() 
    {
        return $this->locale;
    }
    
    /**
     * 加载语言包文件
     * 重复的选项将被新的选项替换
     *
     * @param string $baseName
     */
    public function load($baseName) 
    {     
        $langFile = "{$this->dir}/{$this->locale}/{$baseName}.php";

        if (!is_file($langFile)) {
            return $this->langs;
        }
        
        $langs = include_once $langFile;
        if ($langs && is_array($langs)) {
            $this->langs = array_merge_recursive($this->langs, $langs);
        }
        
        return $this->langs;
    }
    
    /**
     * 获取语言字符串
     *
     * @param string $key
     * @return string
     */
    public function get($key) 
    {
        return isset($this->langs[$key]) ? $this->langs[$key] : $key;
    }
    
    /**
     * 获取所有已定义的语言变量
     *
     * @return array
     */
    public function getLangs() 
    {
        return $this->langs;
    }
    
    /**
     * 设置本地化语言所在文件夹路径
     * @param string $dir
     */
    public function setDir($dir)
    {
        $this->dir = rtrim($dir, '\\/');
        
        return $this;
    }
    
    /**
     * 获取本地化语言文件夹路径
     * @return string
     */
    public function getDir()
    {
        return $this->dir;
    }
}