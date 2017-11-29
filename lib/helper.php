<?php
/**
 * Windwork
 * 
 * 一个用于快速开发高并发Web应用的轻量级PHP框架
 * 
 * @copyright Copyright (c) 2008-2017 Windwork Team. (http://www.windwork.org)
 * @license   http://opensource.org/licenses/MIT
 */

/**
 * Windwork开发框架帮助函数，
 * 为了简化Windwork框架组件对象访问，提升开发效率和代码可读性。
 * 
 * @author      cm <cmpan@qq.com>
 * @since       0.1.0
 */

/**
 * 获取应用实例
 * 
 * @return \wf\app\web\Application
 */
function app()
{
    return \wf\app\web\Application::app();
}

/**
 * 使用服务定位器获取服务实例
 * @param string $name
 */
function srv($name)
{
    return app()->getSrv()->get($name);
}

/**
 * 服务定位器注入对象
 * @param string $name
 * @param mixed $definition
 * @param array $params
 * @param bool $share = true
 * @return \wf\app\ServiceLocator
 */
function setSrv($name, $definition, array $params = [], $share = true)
{
    return app()->getSrv()->set($name, $definition, $params, $share);
}

/**
 * 调度器实例（前端控制器）
 * 
 * @return \wf\app\web\Dispatcher
 */
function dsp()
{
    return app()->getDispatcher();
}

/**
 * 获取用户通过web提交数据
 * @param string $key
 * @return mixed
 */
function in($key = null)
{
    return dsp()->getRequest()->getRequest($key);
}

/**
 * 读取配置变量
 * 
 * <pre>
 * // 访问 $this->configs['url']
 * $conf->get('url'); 
 * 
 * // 访问 $this->configs['url']['rewrite']
 * $conf->get('url.rewrite'); 
 * 
 * // 访问 $this->configs['url']['alias']['login']
 * $conf->get('url.alias.login'); 
 * </pre>
 * 
 * @param string $name 配置参数下标，访问多层级数组用.隔开
 * @return mixed
 */
function cfg($name = null)
{
    $cfgObj = app()->getConfig();
    
    if ($name === null) {
        return $cfgObj->getAll();
    }

    return $cfgObj->get($name);
}

/**
 * 获取语言包中的字符串
 * @param string $key
 * @return string
 */
function lang($key)
{
    return app()->getI18n()->get($key);
}

/**
 * 获取缓存组件实例
 *
 * @return \wf\cache\CacheInterface
 */
function wfCache()
{
    return srv('cache');
}

/**
 * 获取验证码组件实例
 *
 * @return \wf\captcha\CaptchaInterface
 */
function wfCaptcha()
{
    return srv('captcha');
}

/**
 * 获取加密解密组件实例
 * @return \wf\crypt\CryptInterface
 */
function wfCrypt()
{
    return srv('crypt');
}

/**
 * 写入日志
 *
 * 可以在config/config.php中启用日志，所有日志按类别保存
 * @param string $level 日志级别 emergency|alert|critical|error|warning|notice|info|debug
 * @param string $message 日志内容，如果是非标量则使用var_export成字符串保存
 */
function logging($level, $message)
{
    return srv('logger')->log($level, $message);
}

/**
 * 获取数据库操作组件实例
 * 
 * @param string $id = 'default'
 * @return \wf\db\DBInterface
 */
function wfDb($id = 'default')
{
    return srv('db.' . $id);
}

/**
 * 获取存贮组件实例
 * 
 * @return \wf\storage\StorageInterface
 */
function wfStorage()
{
    return srv('storage');
}

/**
 * 图片处理组件
 * 
 * @return \wf\image\ImageInterface
 */
function wfImage()
{
    return srv('image');
}

/**
 * 邮件发送组件实例
 * 
 * @return \wf\mailer\MailerInterface
 */
function wfMailer()
{
    return srv('mailer');
}

/**
 * 获取缩略图的URL，一般在模板中使用
 * @param string|ing $path 图片路径或图片附件id
 * @param int $width = 100 为0时按高比例缩放
 * @param int $height = 0 为0时按宽比例缩放
 * @param bool $fullUrl = false 是否强制获取完整路径图片
 * @return string
 */
function thumb($path, $width = 100, $height = 0, $fullUrl = false)
{
    return wfStorage()->getThumbUrl($path, $width, $height, $fullUrl);
}

/**
 * URL to path
 * 根据上传文件的Path获取完整URL
 * @param string $path
 * @return string
 */
function storageUrl($path)
{
    return wfStorage()->getFullUrl($path);
}

/**
 * path to URL
 * 根据上传文件的URL获取path
 * @param string $url
 * @return string
 */
function storagePath($url)
{
    return wfStorage()->getPathFromUrl($url);
}

/**
 * 生成URL
 * 
 * 通过路由实例生成符合路由解析规则的URL
 *
 * @param string $uri
 * @param bool $fullUrl = false 是否获取完整URL
 * @return string
 */
function url($uri, $fullUrl = false)
{
    return dsp()->getRouter()->createUrl($uri, [], $fullUrl);
}

/**
 * 创建记录查询分页导航对象
 * 
 * @param int $totals 总记录数
 * @param int $rows = 10  每页显示记录数
 * @param string $tpl = 'simple' 分页 导航模板，mobile）手机分页, simple）简单分页, complex）复杂分页 
 */
function pager($totals, $rows = 10, $tpl = 'simple')
{
    $pager = new \wf\pager\Pager($totals, $rows, '', ['argSeparator' => '/', 'valSeparator' => ':', 'tpl' => $tpl]);
    return $pager;
}
    
/**
 * 分页获取模型记录列表，包括分页实例
 *
 * @param \wf\model\Model $m
 * @param array $cdt = []
 * @param int $rows = 10 每页记录数
 * @param string $countField = '*' 统计字段
 * @return array (
 *   'list'  => $list,
 *   'total' => $total,
 *   'pages' => $pager->lastPage, // 总页数
 *   'pager' => instance of \wf\pager\Pager(),
 * )
 */
function modelPager(\wf\model\Model $m, $cdt = [], $rows = 10, $countField = '*')
{
    $total = $m->find($cdt)->fetchCount($countField);
    $pager = pager($total, $rows);
    
    $list = $m->find($cdt)->fetchAll($pager->offset, $pager->rows);
    
    return [
        'list'  => $list,
        'total' => $total,
        'pages' => $pager->lastPage, // 总页数
        'pager' => $pager,
    ];
}

/**
 * 分页获取模型记录列表，为JS提供数据
 *
 * @param \wf\model\Model $m
 * @param array $cdt = []
 * @param int $pageSize = 10 每页记录数
 * @param int $page = 当前页码
 * @param string $countField = '*' 统计字段
 * @return array [
 *   'list'  => $list,
 *   'total' => $total,
 *   'pages' => $pages, // 总页数
 *   'page'  => $page // 当前页数
 *   'pageSize' => $pageSize // 每页记录数
 * ]
 */
function modelPagerForJs(\wf\model\Model $m, $cdt = [], $pageSize = 10, $page = 1, $countField = '*')
{
    // 总记录数
    $total = $m->find($cdt)->fetchCount($countField);

    // 总页数
    $pages = ceil($total/$pageSize);

    // 页码检查
    if ($page > $pages) {
        $page = $pages;
    } elseif($page < 1) {
        $page = 1;
    }

    $offset = ($page - 1) * $pageSize;
    $list = $m->find($cdt)->fetchAll($offset, $pageSize);

    return [
        'list'  => $list,
        'total' => (int)$total,
        'pages' => (int)$pages, // 总页数
        'page'  => (int)$page, // 当前页数
        'pageSize' => (int)$pageSize, // 每页记录数
    ];
}

/**
 * 获取会员头像的url，一般在模板中使用
 *
 * @param int $uid
 * @param string $type big|medium|small|tiny
 * @param bool $reload 浏览时是否重新加载头像
 * @return string
 */
function avatar($uid, $type = 'small', $reload = false)
{
    $urlExt = cfg('url.rewriteExt');
    // => "storage/avatar/{$type}/{$uid}.jpg"
    $avatar = url("system.uploader.load/avatar/{$type}/{$uid}.jpg");

    $urlExt && $urlExt != '.jpg' && $avatar = preg_replace("/{$urlExt}$/", '', $avatar);

    if($reload) {
        static $rand = null;
        $rand or $rand = sprintf("%X", mt_rand(0x100000, 0xFFFFFF));

        $avatar .= "?".$rand;
    }

    return $avatar;
}

/**
 * 对请求URL进行解码
 * @param string $str
 * @return string||array
 */
function paramDecode($arg)
{
    if (is_array($arg)) {
        foreach ($arg as $key => $val) {
            $arg[$key] = paramDecode($val);
        }
    } else {
        $arg = urldecode(urldecode($arg));
    }
    return $arg;
}

/**
 * 对请求URL进行编码
 * @param string $arg
 * @return string
 */
function paramEncode($arg)
{
    if (is_array($arg)) {
        foreach ($arg as $key => $val) {
            $arg[$key] = paramEncode($val);
        }
    } else {
        $arg = urlencode(urlencode(paramDecode($arg)));
    }
    return $arg;
}
    
/**
 * 默认异常处理
 *
 * @param Exception $e 异常对象
 */
function exceptionHandler($e)
{
    $code = $e->getCode();
    $message = $e->getMessage();
    $message = "<b style='color:#F00; font-size:14px; line-height:18px;'>{$message}</b>";
    
    $trimPath = \wf\app\Loader::getClassPath();
    $trimPath[] = __WF_BASE_DIR;

    $line = $e->getLine();
    $file = str_replace($trimPath, '', $e->getFile());
    $file = str_replace(DIRECTORY_SEPARATOR, '/', $e->getFile());
    $file = trim($file, '/');
    $trace = str_replace($trimPath, '', $e->getTraceAsString());
    $trace = "<pre class=\"error-trace\">{$trace}</pre>\n";

    if (in_array($code, [401, 403, 404])) {
        app()->getMessage()->setError($message, $code);
        dsp()->dispatch("common.message.show/forward:" . paramEncode(dsp()->getRequest()->getRequestUrl()));
        return ;
    }
    
    if (WF_DEBUG) {
        $message = "<div style=\"color:#666;\">"
                 . "  <b>Exception:</b> ".get_class($e) . "\n<br />"
                 . "  <b>Message:</b> {$message}\n<br />"
                 . "  <b>File:</b> {$file}</b>"
                 . "  <b>Line:</b> {$line}</b>"
                 . "  {$trace}\n"
                 . "</div>";
    }
    
    header('Content-Type: text/html; Charset=utf-8');
    print "<div style=\"border: 1px solid #F90; color:#999; padding: 8px 12px; margin:20px 12px; background:#FFFEEE;\">{$message}</div>\n";
    
    logging('exception', $e->__toString()."\n");
    
    exit;
}

/**
 * 消息对象，支持在多个控制器间（dsp()->dispatch()时）共享消息
 * @return \wf\app\Message
 */
function msg() 
{
    return app()->getMessage();
}

/**
 * 数据表数据查询
 * @param string $table
 * @return \wf\db\Finder
 */
function table($table)
{
    return new \wf\db\Finder(['table' => $table]);
}

/**
 * 检查页面请求令牌
 * @return bool
 */
function checkToken() 
{
    $hash = in('token');
    return \wf\util\Csrf::checkToken($hash);
}

/**
 * 修改二维数组索引，构建新的数组，以数组的某个元素的值作为下标
 *
 * @param array $arr
 * @param string $indexKey 以值作为新数组下标的元素的下标
 * @param string $prefix = '' 下标前缀
 * @return array
 */
function chArrIndex($arr, $indexKey, $prefix = '')
{
    $result = [];

    foreach ($arr as $item) {
        $result[$prefix . $item[$indexKey]] = $item;
    }

    return $result;
}

/**
 * 获取分页查询下标
 *
 * @param int $total
 * @param int $page
 * @param int $rowCount
 * @return int
 */
function getPageOffset($total, $page, $rowCount)
{
    if($total == 0) {
        return 0;
    }

    if($page <= 0) {
        $page = 1;
    } else {
        $maxPage = ceil($total/$rowCount);
        if($page > $maxPage) {
            $page = $maxPage;
        }
    }

    $offset = ($page - 1) * $rowCount;

    return $offset;
}