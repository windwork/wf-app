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
 * 403
 * 
 * @package     wf.app.web
 * @author      cm <cmpan@qq.com>
 * @since       0.1.0
 */
class ForbiddenException extends \Exception {
    public function __construct ($message = null, $code = 403, $previous = null) {
        $message = $message ? $message : "此路不通，请绕行！";
        parent::__construct ($message, $code, $previous);
    }
}

