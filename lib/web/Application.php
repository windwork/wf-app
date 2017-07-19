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
 * Web应用容器类
 * 
 * @package     wf.app.web
 * @author      cm <cmpan@qq.com>
 * @link        http://docs.windwork.org/manual/wf.app.html
 * @since       0.1.0
 */
final class Application extends \wf\app\ApplicationAbstrct
{

    /**
     * 调度器实例
     * 
     * @var \wf\app\web\Dispatcher $dispatcher
     */
    private $dispatcher;

    /**
     * 取得前端控制器实例，只允许实例化一次
     * 
     * @return \wf\app\web\Application
     */
    public static function app()
    {
        return parent::app();
    }

    /**
     * 获取调度器实例
     * @return \wf\app\web\Dispatcher
     */
    public function getDispatcher()
    {
        if (!$this->dispatcher) {
            $this->dispatcher = new \wf\app\web\Dispatcher($this);
        }

        return $this->dispatcher;
    }

    /**
     * 应用执行入口
     */
    public function run() 
    {
        \wf\app\Benchmark::mark('appRunFore');
        return $this->getDispatcher()->dispatch();
    }

}
