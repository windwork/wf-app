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
 * 404
 * 
 * @package     wf.app.web
 * @author      cm <cmpan@qq.com>
 * @since       0.1.0
 */
class NotFoundException extends \Exception 
{
    public function __construct ($message = null, $code = 404, $previous = null) 
    {
        $message = $message ? $message : "很不幸，你探索了一个位置领域！";
        parent::__construct ($message, $code, $previous);
    }
}

