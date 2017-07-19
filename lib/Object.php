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
 * 属性重载支持类，支持动态增删读属性。
 * 
 * @package     wf.app
 * @author      cm <cmpan@qq.com>
 * @link        http://docs.windwork.org/manual/wf.app.object.html
 * @since       0.1.0
 */
class Object {
    /**
     * 错误信息
     * @var array
     */
    protected $errors = [];
    
    /**
     * 用来保存动态属性
     * @var array
     */
    protected $attrs = [];
    
    /**
     * 获取属性
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name) 
    {
        if (property_exists($this, $name)) {
            $getter = 'get' . ucfirst($name);
            if (method_exists($this, $getter)) {
                return $this->$getter();                
            }
            
            throw new \Exception(get_called_class() . "::{$name} access denied");
        }
        
        $name = strtolower($name);
        if(isset($this->attrs[$name])) {
            return $this->attrs[$name];
        } else {
            return null;
        }
    }
    
    /**
     * 设置属性
     *
     * @param string $name
     * @param mixed $val
     * @return \wf\app\Object
     */
    public function __set($name, $val) 
    {
        if (property_exists($this, $name)) {
            $setter = 'set' . ucfirst($name);
            if (method_exists($this, $setter)) {
                return $this->$setter($val);
            }
            throw new \Exception(get_called_class() . "::{$name} access denied");
        }
        
        $name = strtolower($name);
        $this->attrs[$name] = $val;
        
        return $this;
    }
    
    /**
     * 该属性是否已经设置
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name) 
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter() !== null;
        }
        
        $name = strtolower($name);
        
        return array_key_exists($name, $this->attrs);
    }
    
    /**
     * 释放属性
     *
     * @param string $name 属性名
     */
    public function __unset($name) 
    {
        $setter = 'set' . ucfirst($name);
        if (method_exists($this, $setter)) {
            return $this->$setter(null);
        }
        
        $name = strtolower($name);        
        unset($this->attrs[$name]);
        
        return $this;
    }
    
    /**
     * @param string $name
     * @param mixed $args
     * @throws \BadMethodCallException
     */
    public function __call($name, $args = []) 
    {        
        $message = 'Not exists method called: ' . get_called_class() . '::'.$name.'()';
        throw new \BadMethodCallException($message);        
    }
    
    /**
     * 获取错误信息
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
    
    /**
     * 获取最后一个错误的内容
     *
     * @return string
     */
    public function getLastError()
    {
        $err = end($this->errors);
        reset($this->errors);
        
        return $err;
    }
    
    /**
     * 是否有错误
     *
     * @return bool
     */
    public function hasError()
    {
        return empty($this->errors) ? false : true;
    }
    
    /**
     * 设置错误信息
     *
     * @param string|array $msg
     * @return \wf\app\Object
     */
    public function setError($msg)
    {
        $this->errors = array_merge($this->errors, (array)$msg);
    
        return $this;
    }
    
    /**
     * 重置错误信息
     * @return \wf\app\Object
     */
    public function resetError() {
        $this->errors = [];
        
        return $this;
    }
}
