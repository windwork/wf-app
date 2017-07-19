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
 * 应用程序向客户端响应信息的封装
 *
 * 相应状态码信息描述：
 * 1xx：信息，请求收到，继续处理
 * 2xx：成功，行为被成功地接受、理解和采纳
 * 3xx：重定向，为了完成请求，必须进一步执行的动作
 * 4xx：客户端错误，请求包含语法错误或者请求无法实现
 * 5xx：服务器错误，服务器不能实现一种明显无效的请求
 *
 * @package     wf.app.web
 * @author      cm <cmpan@qq.com>
 * @link        http://docs.windwork.org/manual/wf.app.web.response.html
 * @since       0.1.0
 */
class Response
{
    /**
     * 是否已经发送响应信息，已发送则不重新发送
     * @var bool
     */
    protected $isSentHeader = false;
    
    /**
     * Status code (100)
     * indicating the client can continue.
     * 
     * @var int
     */
    const HTTP_CONTINUE = 100;
    
    /**
     * Status code (101) indicating the server is switching protocols
     * according to Upgrade header.
     * @var int
     */
    const HTTP_SWITCHING_PROTOCOLS = 101;
    
    /**
     * Status code (200)
     * Status code (200) indicating the request succeeded normally.
     *
     * @var int
     */
    const HTTP_OK = 200;
    
    /**
     * Status code (201)
     *
     * Status code (201) indicating the request succeeded and created
     * a new resource on the server.
     *
     * @var int
     */
    const HTTP_CREATED = 201;
    
    /**
     * Status code (202)
     *
     * Status code (202) indicating that a request was accepted for
     * processing, but was not completed.
     *
     * @var int
     */
    const HTTP_ACCEPTED = 202;
    
    /**
     * Status code (203)
     *
     * Status code (203) indicating that the meta information presented
     * by the client did not originate from the server.
     *
     * @var int
     */
    const HTTP_NON_AUTHORITATIVE_INFORMATION = 203;
    
    /**
     * Status code (204)
     *
     * Status code (204) indicating that the request succeeded but that
     * there was no new information to return.
     *
     * @var int
     */
    const HTTP_NO_CONTENT = 204;
    
    /**
     * Status code (205)
     *
     * Status code (205) indicating that the agent <em>SHOULD</em> reset
     * the document view which caused the request to be sent.
     *
     * @var int
     */
    const HTTP_RESET_CONTENT = 205;
    
    /**
     * Status code (206)
     *
     * Status code (206) indicating that the server has fulfilled
     * the partial GET request for the resource.
     *
     * @var int
     */
    const HTTP_PARTIAL_CONTENT = 206;
    
    /**
     * Status code (300)
     *
     * Status code (300) indicating that the requested resource
     * corresponds to any one of a set of representations, each with
     * its own specific location.
     *
     * @var int
     */
    const HTTP_MULTIPLE_CHOICES = 300;
    
    /**
     * Status code (301)
     *
     * Status code (301) indicating that the resource has permanently
     * moved to a new location, and that future references should use a
     * new URI with their requests.
     *
     * @var int
     */
    const HTTP_MOVED_PERMANENTLY = 301;
    
    /**
     * Status code (302)
     *
     * Status code (302) indicating that the resource has temporarily
     * moved to another location, but that future references should
     * still use the original URI to access the resource.
     *
     * This definition is being retained for backwards compatibility.
     * HTTP_FOUND is now the preferred definition.
     *
     * @var int
     */
    const HTTP_MOVED_TEMPORARILY = 302;
    
    /**
     * Status code (302)
     *
     * Status code (302) indicating that the resource reside
     * temporarily under a different URI. Since the redirection might
     * be altered on occasion, the client should continue to use the
     * Request-URI for future requests.(HTTP/1.1) To represent the
     * status code (302), it is recommended to use this variable.
     *
     * @var int
     */
    const HTTP_FOUND = 302;
    
    /**
     * Status code (303)
     *
     * Status code (303) indicating that the response to the request
     * can be found under a different URI.
     *
     * @var int
     */
    const HTTP_SEE_OTHER = 303;
    
    /**
     * Status code (304)
     *
     * Status code (304) indicating that a conditional GET operation
     * found that the resource was available and not modified.
     *
     * @var int
     */
    const HTTP_NOT_MODIFIED = 304;
    
    /**
     * Status code (305)
     *
     * Status code (305) indicating that the requested resource
     * <em>MUST</em> be accessed through the proxy given by the
     * <code><em>Location</em></code> field.
     *
     * @var int
     */
    const HTTP_USE_PROXY = 305;
    
    /**
     * Status code (307)
     *
     * Status code (307) indicating that the requested resource
     * resides temporarily under a different URI. The temporary URI
     * <em>SHOULD</em> be given by the <code><em>Location</em></code>
     * field in the response.
     *
     * @var int
     */
    const HTTP_TEMPORARY_REDIRECT = 307;
    
    /**
     * Status code (400)
     *
     * Status code (400) indicating the request sent by the client was
     * syntactically incorrect.
     *
     * @var int
     */
    const HTTP_BAD_REQUEST = 400;
    
    /**
     * Status code (401)
     *
     * Status code (401) indicating that the request requires HTTP
     * authentication.
     *
     * @var int
     */
    const HTTP_UNAUTHORIZED = 401;
    
    /**
     * Status code (402)
     *
     * Status code (402) reserved for future use.
     *
     * @var int
     */
    const HTTP_PAYMENT_REQUIRED = 402;
    
    /**
     * Status code (403)
     *
     * Status code (403) indicating the server understood the request
     * but refused to fulfill it.
     *
     * @var int
     */
    const HTTP_FORBIDDEN = 403;
    
    /**
     * Status code (404)
     *
     * Status code (404) indicating that the requested resource is not
     * available.
     *
     * @var int
     */
    const HTTP_NOT_FOUND = 404;
    
    /**
     * Status code (405)
     *
     * Status code (405) indicating that the method specified in the
     * <code><em>Request-Line</em></code> is not allowed for the resource
     * identified by the <code><em>Request-URI</em></code>.
     *
     * @var int
     */
    const HTTP_METHOD_NOT_ALLOWED = 405;
    
    /**
     * Status code (406)
     *
     * Status code (406) indicating that the resource identified by the
     * request is only capable of generating response entities which have
     * content characteristics not acceptable according to the accept
     * headers sent in the request.
     *
     * @var int
     */
    const HTTP_NOT_ACCEPTABLE = 406;
    
    /**
     * Status code (407)
     *
     * Status code (407) indicating that the client <em>MUST</em> first
     * authenticate itself with the proxy.
     *
     * @var int
     */
    const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;
    
    /**
     * Status code (408)
     *
     * Status code (408) indicating that the client did not produce a
     * request within the time that the server was prepared to wait.
     *
     * @var int
     */
    const HTTP_REQUEST_TIMEOUT = 408;
    
    /**
     * Status code (409)
     *
     * Status code (409) indicating that the request could not be
     * completed due to a conflict with the current state of the
     * resource.
     *
     * @var int
     */
    const HTTP_CONFLICT = 409;
    
    /**
     * Status code (410)
     *
     * Status code (410) indicating that the resource is no longer
     * available at the server and no forwarding address is known.
     * This condition <em>SHOULD</em> be considered permanent.
     *
     * @var int
     */
    const HTTP_GONE = 410;
    
    /**
     * Status code (411)
     *
     * Status code (411) indicating that the request cannot be handled
     * without a defined <code><em>Content-Length</em></code>.
     *
     * @var int
     */
    const HTTP_LENGTH_REQUIRED = 411;
    
    /**
     * Status code (412)
     *
     * Status code (412) indicating that the precondition given in one
     * or more of the request-header fields evaluated to false when it
     * was tested on the server.
     *
     * @var int
     */
    const HTTP_PRECONDITION_FAILED = 412;
    
    /**
     * Status code (413)
     *
     * Status code (413) indicating that the server is refusing to process
     * the request because the request entity is larger than the server is
     * willing or able to process.
     *
     * @var int
     */
    const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;
    
    /**
     * Status code (414)
     *
     * Status code (414) indicating that the server is refusing to service
     * the request because the <code><em>Request-URI</em></code> is longer
     * than the server is willing to interpret.
     *
     * @var int
     */
    const HTTP_REQUEST_URI_TOO_LONG = 414;
    
    /**
     * Status code (415)
     *
     * Status code (415) indicating that the server is refusing to service
     * the request because the entity of the request is in a format not
     * supported by the requested resource for the requested method.
     *
     * @var int
     */
    const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
    
    /**
     * Status code (416)
     *
     * Status code (416) indicating that the server cannot serve the
     * requested byte range.
     *
     * @var int
     */
    const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    
    /**
     * Status code (417)
     *
     * Status code (417) indicating that the server could not meet the
     * expectation given in the Expect request header.
     *
     * @var int
     */
    const HTTP_EXPECTATION_FAILED = 417;
    
    /**
     * Status code (500)
     *
     * Status code (500) indicating an error inside the HTTP server
     * which prevented it from fulfilling the request.
     *
     * @var int
     */
    const HTTP_INTERNAL_SERVER_ERROR = 500;
    
    /**
     * Status code (501)
     *
     * Status code (501) indicating the HTTP server does not support
     * the functionality needed to fulfill the request.
     *
     * @var int
     */
    const HTTP_NOT_IMPLEMENTED = 501;
    
    /**
     * Status code (502)
     *
     * Status code (502) indicating that the HTTP server received an
     * invalid response from a server it consulted when acting as a
     * proxy or gateway.
     *
     * @var int
     */
    const HTTP_BAD_GATEWAY = 502;
    
    /**
     * Status code (503)
     *
     * Status code (503) indicating that the HTTP server is
     * temporarily overloaded, and unable to handle the request.
     *
     * @var int
     */
    const HTTP_SERVICE_UNAVAILABLE = 503;
    
    /**
     * Status code (504)
     *
     * Status code (504) indicating that the server did not receive
     * a timely response from the upstream server while acting as
     * a gateway or proxy.
     *
     * @var int
     */
    const HTTP_GATEWAY_TIMEOUT = 504;
    
    /**
     * Status code (505)
     *
     * Status code (505) indicating that the server does not support
     * or refuses to support the HTTP protocol version that was used
     * in the request message.
     *
     * @var int
     */
    const HTTP_HTTP_VERSION_NOT_SUPPORTED = 505;
    
    /**
     * 保存模板名字的顺序索引
     *
     * @var array
     */
    protected $bodyIndex = array();
    
    /**
     * 设置输出的头部信息
     *
     * @var array
     */
    private $headers = array();
    
    /**
     * 是否直接跳转
     *
     * @var boolean
     */
    private $isRedirect = false;
    
    /**
     * 设置相应状态码
     *
     * @var string
     */
    private $status = '';
    
    /**
     * 响应内容类型，如果为null则不响应，为 ''则响应text/html;Charset=UTF-8
     *
     * @var string
     */
    private $type = '';
    
    /**
     * 用以保存响应内容
     *
     * @var array
     */
    protected $body = array();
    
    /**
     * 输出数据的保存
     *
     * @var array
     */
    protected $data = array();
    
    public $baseUrl;

    /**
     * 获取当前响应的mime类型
     * @return string
     */
    public function getResponseType()
    {
        return $this->type;
    }

    /**
     * 设置当前请求的返回类型
     * 
     * @param string $responseType
     */
    public function setResponseType($responseType)
    {
        $this->type = $responseType;
    }

    /**
     * 设置响应头信息，如果已经设置过同名的响应头，该方法将用新的设置取代原来的头字段
     * 
     * @param string $name 响应头的名称
     * @param string $value 响应头的字段取值
     * @param int $replace 响应头信息的replace项值
     * @return void
     */
    public function setHeader($name, $value, $replace = false)
    {
        if (!$name || !$value) return;
        $name = $this->normalizeHeaderName($name);
        $setted = false;
        foreach ($this->headers as $key => $one) {
            if ($one['name'] == $name) {
                $this->headers[$key] = array('name' => $name, 'value' => $value, 'replace' => $replace);
                $setted = true;
                break;
            }
        }
        if ($setted === false) {
            $this->headers[] = array('name' => $name, 'value' => $value, 'replace' => $replace);
        }
    }

    /**
     * 设置响应头信息，如果已经设置过同名的响应头，该方法将增加一个同名的响应头
     * 
     * @param string $name 响应头的名称
     * @param string $value 响应头的字段取值
     * @param int $replace 响应头信息的replace项值
     * @return void 
     */
    public function addHeader($name, $value, $replace = false)
    {
        if ($name == '' || $value == '') return;
        $name = $this->normalizeHeaderName($name);
        $this->headers[] = array('name' => $name, 'value' => $value, 'replace' => $replace);
    }

    /**
     * 设置响应头状态码
     * 
     * @param int $status 响应状态码
     * @param string $message  相应状态信息,默认为空字串
     * @return void
     */
    public function setStatus($status, $message = '')
    {
        $status = (int)$status;
        if ($status < 100 || $status > 505) {
            return;
        }
        $this->status = (int) $status;
    }

    /**
     * 设置响应内容
     *
     * @param string $content 响应内容信息
     * @param string $name 相应内容片段名字,默认为null
     * @return void
     */
    public function setBody($content, $name = 'default')
    {
        if (!$content || !$name) return;
        array_push($this->bodyIndex, $name);
        $this->body[$name] = $content;
    }

    /**
     * 重定向到指定网址并exit
     *
     * @param string $location 重定向的地址
     * @param int $status 状态码,默认为302
     * @return void
     */
    public function sendRedirect($location, $status = 302)
    {
        if (!is_int($status) || $status < 300 || $status > 399) {
            return;
        }
        
        if ($location[0] != '/' && !preg_match("/^[0-9a-z]+:\\/\\//i", $location)) {
            $location = $this->baseUrl . $location;
        }
        
        $this->clearHeaders();
        $this->addHeader('Location', $location, true);
        $this->setStatus($status);
        $this->isRedirect = true;
        $this->sendHeaders();
        
        exit();
    }

    /**
     * 发送一个错误的响应信息
     *
     * @param int $status 错误码,默认为404
     * @param string $message 错误信息,默认为空
     * @return void
     */
    public function sendError($status = self::HTTP_NOT_FOUND, $message = '')
    {
        if (!is_int($status) || $status < 400 || $status > 505) {
            return;
        }
        
        $this->setBody($message, 'error');
        $this->setStatus($status);
        $this->send();
    }

    /**
     * 发送响应信息
     *
     * 依次发送响应头和响应内容
     * @return void
     */
    public function send()
    {
        $this->sendHeaders();
        $this->sendBody();
    }

    /**
     * 发送响应头部信息
     *
     * @return void
     */
    public function sendHeaders()
    {
        if ($this->isSentHeader()) {
            return;
        }
        
        foreach ($this->headers as $header) {
            header($header['name'] . ': ' . $header['value'], $header['replace']);
        }
        
        if ($this->status) {
            header('HTTP/1.x ' . $this->status . ' ' . $this->codeMap($this->status));
            header('Status: ' . $this->status . ' ' . $this->codeMap($this->status));
        }
        
        // Content-Type
        if($this->type !== null) {
            $contentType = $this->type;
            if(!$contentType) {
                // 检查Content-Type未发送则设置默认值
                $headerList = headers_list();
                foreach ($headerList as $header) {
                    $pos = stripos(trim($header), 'Content-Type');
                    if ($pos !== false && !$pos) {
                        $contentType = null; // Content-Type已发送设为null则不再发送
                        break;
                    }
                }
                
                // 未发送则设置默认Content-Type
                $contentType === null or $contentType = 'text/html; charset=UTF-8';
            }
            
            $contentType && header('Content-Type: ' . $contentType);
        }
        
        ob_get_level() && ob_flush();
        
        $this->isSentHeader = 1;
    }

    /**
     * 发送响应内容
     *
     * @return void
     */
    public function sendBody()
    {
        foreach ($this->bodyIndex as $key) {
            echo $this->body[$key];
        }
    }
    
    /**
     * 获取响应内容
     *
     * @param string $name 内容的名称,默认为false:
     * <ul>
     * <li>false: 字符串方式返回所有内容</li>
     * <li>true: 返回响应内容的片段数组</li>
     * <li>string类型: 响应内容中该片段的内容<li>
     * <li>other: 返回null</li>
     * </ul>
     * @return mixed
     */
    public function getBody($name = false)
    {
        if ($name === false) {
            ob_start();
            $this->sendBody();
            return ob_get_clean();
        } elseif ($name === true) {
            return $this->body;
        } elseif (is_string($name) && isset($this->body[$name])) {
            return $this->body[$name];
        }
        return null;
    }

    /**
     * 是否已经发送了响应头部
     * 
     * @param bool $throw 是否抛出错误,默认为false
     * <ul>
     * <li>true: 如果已经发送了头部则抛出异常信息</li>
     * <li>false: 无论如何都不抛出异常信息</li>
     * </ul>
     * @return bool 已经发送头部信息则返回true否则返回false
     */
    public function isSentHeader($throw = false)
    {
        $sent = $this->isSentHeader || headers_sent($file, $line);
        if ($throw && $sent) {
            throw new \Exception('The headers were sent in file ' . $file . ' on line ' . $line);
        }
            
        return $sent;
    }

    /**
     * 获取响应头信息
     * 
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * 清理响应体信息
     * 
     * @return void
     */
    public function clearBody()
    {
        $this->body = array();
    }

    /**
     * 清除响应头信息
     * 
     * @return void
     */
    public function clearHeaders()
    {
        $this->headers = array();
    }

    /**
     * 获得保存输出数据
     *
     * @param string $var
     * @return mixed
     */
    public function getData()
    {
        $tmp = $this->data;
        foreach (func_get_args() as $arg) {
            if (is_array($tmp) && isset($tmp[$arg])) {
                $tmp = $tmp[$arg];
            } else {
                return '';
            }
        }
        
        return $tmp;
    }

    /**
     * 设置保存输出数据
     *
     * @param mixed $data 待保存的输出数据
     * @param string $key 输出数据的key名称,默认为空
     * @param bool $merge 是否合并内容
     */
    public function setData($data, $key = '', $merge = false)
    {
        if ($key) {
            if ($merge && !empty($this->data[$key])) {
                $this->data[$key] = array_merge($this->data[$key], (array) $data);
            } else {
                $this->data[$key] = $data;
            }
        } else {
            if (is_object($data)) {
                $data = get_object_vars($data);
            }
            if (is_array($data)) {
                $this->data = array_merge($this->data, $data);
            }
        }
    }
    
    /**
     * 响应状态码对应的响应信息
     * @param int $code
     * @return string
     */
    public function codeMap($code)
    {
        $maps = array(
            100 => 'continue',
            101 => 'witching protocols',

            200 => 'ok',
            201 => 'created',
            202 => 'accepted',
            203 => 'non authoritative information',
            204 => 'no content',
            205 => 'reset content',
            206 => 'partial content',

            300 => 'multiple choices',
            301 => 'moved permanently',
            302 => 'moved temporarily',
            302 => 'found',
            303 => 'see other',
            304 => 'not modified',
            305 => 'use proxy',
            307 => 'temporary redirect',

            400 => 'bad request',
            401 => 'unauthorized',
            402 => 'payment required',
            403 => 'forbidden',
            404 => 'not found',
            405 => 'method not allowed',
            406 => 'not acceptable',
            407 => 'proxy authentication required',
            408 => 'request timeout',
            409 => 'conflict',
            410 => 'gone',
            411 => 'length required',
            412 => 'precondition failed',
            413 => 'request entity too large',
            414 => 'request uri too long',
            415 => 'unsupported media type',
            416 => 'requested range not satisfiable',
            417 => 'expectation failed',

            500 => 'internal server error',
            501 => 'not implemented',
            503 => 'service unavailable',
            503 => 'service unavailable',
            504 => 'gateway timeout',
            505 => 'http version not supported',
        );
        
        return isset($maps[$code]) ? ucwords($maps[$code]) : '';
    }

    /**
     * 格式化响应头信息
     *
     * @param string $name 响应头部名字
     * @return string
     */
    private function normalizeHeaderName($name)
    {
        $name = str_replace(array('-', '_'), ' ', (string) $name);
        $name = ucwords(strtolower($name));
        $name = str_replace(' ', '-', $name);
        return $name;
    }
    
    /**
     * 设置已发送
     * @return \wf\app\web\Response
     */
    public function setSentHeader()
    {
        $this->isSentHeader = true;
        return $this;
    }
}