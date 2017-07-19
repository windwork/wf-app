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
 * Session支持类
 * 
 * @package     wf.app
 * @author      cm <cmpan@qq.com>
 * @link        http://docs.windwork.org/manual/wf.app.session.html
 * @since       0.1.0
 */
class Session 
{
    /**
     * 是否已启用session
     * @var bool
     */
    private static $isSessionStarted = false;
    
    /**
     * 启用session
     * @param array $cfg
     * @throws \RuntimeException
     */
    public static function start(array $cfg)
    {        
        // 只启动一次
        if (static::$isSessionStarted) {
            return;
        }
        
        // session 状态
        $status = session_status();
        
        if($status == PHP_SESSION_DISABLED) {
            // session模块被禁用
            throw new \RuntimeException('当前PHP引擎不支持session！');
        }
        
        if ($status == PHP_SESSION_ACTIVE) {
            // 清空启用session.auto_start自动开启时初始化的数据
            $_SESSION = [];
            session_destroy();
        }
        
        // session运行时设置
        ini_set('session.name',             'WF_SID');
        ini_set('session.use_trans_sid',    $cfg['useTransSid']);
        ini_set('session.cache_limiter',    'nocache'); // http 响应头中的 Cache-Control
        ini_set('session.save_handler',     $cfg['saveHandler']);
        ini_set('session.save_path',        $cfg['savePath']);
        ini_set('session.use_cookies',      1);
        ini_set('session.cookie_path',      $cfg['cookiePath']);
        ini_set('session.cookie_domain',    $cfg['cookieDomain']);
        ini_set('session.cookie_lifetime',  $cfg['cookieLifetime']);
        
        /*
        - 关于防 session_id注入：
        - session_id为空字符或包含字母、数字、-、_之外的字符时将出现警告信息，因此需要处理。
        */
                
        // 提供允许通过URL传递session_id的支持，解决客户端不支持cookie的问题
        if ($cfg['useTransSid'] && !empty($_GET[session_name()]) && $_GET[session_name()] != session_id()) {
            $sessionId = $_GET[session_name()];
            $sessionId = preg_replace("/[^0-9a-z_\\-\\,]/i", '', $sessionId);
            $sessionId = substr(trim($sessionId), 0, 40);            
            session_id($sessionId);
        } else if (isset($_COOKIE[session_name()])) {
            // $_COOKIE传入session_id合法性检查
            $sessionId = $_COOKIE[session_name()];
            if ($sessionId && preg_match("/[^0-9a-z_\\-\\,]/i", $sessionId)) {
                // 非法字符处理
                $sessionId = preg_replace("/[^0-9a-z_\\-\\,]/i", '', $sessionId);
                $sessionId = substr(trim($sessionId), 0, 40);
                
                if($sessionId) {
                    session_id($sessionId);
                }
            }
            
            if(!$sessionId) {
                // session_id为空字符将出现警告信息
                // 因此需要删除COOKIE传入的session_id，系统将会自动重新生成session_id
                unset($_COOKIE[session_name()]);
            }
        }
        
        session_start();
        
        static::$isSessionStarted = true;
    }
    
    /**
     * 清除session，用户退出后调用
     */
    public static function destroy() {
        session_destroy();
        $_SESSION = [];
        setcookie(session_name(), null, 1, ini_get('session.cookie_path'), ini_get('session.cookie_domain'));
    }
}