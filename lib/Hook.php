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
 * 组件调用机制
 * 
 * 提供一种机制在不需要修改框架代码的情况下来扩展系统。
 * <pre>
 * 1、在config/hooks.php中设置和启用钩子 hook_enabled => 1
 *   配置规则：
 *   方式1：钩子类名或钩子类的实例,如：'\\user\\hook\\Acl', new \app\user\hook\Acl()
 *   方式2：钩子类名或钩子类的实例+数组参数,如：array('\\user\\hook\\Acl', array($param1, $param2, ....)), array(new \app\user\hook\Acl(), array($param1, $param2, ....))
 *   
 * 2、钩子类必须实现 \wf\app\HookInterface接口
 * 
 * 3、钩子在框架中调用的位置有：     
 *   1)appRuntimeAft 初始化框架允许环境后触发的钩子，只执行一次； 
 *   2)dspNewControllerFore 创建控制器实例前触发的钩子
 *   3)dspRunActionFore 执行action前触发的钩子
 *   4)dspOutputFore 内容输出前触发的钩子，可对输出内容进行处理过滤
 *   5)dspResponseAft 程序执行完后触发的钩
 *   
 * 4、你也可以在自己开发的应用中加入钩子调用点
 * </pre>
 * 
 * @package     wf.app
 * @author      cm <cmpan@qq.com>
 * @link        http://docs.windwork.org/manual/wf.app.hook.html
 * @since       0.1.0
 */
final class Hook 
{
    /**
     * 是否启用钩子
     *
     * @var    bool
     */
    public $enabled = false;
    
    /**
     * 钩子注册的配置信息
     *
     * @var    array
     */
    public $hooks = array();
    
    /**
     * 钩子实现类的实例
     *
     * @var array
     */
    protected $instances = array();
    
    /**
     * 初始化钩子 ，设置钩子配置信息
     *
     * @param array $hooksCfg
     * @return \wf\app\Hook
     */
    public function __construct($hooksCfg)
    {
        $this->hooks = $hooksCfg;
        
        if(!empty($hooksCfg['enableHook'])) {
            $this->enabled = true;
        }
        
        return $this;
    }

    /**
     * 获得指定扩展点的全部扩展调用
     *
     * @param string $registerKey
     * @return array
     */
    public function getRegistry($registerKey)
    {
        if (!isset($this->hooks[$registerKey])) {
            return false;
        }
        
        return $this->hooks[$registerKey];
    }

    /**
     * 手动注册钩子
     *
     * @param string $registerKey 钩子位置
     * @param string|object|array $injectHook 注入信息 Hook Class|Hook instance|array('Hook Class', array($param1, $param2, ....)), array(Hook instance, array($param1, $param2, ....))
     * @return void
     */
    public function register($registerKey, $injectHook)
    {
        $this->hooks[$registerKey][] = $injectHook;
    }

    /**
     * 执行指定钩子点的所有方法
     * 
     * @param string $hookId = '' 指定钩子点
     * @param mixed $args = null 传递参数
     * @return bool
     */
    public function call($hookId = '', $args = null)
    {
        if(!$this->enabled || !isset($this->hooks[$hookId])) {
            return false;
        }

        // 执行钩子列表中的钩子
        foreach ($this->hooks[$hookId] as $hook) {
            $this->callDetail($hook, $args);
        }
        
        return true;
    }
    
    /**
     * 执行钩子注入的具体业务
     * @param mixed $hook  Hook Class|Hook instance|array('Hook Class', array($param1, $param2, ....)), array(Hook instance, array($param1, $param2, ....))
     * @param mixed $params = null 传递参数
     * @throws \wf\app\Exception
     * @return void|boolean
     */
    protected function callDetail($hook, $params = null)
    {        
        if (is_object($hook)) {
            // 设置值为实例
            if (!$hook instanceof \wf\app\HookInterface) {
                throw new \DomainException(get_class($hook) . '钩子必须实现\wf\app\HookInterface接口');
            }
            
            $hookObj = $hook;
        } elseif (is_string($hook)) {
            // 设置值为类名
            $hook = '\\' . ltrim($hook, '\\');
            if (isset($this->instances[$hook])) {
                // 已创建类实例
                $hookObj = $this->instances[$hook];
            } else {
                // 未创建类实例
                if(!class_exists($hook)) {
                    throw new \Exception($hook . '钩子类不存在');
                }
                // 类实例
                $hookObj = new $hook();
                
                if (!$hookObj instanceof \wf\app\HookInterface) {
                    throw new \DomainException($hook . '钩子必须实现\wf\app\HookInterface接口');
                }
                
                $this->instances[$hook] = $hookObj;
            }
        } elseif (is_array($hook)) {            
            if (!isset($hook[0])) {
                throw new \Exception($hook . '钩子设置错误');
            }
            
            if (is_object($hook[0])) {
                $hookObj = $hook[0];
                // 设置值为实例
                if (!$hookObj instanceof \wf\app\HookInterface) {
                    throw new \DomainException(get_class($hookObj) . '钩子必须实现\wf\app\HookInterface接口');
                }
            } elseif (is_string($hook[0])) {
                // 设置值为类名
                $hookClass = '\\' . ltrim($hook[0], '\\');
                if (isset($this->instances[$hookClass])) {
                    // 已创建类实例
                    $hookObj = $this->instances[$hookClass];
                } else {
                    // 未创建类实例
                    if(!class_exists($hookClass)) {
                        throw new \Exception($hookClass . '钩子类不存在');
                    }
                    
                    // 类实例
                    $hookObj = new $hookClass();
                    
                    if (!$hookObj instanceof \wf\app\HookInterface) {
                        throw new \DomainException($hookClass . '钩子必须实现\wf\app\HookInterface接口');
                    }
                    
                    $this->instances[$hookClass] = $hookObj;
                }                
            } else {
                throw new \Exception(var_export($hook, 1) . '钩子设置错误');
            }
            
            isset($hook['args']) && $params = $hook['args'];
        }
        
        $hookObj->execute($params);
        
        return true;
    }
}
