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
 * 服务定位器
 * 
 * 控制反转（IoC）模式的一种依赖注入容器实现，解决的问题是解耦服务提供者和用户，用户无需直接访问具体的服务提供者类。
 * 
 * 服务定位器是一种反模式，存在这么个问题：我们把对象设置到服务定位器中时，事先没法知
 * 道这个对象提供哪些属性/方法，因此我们在wf/web/lib/helper.php组件中定义一些函数来
 * 访问服务定位器中注册的windwork组件。
 * 
 * @package     wf.app
 * @author      cm <cmpan@qq.com>
 * @link        http://docs.windwork.org/manual/wf.app.serviceloader.html
 * @since       0.1.0
 */
final class ServiceLocator
{
    /**
     * 对象类名
     * @var array
     */
    private $definitions = [];
    
    /**
     * 对象创建参数
     * @var array
     */
    private $params = [];

    /**
     * 已初始化的实例
     * @var array
     */
    private $services = [];

    /**
     * 共享的实例id
     * @var array
     */
    private $shareds = [];

    /**
     * 添加注入类、对象实例、闭包
     *
     * @param string $id
     * @param mixed $definition 注入对象定义，可以是类名（类型为字符串时）、对象实例、闭包函数对象，不允许是函数名，如果要传入函数可使用闭包
     * @param array $params = []
     * @param bool $share = true 是否共享实例，如果不共享，每次获取实例都创建一次。
     * @return \wf\app\ServiceLocator
     */
    public function set($id, $definition, array $params = [], $share = true)
    {        
        // 设置对象实例（非闭包函数）则保存实例
        if (is_object($definition) && !($definition instanceof \Closure)) {
            // object
            $this->services[$id] = $definition;
            $definition = get_class($definition);
        } elseif (!is_string($definition) && !($definition instanceof \Closure)) {
            // 检查字符串（类目）、闭包函数
            // 不是 object/Closure/string 则抛出异常
            throw new \InvalidArgumentException('The $definition argumet type must be "class name/object/Closure", the given is ' . var_export($definition, true));
        }
        
        $this->definitions[$id] = $definition;
        $this->params[$id] = $params;
        $this->shareds[$id] = $share;
        
        return $this;
    }

    /**
     * 服务是否已注入
     * @param string $id
     */
    public function has($id)
    {
        return isset($this->definitions[$id]) || isset($this->services[$id]);
    }

    /**
     * 获取服务实例，如果实实例未创建则创建实例后返回
     * 
     * @param string $id
     *
     * @return object
     */
    public function get($id)
    {
        if (isset($this->services[$id]) && $this->shareds[$id]) {
            return $this->services[$id];
        }
        
        if (!$this->has($id)) {
            throw new Exception('not exists id: ' . $id);
        }

        // 实例创建的参数
        $args = $this->params[$id];
        $definition = $this->definitions[$id];
        
        if ($definition instanceof \Closure || (is_string($definition) && function_exists($definition))) {
            // 执行闭包/函数
            $object = $definition(...$args);
        } elseif (is_string($definition)) {
            // 创建类实例
            $object = new $definition(...$args);
        } else {
            throw new Exception('definition error: ' . $id);
        }
        
        if ($this->shareds[$id]) {
            $this->services[$id] = $object;
        }
        
        return $object;
    }
}
