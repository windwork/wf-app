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
            @session_destroy();
        }

        $sessionName = $cfg['sessionName'];

        // session运行时设置
        ini_set('session.name',             $sessionName);
        ini_set('session.use_trans_sid',    $cfg['useTransSid']);
        ini_set('session.cache_limiter',    'nocache'); // http 响应头中的 Cache-Control
        ini_set('session.save_handler',     $cfg['saveHandler']);
        ini_set('session.save_path',        $cfg['savePath']);
        ini_set('session.use_cookies',      1);
        ini_set('session.cookie_path',      $cfg['cookiePath']);
        ini_set('session.cookie_domain',    $cfg['cookieDomain']);
        ini_set('session.cookie_lifetime',  $cfg['cookieLifetime']);

        // 支持许通过URL传递session_id，解决客户端不支持cookie的问题（需在配置文件中启用）
        if ($cfg['useTransSid'] && !empty($_REQUEST[$sessionName])) {
            if(!static::setSessionId($_REQUEST[$sessionName])) {
                unset($_GET[$sessionName], $_POST[$sessionName], $_REQUEST[$sessionName]);
            }
        } else if (!empty($_COOKIE[$sessionName]) && !static::setSessionId($_COOKIE[$sessionName])) {
            // session_id为空字符将出现警告信息
            // 因此需要unset COOKIE的session_id，unset后将会自动重新生成session_id
            unset($_COOKIE[$sessionName]);
        }
        
        session_start();
        
        static::$isSessionStarted = true;
    }

    /**
     * 设置session_id，每次请求都设置，以更新session过期时间
     * @param string $sessionId
     * @return bool|string
     */
    private static function setSessionId($sessionId){
        $sessionId = trim($sessionId);

        // 非法字符处理
        if (!$sessionId || preg_match("/[^0-9a-z_\\-\\,]/i", $sessionId)) {
            return false;
        }

        $sessionId = substr($sessionId, 0, 40);
        session_id($sessionId);

        return $sessionId;
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