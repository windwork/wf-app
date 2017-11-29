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
    private $data = [
        /**
         * 是否是操作成功
         * @var bool
         */
        'success' => true,

        /**
         * 消息状态码
         * @var code
         */
        'code' => 0,

        /**
         * 消息内容
         * @var string
         */
        'message' => '',
    ];

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
        return $this->data['code'];
    }
    
    /**
     * 消息内容
     * @return string
     */
    public function getMessage() 
    {
        return $this->data['message'];
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
            $this->data['code']    = $error->getCode();
            $this->data['message'] = $error->getMessage();
        } elseif (is_scalar($error)) {
            $this->data['code']    = $code;
            $this->data['message'] = $error;
        } else {
            throw new \InvalidArgumentException('错误的消息类型');
        }

        $this->data['success'] = false;
	
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
        $this->data['success'] = true;
        $this->data['code']    = $code;
        $this->data['message'] = $msg;
        
        return $this;
    }
        
    /**
     * 是否成功
     * 
     * @return bool
     */
    public function isSuccess()
    {
        return $this->data['success'];
    }
    
    /**
     * 重置
     * @return \wf\app\Message
     */
    public function reset() 
    {
        $this->data = [
            'success' => true,
            'code'    => 0,
            'message' => '',
        ];
        
        return $this;
    }
    
    /**
     * 天假自定义消息数据
     * @param string $key
     * @param mixed $value
     * @return \wf\app\Message
     */
    public function addData($key, $value)
    {
        $this->data[$key] = $value;
        
        return $this;
    }
    
    /**
     * 对象数据转成数组结构
     * @return string
     */
    public function toArray()
    {
        return $this->data;
    }
}
