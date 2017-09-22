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
 * Web输出帮助类
 * 
 * @package     wf.app.web
 * @author      cm <cmpan@qq.com>
 * @link        http://docs.windwork.org/manual/wf.app.web.output.html
 * @since       0.1.0
 */
class Output {
	
	/**
	 * 输出js内容
	 *
	 * @param string $js js程序代码
	 * @return string
	 */
	public static function jsScript($js) {
		return "<script type='text/javascript'>{$js}</script>\n";
	}
	
	/**
	 * js跳转
	 *
	 * @param string $url
	 * @param bool $waitTime = 0 等待多时秒
	 * @return string
	 */
	public static function jsLocation($url, $waitTime = 0) {
		$url = urldecode(urldecode($url));
		$url = str_replace("'", "\\'", $url);
		
		if($waitTime) {
		    $waitTime = $waitTime * 1000;
		    return static::jsScript("setTimeout(function(){window.location.href='{$url}';}, {$waitTime});");
		} else {
		    return static::jsScript("window.location.href='{$url}'");
		}
	}
	
	/**
	 * 把内容转换成提供js的document.write()使用的字符串
	 * 
	 * @param string $content
	 */
	public static function jsWrite($content) {		
		$search  = array("\r\n", "\n", "\r", "\"", "<script ");
		$replace = array(' ', ' ', ' ', '\"', '<scr"+"ipt ');
        $content = str_replace($search, $replace, $content);
        
		return "document.write(\"{$content}\");\n";
	}

	/**
	 * 生成可回调的json格式数据
	 * - ajax回调）传入ajax_cb URL 请求参数，将返回js代码，并在代码中回调以该参数命名的函数；<br />
	 * - iframe回调）传入if_cb URL 请求参数，将返回HTML代码，并在代码中回调以该参数命名的函数； <br />
	 * - 默认）直接返回json数据
	 * 
	 * @param string $array
	 * @param int $jsonOption = null
	 */
	public static function jsonCallable($array, $jsonOption = null) {
		$json = json_encode($array, $jsonOption);

		$req = dsp()->getRequest();

		header('Content-Type: text/html; charset=UTF-8');
		
		if (false != ($ifCbUrl = $req->getRequest('if_cb_url'))) {
		    $ifCbUrl = paramDecode($ifCbUrl);
		    if (!preg_match("/^(http|https|ftp):\\/\\//i", $ifCbUrl)) {
		        return '';
		    }
		    		    
		    // 允许回调的限制域名限制
		    $allowDomains = cfg('url.ifCbDomains');
		    if ($allowDomains) {
		        $allowCb = false;
		        $ifCbDomain = str_replace("/.*?:\\/\\/(.*?)\\/.*/", "\\1", $ifCbUrl);
		        foreach ($allowDomains as $allowDomain) {
		            if ($allowDomain[0] == '*') {
		                $allowDomain = ltrim($allowDomain, '*'); // .xxx.xx
		                if (substr($ifCbDomain, -strlen($allowDomain)) == $allowDomain) {
		                    $allowCb = true;
		                    break;
		                }
		            } else if ($ifCbDomain == $allowDomain) {
	                    $allowCb = true;
	                    break;		                
		            }
		        }
		        
		        // 不允许回调的域名，直接显示json
		        if (!$allowCb) {
		            return $json; 
		        }
		    }		    
		    
		    $ifCbUrl .= urlencode($json);
		    
		    // 跳转回调
			$json = "<script type=\"text/javascript\">window.location.href='{$ifCbUrl}'</script>";
		} elseif (false != ($iframeCallback = $req->getRequest('if_cb'))) {
			$callback = preg_replace("/[^0-9a-z_\\.]/i", '', $iframeCallback);
			$callback = preg_replace("/^parent\\./", '', $callback);
			$json = "<script type=\"text/javascript\">try{parent.{$callback}({$json});}catch(e){}</script>";
		} elseif (false != ($ajaxCallback = $req->getRequest('ajax_cb')) && $ajaxCallback != '?') {
			$callback = preg_replace("/[^0-9a-z_\\.]/i", '', $ajaxCallback);
		    $json = "try{{$callback}({$json});}catch(e){}";
		}
		
		return $json;
	}
}