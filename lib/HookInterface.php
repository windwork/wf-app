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
 * 钩子接口
 * 
 * 所有钩子必须实现该接口，钩子管理器通过调用接口的execute方法执行钩子业务逻辑
 * 
 * @package     wf.app
 * @author      cm <cmpan@qq.com>
 * @link        http://docs.windwork.org/manual/wf.app.hook.html
 * @since       0.1.0
 */
interface HookInterface 
{
    /**
     * 执行Hook
     * @param mixed $params = null
     */
    public function execute($params = null);
}

