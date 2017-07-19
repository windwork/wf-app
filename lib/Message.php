<?php
/**
 * Windwork
 * 
 * 一个用于快速开发高并发Web应用的轻量级PHP框架
 * 
 * @copyright   Copyright (c) 2008-2016 Windwork Team. (http://www.windwork.org)
 * @license   http://opensource.org/licenses/MIT
 */
namespace wf\app;

/**
 * 应用程序内部传递消息类，将在视图中显示 
 * 
 * @package     wf.app
 * @author      erzh <cmpan@qq.com>
 * @link        http://docs.windwork.org/manual/wf.app.message.html
 * @since       0.1.0
 */
class Message 
{
    /**
     * 是否是操作成功
     * @var bool
     */
    private $success = true;
    
    /**
     * 消息状态码
     * @var code
     */
    private $code = 0;
    
    /**
     * 消息内容
     * @var string
     */
    private $message = '';

    /**
     * 数据内容消息
     * @var array
     */
    private $data = [];
    
    /**
     * 默认错误码
     * @var int
     */
    const DEFAULT_ERROR_CODE = 10001;
    
    /**
     * 消息状态码
     * @return int
     */
    public function getCode() 
    {
        return $this->code;
    }
    
    /**
     * 消息内容
     * @return string
     */
    public function getMessage() 
    {
        return $this->message;
    }
    
    /**
     * 获取所有提示信息
     * 
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
    
    /**
     * 设置错误信息
     * 
     * @param string|\wf\model\Error $error
     * @param int $code = 10001  错误码，如果$error参数是\wf\model\Error实例，则忽略此参数
     * @return \wf\app\Message
     */
    public function setError($error, $code = Message::DEFAULT_ERROR_CODE)
    {
        if ($error instanceof \wf\model\Error) {
            $this->code    = $error->getCode();
            $this->message = $error->getMessage();     
        } elseif (is_scalar($error)) {
            $this->code    = $code;
            $this->message = $error;          
        } else {
            throw new \InvalidArgumentException('错误的消息类型');
        }

        $this->success = false;
	
        return $this;
    }
    
    /**
     * 设置”操作成功“消息
     * 
     * @param string|array $msg
     * @return \wf\app\Message
     */
    public function setSuccess($msg, $code = 0)
    {
        $this->success = true;
        $this->code    = $code;
        $this->message = $msg;
        
        return $this;
    }
        
    /**
     * 是否成功
     * 
     * @return bool
     */
    public function isSuccess()
    {
        return $this->success;
    }
    
    /**
     * 重置
     * @return \wf\app\Message
     */
    public function reset() 
    {
        $this->success = true;
        $this->code    = 0;
        $this->message = '';
        $this->data    = [];
        
        return $this;
    }
    
    /**
     * 设置消息数据
     * @param mixed $value
     * @return \wf\app\Message
     */
    public function setData($value)
    {
        $this->data = $value;
        
        return $this;
    }
    
    /**
     * 对象数据转成数组结构
     * @return string
     */
    public function toArray()
    {
        $arr = [
            'success' => $this->success,
            'code'    => $this->code,
            'message' => $this->message,
        ];
        
        if ($this->data) {
            $arr['data'] = $this->data;
        }
        
        return $arr;
    }
}
